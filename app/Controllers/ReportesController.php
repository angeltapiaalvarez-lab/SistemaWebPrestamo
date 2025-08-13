<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\ClientesModel;
use App\Models\PrestamosModel;

use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportesController extends BaseController
{
    private $prestamos, $empresa, $session, $clientes;
    public function __construct()
    {
        $this->prestamos = new PrestamosModel();
        $this->empresa = new AdminModel();
        $this->clientes = new ClientesModel();
        $this->session = session();
        helper(['fecha']);
    }
    public function historial()
    {
        if (!verificar('pdf prestamos', $this->session->permisos) && !verificar('excel prestamos', $this->session->permisos)) {
            return view('permisos');
        }
        $fechaFin = $this->request->getGet('fecha_fin');
        $fechaInicio = $this->request->getGet('fecha_inicio');
        if (empty($fechaInicio) || empty($fechaFin)) {
            $fechaFin = date('Y-m-d');
            $fechaInicio = date('Y-m-d', strtotime('-30 days'));
        }
        $data['fecha_inicio'] = $fechaInicio;
        $data['fecha_fin'] = $fechaFin;
        $data['active'] = 'reportesHistorial';
        $data['prestamos'] = [];
        $data['mensaje'] = '';
        if ($fechaInicio > $fechaFin) {
            $data['mensaje'] = 'Rango de fechas inválido.';
        } else {
            $data['prestamos'] = $this->filtroReportes($fechaInicio, $fechaFin);
            if (empty($data['prestamos'])) {
                $data['mensaje'] = 'No hay datos para el rango seleccionado.';
            } else {
                for ($i = 0; $i < count($data['prestamos']); $i++) {
                    $result = $this->clientes->select('nombre, apellido')->where('id', $data['prestamos'][$i]['id_cliente'])->first();
                    $data['prestamos'][$i]['cliente'] = $result['nombre'] . ' ' . $result['apellido'];
                }
            }
        }
        return view('reportes/historial', $data);
    }
    public function reportesPdf()
    {
        if (!verificar('pdf prestamos', $this->session->permisos)) {
            return view('permisos');
        }
        $fechaInicio = $this->request->getGet('fecha_inicio');
        $fechaFin = $this->request->getGet('fecha_fin');
        if ($fechaInicio > $fechaFin) {
            return redirect()->to(base_url('reportes/historial'))->with('respuesta', [
                'type' => 'warning',
                'msg' => 'Rango de fechas inválido'
            ]);
        }
        $data['prestamos'] = $this->filtroReportes($fechaInicio, $fechaFin);
        if (empty($data['prestamos'])) {
            return redirect()->to(base_url('reportes/historial'))->with('respuesta', [
                'type' => 'warning',
                'msg' => 'No hay datos para el rango seleccionado'
            ]);
        }
        $data['titulo'] = 'Historial de préstamos';

        for ($i = 0; $i < count($data['prestamos']); $i++) {
            $result = $this->clientes->select('nombre, apellido')->where('id', $data['prestamos'][$i]['id_cliente'])->first();
            $data['prestamos'][$i]['cliente'] = $result['nombre'] . ' ' . $result['apellido'];
        }
        $data['empresa'] = $this->empresa->first();
        $data['usuario'] = $this->session->nombre;
        $data['generado'] = date('Y-m-d H:i:s');
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        ob_start();
        echo view('reportes/prestamos', $data);
        $html = ob_get_clean();

        $options = $dompdf->getOptions();
        $options->set('isJavascriptEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'vertical');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $nombre = 'historial_prestamos_' . date('Ymd') . '.pdf';
        $dompdf->stream($nombre, ['Attachment' => false]);
    }

    public function reportesExcel()
    {
        if (!verificar('excel prestamos', $this->session->permisos)) {
            return view('permisos');
        }

        $fechaInicio = $this->request->getGet('fecha_inicio');
        $fechaFin = $this->request->getGet('fecha_fin');
        if ($fechaInicio > $fechaFin) {
            return redirect()->to(base_url('reportes/historial'))->with('respuesta', [
                'type' => 'warning',
                'msg' => 'Rango de fechas inválido'
            ]);
        }
        $results = $this->filtroReportes($fechaInicio, $fechaFin);
        if (empty($results)) {
            return redirect()->to(base_url('reportes/historial'))->with('respuesta', [
                'type' => 'warning',
                'msg' => 'No hay datos para el rango seleccionado'
            ]);
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('Angel')->setTitle('Prestamos');

        $spreadsheet->setActiveSheetIndex(0);

        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF0000');

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->getColumnDimension('A')->setWidth('50');
        $hojaActiva->getColumnDimension('B')->setWidth('15');
        $hojaActiva->getColumnDimension('C')->setWidth('20');
        $hojaActiva->getColumnDimension('D')->setWidth('20');
        $hojaActiva->getColumnDimension('E')->setWidth('40');


        $hojaActiva->setCellValue('A1', 'CLIENTE');
        $hojaActiva->setCellValue('B1', 'IMPORTE');
        $hojaActiva->setCellValue('C1', 'MODALIDAD');
        $hojaActiva->setCellValue('D1', 'TASA INTERES');
        $hojaActiva->setCellValue('E1', 'F. VENCIMIENTO');

        $fila = 2;
        $total = 0;
        foreach ($results as $prestamo) {
            $result = $this->clientes->select('nombre, apellido')->where('id', $prestamo['id_cliente'])->first();
            $hojaActiva->setCellValue('A' . $fila, $result['nombre'] . ' ' . $result['apellido']);
            $hojaActiva->setCellValue('B' . $fila, $prestamo['importe']);
            $hojaActiva->setCellValue('C' . $fila, $prestamo['modalidad']);
            $hojaActiva->setCellValue('D' . $fila, $prestamo['tasa_interes']);
            $hojaActiva->setCellValue('E' . $fila, fechaPerzo($prestamo['fecha_venc']));
            $total += $prestamo['importe'];
            $fila++;
        }
        $hojaActiva->setCellValue('A' . $fila, 'Total');
        $hojaActiva->setCellValue('B' . $fila, $total);
        $fila += 2;
        $hojaActiva->setCellValue('A' . $fila, 'Generado por: ' . $this->session->nombre);
        $hojaActiva->setCellValue('B' . $fila, 'Fecha: ' . date('Y-m-d H:i:s'));

        header('Content-Type: application/vnd.ms-excel');
        $nombre = 'historial_prestamos_' . date('Ymd') . '.xls';
        header('Content-Disposition: attachment;filename="' . $nombre . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');
    }

    public function filtroReportes($fechaInicio = null, $fechaFin = null) {
        $id_usuario = $this->session->id_usuario;
        $builder = $this->prestamos->where('id_usuario', $id_usuario)->where('estado', 1);
        if ($fechaInicio && $fechaFin) {
            $builder->where('fecha >=', $fechaInicio)->where('fecha <=', $fechaFin);
        }
        return $builder->findAll();
    }
}
