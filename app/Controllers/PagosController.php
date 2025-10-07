<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PagosModel;
use App\Models\DetPrestamoModel;
use App\Models\PrestamosModel;
use App\Models\AdminModel;
// reference the Dompdf namespace
use Dompdf\Dompdf;

class PagosController extends BaseController
{
    private $pagos, $detalle, $prestamos, $empresa, $session;

    public function __construct()
    {
        helper(['fecha']);
        $this->pagos = new PagosModel();
        $this->detalle = new DetPrestamoModel();
        $this->prestamos = new PrestamosModel();
        $this->empresa = new AdminModel();
        $this->session = session();
    }

    public function index()
    {
        if (!verificar('historial pagos', $this->session->permisos)) {
            return view('permisos');
        }
        $data['active'] = 'pago';
        return view('pagos/index', $data);
    }

    public function listar()
    {
        if ($this->request->is('get')) {
            $data = $this->pagos
                ->select('pagos.*, p.id AS prestamo, p.moneda, d.cuota, CONCAT(u.nombre, " ", u.apellido) AS usuario')
                ->join('detalle_prestamos AS d', 'pagos.id_detalle_prestamo = d.id')
                ->join('prestamos AS p', 'd.id_prestamo = p.id')
                ->join('usuarios AS u', 'pagos.id_usuario = u.id')
                ->orderBy('pagos.fecha_pago', 'DESC')
                ->findAll();

            $data = array_map(static function ($pago) {
                $moneda = $pago['moneda'] ?? 'NIO';
                $pago['moneda_label'] = currency_name($moneda) . ' (' . $moneda . ')';
                $pago['monto_formateado'] = format_currency($pago['monto'], $moneda);
                return $pago;
            }, $data);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die();
        }
    }

    public function recibo($id)
    {
        if (!verificar('abono prestamo', $this->session->permisos)) {
            return view('permisos');
        }

        $data['pago'] = $this->pagos
            ->select('pagos.*, d.cuota, d.id_prestamo, p.id AS prestamo, p.moneda, c.identidad, c.num_identidad, c.nombre AS cliente, c.apellido, c.telefono, c.direccion')
            ->join('detalle_prestamos AS d', 'pagos.id_detalle_prestamo = d.id')
            ->join('prestamos AS p', 'd.id_prestamo = p.id')
            ->join('clientes AS c', 'p.id_cliente = c.id')
            ->where('pagos.id', $id)
            ->first();

        $data['empresa'] = $this->empresa->first();

        $dompdf = new Dompdf();
        ob_start();
        echo view('pagos/recibo', $data);
        $html = ob_get_clean();

        $options = $dompdf->getOptions();
        $options->set('isJavascriptEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'vertical');
        $this->response->setHeader('Content-Type', 'application/pdf');
        $dompdf->render();
        $nombre = 'recibo_pago_' . $id . '_' . date('Ymd') . '.pdf';
        $dompdf->stream($nombre, ['Attachment' => false]);
    }
}
