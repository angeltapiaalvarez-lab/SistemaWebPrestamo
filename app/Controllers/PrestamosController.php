<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\CajasModel;
use App\Models\ClientesModel;
use App\Models\DetPrestamoModel;
use App\Models\PrestamosModel;
use App\Models\PagosModel;
use App\Models\TransaccionesModel;

// reference the Dompdf namespace
use Dompdf\Dompdf;

class PrestamosController extends BaseController
{
    private $empresa, $clientes, $prestamos,
        $detalle, $session, $reglas, $cajas, $pagos, $transacciones;
    public function __construct()
    {
        helper(['form', 'fecha', 'email']);
        $this->empresa = new AdminModel();
        $this->clientes = new ClientesModel();
        $this->prestamos = new PrestamosModel();
        $this->detalle = new DetPrestamoModel();
        $this->cajas = new CajasModel();
        $this->pagos = new PagosModel();
        $this->transacciones = new \App\Models\TransaccionesModel();
        $this->session = session();
    }

    public function index()
    {
        if (!verificar('nuevo prestamo', $this->session->permisos)) {
            return view('permisos');
        }
        $data['empresa'] = $this->empresa->first();
        $data['active'] = 'prestamo';
        return view('prestamos/nuevo', $data);
    }

    public function buscarCliente()
    {
        if ($this->request->is('get') && !empty($this->request->getVar('term'))) {
            $data = $this->clientes
                ->groupStart()
                ->like('num_identidad', $this->request->getVar('term'))
                ->orLike('nombre', $this->request->getVar('term'))
                ->orLike('apellido', $this->request->getVar('term'))
                ->groupEnd()
                ->where('estado', '1')->findAll(10);
            $result = array();
            foreach ($data as $cliente) {
                $datos['id'] = $cliente['id'];
                $datos['value'] = $cliente['num_identidad'] . ' - ' . $cliente['nombre'] . ' ' . $cliente['apellido'];
                array_push($result, $datos);
            }
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            die();
        }
    }

