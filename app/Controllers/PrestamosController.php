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
use DateInterval;
use DateTimeImmutable;
use Dompdf\Dompdf;

class PrestamosController extends BaseController
{
    private $empresa, $clientes, $prestamos,
        $detalle, $session, $reglas, $cajas, $pagos, $transacciones;
    public function __construct()
    {
        helper(['form', 'fecha', 'email', 'prestamo']);
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
            $moneda = strtoupper($this->request->getVar('moneda'));
            if (!array_key_exists($moneda, currency_options())) {
                $moneda = 'NIO';
            }
            $fechaPrestamo = $this->obtenerFechaPrestamo();
            $modalidad = (string) $this->request->getVar('modalidad');
            $diaReferencia = (int) $fechaPrestamo->format('d');
            $fechaVencimientoInicial = $this->calcularFechaVencimientoInicial($fechaPrestamo, $modalidad, $diaReferencia);
            $data = [
                'cliente' => $this->request->getVar('cliente'),
                'importe' => $this->request->getVar('importe_credito'),
                'moneda' => $moneda,
                'modalidad' => $modalidad,
                'tasa_interes' => $this->request->getVar('tasa_interes'),
                'cuotas' => $this->request->getVar('cuotas'),
                'fecha' => $fechaPrestamo->setTime((int) date('H'), (int) date('i'), (int) date('s'))->format('Y-m-d H:i:s'),
                'fecha_venc' => $fechaVencimientoInicial->format('Y-m-d'),
                'estado' => '1',
                'id_cliente' => $this->request->getVar('id_cliente'),
                'id_usuario' => $this->session->id_usuario
            ];
            //verificar cliente
            $sqlCliente = $this->prestamos->where([
                'id_cliente' => $this->request->getVar('id_cliente'),
                'estado' => '1',
            ])->first();

            $verificarSaldo = $this->cajas->calcularMovimientos($this->session->id_usuario, $moneda);
            if ($verificarSaldo['saldo'] >= $this->request->getVar('importe_credito')) {
                if (empty($sqlCliente)) {
                    if ($this->prestamos->insert($data) === false) {
                        $data['errors'] = $this->prestamos->errors();
                        $data['empresa'] = $this->empresa->first();
                        $data['modalidad'] = $this->request->getVar('modalidad');
                        $data['cuotas'] = $this->request->getVar('cuotas');
                        $data['moneda'] = $moneda;
                        $data['active'] = 'prestamo';
                        return view('prestamos/nuevo', $data);
                    }
                    $prestamo = $this->prestamos->getInsertID();
                    if ($prestamo > 0) {
                        $importeCredito = (float) $this->request->getVar('importe_credito');
                        $totalCuotas = (int) $this->request->getVar('cuotas');
                        $tasaPeriodo = ((float) $this->request->getVar('tasa_interes')) / 100;

                        $tablaAmortizacion = generarTablaAmortizacionFrancesa(
                            $importeCredito,
                            $tasaPeriodo,
                            $totalCuotas
                        );

                        $fechasCuotas = $this->generarFechasCuotas(
                            $fechaVencimientoInicial,
                            $modalidad,
                            $totalCuotas,
                            $diaReferencia
                        );

                        foreach ($tablaAmortizacion['tabla'] as $indice => $detalleCuota) {
                            $fechaCuota = $fechasCuotas[$indice] ?? $fechaVencimientoInicial;
                            $this->detalle->insert([
                                'cuota' => (int) $detalleCuota['cuota'],
                                'fecha_venc' => $fechaCuota->format('Y-m-d'),
                                'importe_cuota' => (float) $detalleCuota['pago'],
                                'id_prestamo' => $prestamo,
                                'estado' => '1',
                            ]);
                        }

                        $this->transacciones->insert([
                            'accion'      => 'CREAR',
                            'descripcion' => 'Prestamo ID ' . $prestamo . ' (' . $moneda . ')',
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
                    'msg' => 'SALDO INSUFICIENTE EN ' . currency_name($moneda),
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

        $monedaPrestamo = $data['prestamo']['moneda'] ?? 'NIO';
        $data['moneda_simbolo'] = currency_symbol($monedaPrestamo);
        $data['moneda_nombre'] = currency_name($monedaPrestamo);

        $detalles = $this->detalle
            ->where('id_prestamo', $id)
            ->orderBy('cuota', 'ASC')
            ->findAll();

        $amortizacionData = $this->obtenerAmortizacionDetallada($data['prestamo'], $detalles);

        $data['detalles'] = $amortizacionData['detalles'];
        $data['amortizacion'] = $amortizacionData['amortizacion'];
        $totalCuotasProgramado = $amortizacionData['total_programado'];
        $data['total_interes_programado'] = $amortizacionData['total_interes'];
        $data['pagos'] = $this->pagos
            ->select('pagos.*, d.cuota, p.moneda AS moneda')
            ->join('detalle_prestamos AS d', 'pagos.id_detalle_prestamo = d.id')
            ->join('prestamos AS p', 'd.id_prestamo = p.id')
            ->where('d.id_prestamo', $id)
            ->orderBy('pagos.fecha_pago', 'ASC')
            ->findAll();

        $data['pagos'] = array_map(static function ($pago) use ($monedaPrestamo) {
            $moneda = $pago['moneda'] ?? $monedaPrestamo;
            $pago['monto_formateado'] = format_currency($pago['monto'], $moneda);
            return $pago;
        }, $data['pagos']);

        $totalCuotas = $totalCuotasProgramado;

        $totalPagado = $this->pagos
            ->selectSum('monto')
            ->join('detalle_prestamos AS d', 'pagos.id_detalle_prestamo = d.id')
            ->where('d.id_prestamo', $id)
            ->first();

        $pagado = isset($totalPagado['monto']) ? (float) $totalPagado['monto'] : 0.0;

        $data['total_programado'] = $totalCuotasProgramado;
        $data['total_pagado'] = $pagado;
        $data['total_restante'] = max(0, $totalCuotasProgramado - $pagado);
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

        $monedaPrestamo = $data['prestamo']['moneda'] ?? 'NIO';
        $data['moneda_simbolo'] = currency_symbol($monedaPrestamo);
        $data['moneda_nombre'] = currency_name($monedaPrestamo);

        $detallesReporte = $this->detalle
            ->where('id_prestamo', $id)
            ->orderBy('cuota', 'ASC')
            ->findAll();

        $amortizacionReporte = $this->obtenerAmortizacionDetallada($data['prestamo'], $detallesReporte);
        $data['detalles'] = $amortizacionReporte['detalles'];
        $data['amortizacion'] = $amortizacionReporte['amortizacion'];
        $data['total_programado'] = $amortizacionReporte['total_programado'];
        $data['total_interes_programado'] = $amortizacionReporte['total_interes'];

        $totalPagadoReporte = $this->pagos
            ->selectSum('monto')
            ->join('detalle_prestamos AS d', 'pagos.id_detalle_prestamo = d.id')
            ->where('d.id_prestamo', $id)
            ->first();

        $pagadoReporte = isset($totalPagadoReporte['monto']) ? (float) $totalPagadoReporte['monto'] : 0.0;
        $data['total_pagado'] = $pagadoReporte;
        $data['total_restante'] = max(0, $amortizacionReporte['total_programado'] - $pagadoReporte);
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

            // El monto del abono se toma del valor de la cuota para evitar modificaciones manuales
            $monto = $consulta['importe_cuota'];
            $metodo = $this->request->getVar('metodo');

            $idPago = $this->pagos->insert([
                'id_detalle_prestamo' => $id,
                'monto'               => $monto,
                'fecha_pago'          => date('Y-m-d H:i:s'),
                'metodo'              => $metodo,
                'id_usuario'          => $this->session->id_usuario,
            ]);

            // Enviar recibo por correo si el pago se registró correctamente
            if ($idPago) {
                // Datos del pago para generar el recibo
                $pagoData = $this->pagos
                    ->select(
                        'pagos.*, d.cuota, d.id_prestamo, p.id AS prestamo, c.identidad, c.num_identidad, c.nombre AS cliente, c.apellido, c.telefono, c.direccion, c.correo'
                    )
                    ->join('detalle_prestamos AS d', 'pagos.id_detalle_prestamo = d.id')
                    ->join('prestamos AS p', 'd.id_prestamo = p.id')
                    ->join('clientes AS c', 'p.id_cliente = c.id')
                    ->where('pagos.id', $idPago)
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

                    // Guardar archivo temporalmente
                    $dirRecibos = WRITEPATH . 'recibos';
                    if (!is_dir($dirRecibos)) {
                        mkdir($dirRecibos, 0777, true);
                    }
                    $nombreArchivo = $dirRecibos . DIRECTORY_SEPARATOR . 'recibo_pago_' . $idPago . '.pdf';
                    file_put_contents($nombreArchivo, $dompdf->output());

                    $body = '<p>Adjunto encontrá el recibo de su pago.</p>';
                    sendEmail(
                        $pagoData['correo'],
                        $pagoData['cliente'],
                        'Recibo de pago',
                        $body,
                        $empresa['correo'],
                        $empresa['nombre'],
                        [['path' => $nombreArchivo, 'name' => 'recibo_pago_' . $idPago . '.pdf']]
                    );

                    @unlink($nombreArchivo);
                }
            }

            if ($idPago) {
                $descripcion = 'Prestamo ID ' . $consulta['id_prestamo'] . ', Cuota ' . $consulta['cuota'] . ', Pago ID ' . $idPago;
                $this->transacciones->insert([
                    'accion'      => 'PAGO',
                    'descripcion' => $descripcion,
                    'id_usuario'  => $this->session->id_usuario,
                ]);
            }

            $pagado = $this->pagos->selectSum('monto')
                ->where('id_detalle_prestamo', $id)->first();

            $msg = 'PAGO REGISTRADO';

            if ($pagado['monto'] >= $consulta['importe_cuota'] && $consulta['estado'] == 1) {
                $this->detalle->update($id, ['estado' => '0']);

                if ($consulta['modalidad'] === 'QUINCENAL') {
                    $fecha_venc = date('Y-m-d', strtotime($consulta['fecha_venc'] . '+15 days'));
                } else if ($consulta['modalidad'] === 'MENSUAL') {
                    $fecha_venc = date('Y-m-d', strtotime($consulta['fecha_venc'] . '+30 days'));
                } else {
                    $fecha_venc = date('Y-m-d', strtotime($consulta['fecha_venc'] . '+15 days'));
                }

                $datos = $this->detalle->where([
                    'id_prestamo' => $consulta['id_prestamo'],
                    'estado' => '1'
                ])->first();

                if (!empty($datos)) {
                    $this->prestamos->update($consulta['id_prestamo'], ['fecha_venc' => $fecha_venc]);
                    $msg = 'Ha realizado el pago';
                } else {
                    $this->prestamos->update($consulta['id_prestamo'], ['estado' => '2']);
                    $msg = 'PRESTAMO FINALIZADO';
                }
            }

            return redirect()->to(base_url('prestamos/' . $consulta['id_prestamo'] . '/detail'))
                ->with('respuesta', [
                    'type' => 'success',
                    'msg'  => $msg,
                    'title' => '¡Pago realizado!',
                ])
                ->with('id_pago', $idPago);
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
            foreach ($data as $index => $row) {
                $data[$index]['vencimiento'] = fechaPerzo($row['fecha_venc']);
                $ganancia = $this->detalle->selectSum('importe_cuota')->where([
                    'estado' => '0',
                    'id_prestamo' => $row['id']
                ])->first();
                $gananciaValor = ($ganancia['importe_cuota'] != null)
                    ? (float) $ganancia['importe_cuota'] - (float) $row['importe']
                    : - (float) $row['importe'];
                $data[$index]['ganancia'] = format_currency($gananciaValor, $row['moneda']);
                $data[$index]['gd'] = $gananciaValor;
                $data[$index]['importe_formateado'] = format_currency($row['importe'], $row['moneda']);
                $data[$index]['moneda_label'] = currency_name($row['moneda']) . ' (' . $row['moneda'] . ')';
                $data[$index]['simbolo'] = currency_symbol($row['moneda']);
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die();
        }
    }

    private function obtenerAmortizacionDetallada(array $prestamo, array $detalles): array
    {
        $importePrestamo = (float) ($prestamo['importe'] ?? 0);
        $totalCuotasPrestamo = (int) ($prestamo['cuotas'] ?? 0);
        $tasaPeriodoPrestamo = ((float) ($prestamo['tasa_interes'] ?? 0)) / 100;

        $tablaAmortizacion = generarTablaAmortizacionFrancesa(
            $importePrestamo,
            $tasaPeriodoPrestamo,
            $totalCuotasPrestamo
        );

        $mapaAmortizacion = [];
        foreach ($tablaAmortizacion['tabla'] as $detalleAmortizacion) {
            $mapaAmortizacion[(int) $detalleAmortizacion['cuota']] = $detalleAmortizacion;
        }

        foreach ($detalles as $index => $detalle) {
            $numeroCuota = (int) ($detalle['cuota'] ?? 0);
            if (isset($mapaAmortizacion[$numeroCuota])) {
                $pagoProgramado = (float) ($mapaAmortizacion[$numeroCuota]['pago'] ?? 0);
                if (abs(((float) ($detalle['importe_cuota'] ?? 0)) - $pagoProgramado) >= 0.01) {
                    $this->detalle->update($detalle['id'], ['importe_cuota' => $pagoProgramado]);
                    $detalles[$index]['importe_cuota'] = $pagoProgramado;
                }
                $detalles[$index]['desglose'] = $mapaAmortizacion[$numeroCuota];
            }
        }

        $totalProgramado = array_reduce(
            $tablaAmortizacion['tabla'],
            static function ($carry, $detalleAmortizacion) {
                return $carry + (float) ($detalleAmortizacion['pago'] ?? 0);
            },
            0.0
        );

        $totalInteres = array_reduce(
            $tablaAmortizacion['tabla'],
            static function ($carry, $detalleAmortizacion) {
                return $carry + (float) ($detalleAmortizacion['interes'] ?? 0);
            },
            0.0
        );

        return [
            'detalles' => $detalles,
            'amortizacion' => $tablaAmortizacion['tabla'],
            'total_programado' => $totalProgramado,
            'total_interes' => $totalInteres,
        ];
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

    private function obtenerFechaPrestamo(): DateTimeImmutable
    {
        $fechaFormulario = $this->request->getVar('fecha');

        if (!empty($fechaFormulario)) {
            $fecha = DateTimeImmutable::createFromFormat('Y-m-d', $fechaFormulario);
            if ($fecha instanceof DateTimeImmutable) {
                return $fecha;
            }
        }

        return new DateTimeImmutable(date('Y-m-d'));
    }

    private function calcularFechaVencimientoInicial(DateTimeImmutable $fechaPrestamo, string $modalidad, int $diaReferencia): DateTimeImmutable
    {
        if ($modalidad === 'MENSUAL') {
            return $this->sumarMesManteniendoDia($fechaPrestamo, $diaReferencia);
        }

        if ($modalidad === 'QUINCENAL') {
            return $fechaPrestamo->add(new DateInterval('P15D'));
        }

        return $fechaPrestamo->add(new DateInterval('P15D'));
    }

    /**
     * @return DateTimeImmutable[]
     */
    private function generarFechasCuotas(DateTimeImmutable $fechaInicial, string $modalidad, int $totalCuotas, int $diaReferencia): array
    {
        $fechas = [];
        $fechaCuota = $fechaInicial;

        for ($numero = 1; $numero <= $totalCuotas; $numero++) {
            $fechas[] = $fechaCuota;

            if ($modalidad === 'MENSUAL') {
                $fechaCuota = $this->sumarMesManteniendoDia($fechaCuota, $diaReferencia);
            } else {
                $fechaCuota = $fechaCuota->add(new DateInterval('P15D'));
            }
        }

        return $fechas;
    }

    private function sumarMesManteniendoDia(DateTimeImmutable $fechaBase, int $diaReferencia): DateTimeImmutable
    {
        $fechaPrimerDia = $fechaBase->setDate(
            (int) $fechaBase->format('Y'),
            (int) $fechaBase->format('m'),
            1
        );

        $fechaProximoMes = $fechaPrimerDia->add(new DateInterval('P1M'));
        $ultimoDiaMes = (int) $fechaProximoMes->format('t');
        $diaFinal = min($diaReferencia, $ultimoDiaMes);

        return $fechaProximoMes->setDate(
            (int) $fechaProximoMes->format('Y'),
            (int) $fechaProximoMes->format('m'),
            $diaFinal
        );
    }
}