    public function create()
    {
        if ($this->request->is('post') && verificar('nuevo prestamo', $this->session->permisos)) {
            $fecha = date('Y-m-d');
            //calcular vencimiento
            if ($this->request->getVar('modalidad') === 'QUINCENAL') {
                $fecha_venc = date('Y-m-d', strtotime($fecha . '+15 days'));
            } else if ($this->request->getVar('modalidad') === 'MENSUAL') {
                $fecha_venc = date('Y-m-d', strtotime($fecha . '+30 days'));
            } else {
                $fecha_venc = date('Y-m-d', strtotime($fecha . '+15 days'));
            }
            $data = [
                'cliente' => $this->request->getVar('cliente'),
                'importe' => $this->request->getVar('importe_credito'),
                'modalidad' => $this->request->getVar('modalidad'),
                'tasa_interes' => $this->request->getVar('tasa_interes'),
                'cuotas' => $this->request->getVar('cuotas'),
                'fecha' => date('Y-m-d H:i:s'),
                'fecha_venc' => $fecha_venc,
                'estado' => '1',
                'id_cliente' => $this->request->getVar('id_cliente'),
                'id_usuario' => $this->session->id_usuario
            ];
            //verificar cliente
            $sqlCliente = $this->prestamos->where([
                'id_cliente' => $this->request->getVar('id_cliente'),
                'estado' => '1',
            ])->first();

            $verificarSaldo = $this->cajas->calcularMovimientos($this->session->id_usuario);
            if ($verificarSaldo['saldo'] >= $this->request->getVar('importe_credito')) {
                if (empty($sqlCliente)) {
                    if ($this->prestamos->insert($data) === false) {
                        $data['errors'] = $this->prestamos->errors();
                        $data['empresa'] = $this->empresa->first();
                        $data['modalidad'] = $this->request->getVar('modalidad');
                        $data['cuotas'] = $this->request->getVar('cuotas');
                        $data['active'] = 'prestamo';
                        return view('prestamos/nuevo', $data);
                    }
                    $prestamo = $this->prestamos->getInsertID();
                    if ($prestamo > 0) {
                        //calcular ganancia
                        $ganancia = $this->request->getVar('importe_credito')
                            * ($this->request->getVar('tasa_interes') / 100);
                        //calcular importe cuota
                        $importe_cuota = ($this->request->getVar('importe_credito')
                            / $this->request->getVar('cuotas'))
                            + ($ganancia / $this->request->getVar('cuotas'));

                        for ($i = 1; $i <= $this->request->getVar('cuotas'); $i++) {
                            $presDetalle = $this->detalle->insert([
                                'cuota' => $i,
                                'fecha_venc' => $fecha_venc,
                                'importe_cuota' => $importe_cuota,
                                'id_prestamo' => $prestamo,
                                'estado' => '1',
                            ]);
                            //consulta de vencimiento
                            $consulta = $this->detalle->where('id', $presDetalle)->first();
                            //calcular vencimiento
                            if ($this->request->getVar('modalidad') === 'QUINCENAL') {
                                $fecha_venc = date('Y-m-d', strtotime($consulta['fecha_venc'] . '+15 days'));
                            } else if ($this->request->getVar('modalidad') === 'MENSUAL') {
                                $fecha_venc = date('Y-m-d', strtotime($consulta['fecha_venc'] . '+30 days'));
                            } else {
                                $fecha_venc = date('Y-m-d', strtotime($consulta['fecha_venc'] . '+15 days'));
                            }
                        }

                        $this->transacciones->insert([
                            'accion'      => 'CREAR',
                            'descripcion' => 'Prestamo ID ' . $prestamo,
                            'id_usuario'  => $this->session->id_usuario,
                        ]);

                        return redirect()->to(base_url('prestamos/' . $prestamo . '/detail'))->with('respuesta', [
                            'type' => 'success',
                            'msg' => '',
                            'title' => '¡Préstamo registrado!',
                        ]);
                    } else {
                        return redirect()->to(base_url('prestamos'))->with('respuesta', [
                            'type' => 'warning',
                            'msg' => 'ERROR AL REALIZAR PRESTAMO',
                        ]);
                    }
                } else {
                    return redirect()->to(base_url('prestamos'))->with('respuesta', [
                        'type' => 'warning',
                        'msg' => 'YA TIENES UN PRESTAMO PENDIENTE',
                    ]);
                }
            } else {
                return redirect()->to(base_url('prestamos'))->with('respuesta', [
                    'type' => 'warning',
                    'msg' => 'SALDO INSUFICIENTE',
                ]);
            }
        }else{
            return view('permisos');
        }
    }

    public function detail($id)
    {
        if (!verificar('ver prestamo', $this->session->permisos)) {
            return view('permisos');
        }
        $data['prestamo'] = $this->prestamos
            ->select('prestamos.*, c.identidad, c.num_identidad, c.nombre AS cliente, c.apellido, c.telefono, c.whatsapp, c.correo, u.nombre AS usuario, u.apellido AS user_apellido')
            ->join('clientes AS c', 'prestamos.id_cliente = c.id')
            ->join('usuarios AS u', 'prestamos.id_usuario = u.id')
            ->where('prestamos.id', $id)->first();

        $data['detalles'] = $this->detalle
            ->select('detalle_prestamos.*, COALESCE(SUM(pagos.monto),0) AS pagado')
            ->join('pagos', 'pagos.id_detalle_prestamo = detalle_prestamos.id', 'left')
            ->where('detalle_prestamos.id_prestamo', $id)
            ->groupBy('detalle_prestamos.id')
            ->findAll();

        $pendienteTotal = 0;
        foreach ($data['detalles'] as $detalle) {
            $pendienteTotal += ($detalle['importe_cuota'] - $detalle['pagado']);
        }
        $data['pendiente_total'] = $pendienteTotal;
        $data['pagos'] = $this->pagos
            ->select('pagos.*, d.cuota')
            ->join('detalle_prestamos AS d', 'pagos.id_detalle_prestamo = d.id')
            ->where('d.id_prestamo', $id)
            ->orderBy('pagos.fecha_pago', 'ASC')
            ->findAll();
        $data['active'] = 'prestamo';
        return view('prestamos/detail', $data);
    }

    public function reporte($id)
    {
        if (!verificar('ver prestamo', $this->session->permisos)) {
            return view('permisos');
        }
        $data['prestamo'] = $this->prestamos
            ->select('prestamos.*, c.identidad, c.num_identidad, c.nombre AS cliente, c.apellido, c.telefono, c.whatsapp, c.correo, c.direccion, u.nombre AS usuario, u.apellido AS user_apellido')
            ->join('clientes AS c', 'prestamos.id_cliente = c.id')
            ->join('usuarios AS u', 'prestamos.id_usuario = u.id')
            ->where('prestamos.id', $id)->first();

        $data['detalles'] = $this->detalle->where('id_prestamo', $id)->findAll();
        $data['empresa'] = $this->empresa->first();
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        ob_start();
        echo view('prestamos/contrato', $data);
        $html = ob_get_clean();

        $options = $dompdf->getOptions();
        $options->set('isJavascriptEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'vertical');
        $this->response->setHeader('Content-Type', 'application/pdf');
        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $nombre = 'contrato_prestamo_' . $id . '_' . date('Ymd') . '.pdf';
        $dompdf->stream($nombre, ['Attachment' => false]);
    }

    public function update($id)
    {
        if ($this->request->is('put') && verificar('abono prestamo', $this->session->permisos)) {
            $consulta = $this->detalle
                ->select('detalle_prestamos.*, p.modalidad')
                ->join('prestamos AS p', 'detalle_prestamos.id_prestamo = p.id')
                ->where('detalle_prestamos.id', $id)->first();

            if (empty($consulta)) {
                return redirect()->back();
            }

            // Validar que no existan cuotas anteriores pendientes de pago
            $anterior = $this->detalle
                ->where('id_prestamo', $consulta['id_prestamo'])
                ->where('cuota <', $consulta['cuota'])
                ->where('estado', '1')
                ->first();

            if (!empty($anterior)) {
                return redirect()->to(base_url('prestamos/' . $consulta['id_prestamo'] . '/detail'))
                    ->with('respuesta', [
                        'type' => 'warning',
                        'msg'  => 'Tiene una cuota anterior por pagar',
                        'title' => 'Aviso',
                    ]);
            }

            $monto  = (float)$this->request->getVar('monto');
            $metodo = $this->request->getVar('metodo');

            if ($monto <= 0) {
                return redirect()->back();
            }

            $restante       = $monto;
            $idPagoPrimero  = null;
            $prestamoId     = $consulta['id_prestamo'];
            $msg            = 'PAGO REGISTRADO';

            $cuotasPendientes = $this->detalle
                ->where('id_prestamo', $prestamoId)
                ->where('estado', '1')
                ->orderBy('cuota', 'ASC')
                ->findAll();
            $cuotasInfo = [];
            $totalPendiente = 0;
            foreach ($cuotasPendientes as $cuota) {
                $pagado = $this->pagos->selectSum('monto')
                    ->where('id_detalle_prestamo', $cuota['id'])
                    ->first();
                $pagado = $pagado['monto'] ?? 0;
                $cuota['por_pagar'] = $cuota['importe_cuota'] - $pagado;
                $totalPendiente += $cuota['por_pagar'];
                $cuotasInfo[] = $cuota;
            }

            if ($monto > $totalPendiente) {
                return redirect()->back()->with('respuesta', [
                    'type'  => 'warning',
                    'msg'   => 'El monto supera el total del préstamo pendiente',
                    'title' => 'Aviso',
                ]);
            }

            foreach ($cuotasInfo as $cuota) {
                if ($cuota['cuota'] < $consulta['cuota']) {
                    continue;
                }
                if ($restante <= 0) {
                    break;
                }

                $porPagar = $cuota['por_pagar'];
                if ($porPagar <= 0) {
                    continue;
                }

                $abono = ($restante >= $porPagar) ? $porPagar : $restante;

                $idPago = $this->pagos->insert([
                    'id_detalle_prestamo' => $cuota['id'],
                    'monto'               => $abono,
                    'fecha_pago'          => date('Y-m-d H:i:s'),
                    'metodo'              => $metodo,
                    'id_usuario'          => $this->session->id_usuario,
                ]);

                if ($idPago && $idPagoPrimero === null) {
                    $idPagoPrimero = $idPago;
                }

                if ($idPago) {
                    $descripcion = 'Prestamo ID ' . $prestamoId . ', Cuota ' . $cuota['cuota'] . ', Pago ID ' . $idPago;
                    $this->transacciones->insert([
                        'accion'      => 'PAGO',
                        'descripcion' => $descripcion,
                        'id_usuario'  => $this->session->id_usuario,
                    ]);
                }

                $restante -= $abono;

                if ($abono >= $porPagar) {
                    $this->detalle->update($cuota['id'], ['estado' => '0']);
                }
            }

            // Enviar recibo por correo solo del primer pago registrado
            if ($idPagoPrimero) {
                $pagoData = $this->pagos
                    ->select(
                        'pagos.*, d.cuota, d.id_prestamo, p.id AS prestamo, c.identidad, c.num_identidad, c.nombre AS cliente, c.apellido, c.telefono, c.direccion, c.correo'
                    )
                    ->join('detalle_prestamos AS d', 'pagos.id_detalle_prestamo = d.id')
                    ->join('prestamos AS p', 'd.id_prestamo = p.id')
                    ->join('clientes AS c', 'p.id_cliente = c.id')
                    ->where('pagos.id', $idPagoPrimero)
                    ->first();

                $empresa = $this->empresa->first();

                if (!empty($pagoData) && !empty($pagoData['correo'])) {
                    $dompdf = new \Dompdf\Dompdf();
                    ob_start();
                    echo view('pagos/recibo', ['pago' => $pagoData, 'empresa' => $empresa]);
                    $html = ob_get_clean();

                    $options = $dompdf->getOptions();
                    $options->set('isJavascriptEnabled', true);
                    $options->set('isRemoteEnabled', true);
                    $dompdf->setOptions($options);

                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'vertical');
                    $dompdf->render();

                    $dirRecibos = WRITEPATH . 'recibos';
                    if (!is_dir($dirRecibos)) {
                        mkdir($dirRecibos, 0777, true);
                    }
                    $nombreArchivo = $dirRecibos . DIRECTORY_SEPARATOR . 'recibo_pago_' . $idPagoPrimero . '.pdf';
                    file_put_contents($nombreArchivo, $dompdf->output());

                    $body = '<p>Adjunto encontrá el recibo de su pago.</p>';
                    sendEmail(
                        $pagoData['correo'],
                        $pagoData['cliente'],
                        'Recibo de pago',
                        $body,
                        $empresa['correo'],
                        $empresa['nombre'],
                        [['path' => $nombreArchivo, 'name' => 'recibo_pago_' . $idPagoPrimero . '.pdf']]
                    );

                    @unlink($nombreArchivo);
                }
            }

            $proximo = $this->detalle
                ->where('id_prestamo', $prestamoId)
                ->where('estado', '1')
                ->orderBy('cuota', 'ASC')
                ->first();

            if (!empty($proximo)) {
                $this->prestamos->update($prestamoId, ['fecha_venc' => $proximo['fecha_venc']]);
                $msg = 'Ha realizado el pago';
            } else {
                $this->prestamos->update($prestamoId, ['estado' => '2']);
                $msg = 'PRESTAMO FINALIZADO';
            }

            return redirect()->to(base_url('prestamos/' . $prestamoId . '/detail'))
                ->with('respuesta', [
                    'type' => 'success',
                    'msg'  => $msg,
                    'title' => '¡Pago realizado!',
                ])
                ->with('id_pago', $idPagoPrimero);
        } else {
            return view('permisos');
        }
    }

    public function enviarCorreo()
    {
        $this->reglas = [
            'correo' => [
                'rules' => 'required|valid_email'
            ],
            'mensaje' => [
                'rules' => 'required'
            ]
        ];
        if ($this->request->is('post') && $this->validate($this->reglas)) {
            $correo = $this->request->getVar('correo');
                $empresa = $this->empresa->first();
                $cliente = $this->clientes->where('correo', $correo)->first();

                if (sendEmail(
                    $correo,
                    $cliente['nombre'],
                    'Contrato de prestamo - ' . $empresa['nombre'],
                    $this->request->getVar('mensaje'),
                    $empresa['correo'],
                    $empresa['nombre']
                )) {
                    return redirect()->to(base_url('prestamos/' . $this->request->getVar('id_prestamo') . '/detail'))->with('respuesta', [
                        'type' => 'success',
                        'msg' => 'CORREO ENVIADO',
                        'title' => '¡Correo enviado!',
                    ]);
                }

                return redirect()->to(base_url('prestamos/' . $this->request->getVar('id_prestamo') . '/detail'))->with('respuesta', [
                    'type' => 'danger',
                    'msg' => 'ERROR AL ENVIAR CORREO',
                    'title' => 'Error',
                ]);
        } else {
            $data['validator'] = $this->validator;

            $data['prestamo'] = $this->prestamos
                ->select('prestamos.*, c.identidad, c.num_identidad, c.nombre AS cliente, c.apellido, c.telefono, c.whatsapp, c.correo, u.nombre AS usuario, u.apellido AS user_apellido')
                ->join('clientes AS c', 'prestamos.id_cliente = c.id')
                ->join('usuarios AS u', 'prestamos.id_usuario = u.id')
                ->where('prestamos.id', $this->request->getVar('id_prestamo'))->first();

            $data['detalles'] = $this->detalle->where('id_prestamo', $this->request->getVar('id_prestamo'))->findAll();
            $data['active'] = 'prestamo';
            return view('prestamos/detail', $data);
        }
    }

    public function historial()
    {
        if (!verificar('historial prestamos', $this->session->permisos)) {
            return view('permisos');
        }
        $data['active'] = 'prestamo';
        return view('prestamos/historial', $data);
    }

    public function listHistorial()
    {
        if ($this->request->is('get')) {
            $data = $this->prestamos->select('prestamos.*, c.identidad, c.num_identidad, c.nombre, c.apellido, u.nombre AS usuario')
                //->from('prestamos AS p', true)
                ->join('clientes AS c', 'prestamos.id_cliente = c.id')
                ->join('usuarios AS u', 'prestamos.id_usuario = u.id')
                ->where('prestamos.estado != 0')->findAll();
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['vencimiento'] = fechaPerzo($data[$i]['fecha_venc']);
                $ganancia = $this->detalle->selectSum('importe_cuota')->where([
                    'estado' => '0',
                    'id_prestamo' => $data[$i]['id']
                ])->first();
                $data[$i]['ganancia'] = ($ganancia['importe_cuota'] != null) ? number_format($ganancia['importe_cuota'] - $data[$i]['importe'], 2) : '-' . number_format($data[$i]['importe'], 2);
                $data[$i]['gd'] = ($ganancia['importe_cuota'] != null) ? $ganancia['importe_cuota'] - $data[$i]['importe'] : '-' . $data[$i]['importe'];
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die();
        }
    }

    public function delete($id)
    {
        if ($this->request->is('delete') && verificar('eliminar prestamo', $this->session->permisos)) {
            //$data = $this->prestamos->delete($id);
            $data = $this->prestamos->update($id, ['estado' => '0']);
            if ($data) {
                $this->transacciones->insert([
                    'accion'      => 'ELIMINAR',
                    'descripcion' => 'Prestamo ID ' . $id,
                    'id_usuario'  => $this->session->id_usuario,
                ]);
                return redirect()->to(base_url('prestamos/historial'))->with('respuesta', [
                    'type' => 'success',
                    'msg' => 'PRESTAMO ELIMINADO',
                ]);
            } else {
                return redirect()->to(base_url('prestamos/historial'))->with('respuesta', [
                    'type' => 'danger',
                    'msg' => 'ERROR AL ELIMINAR',
                ]);
            }
        }else{
            return view('permisos');
        }
    }
}
