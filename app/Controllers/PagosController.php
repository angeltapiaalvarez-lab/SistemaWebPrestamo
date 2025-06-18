<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PagosModel;
use App\Models\DetPrestamoModel;
use App\Models\PrestamosModel;

class PagosController extends BaseController
{
    private $pagos, $detalle, $prestamos, $session;

    public function __construct()
    {
        $this->pagos = new PagosModel();
        $this->detalle = new DetPrestamoModel();
        $this->prestamos = new PrestamosModel();
        $this->session = session();
    }

    public function index()
    {
        if (!verificar('abono prestamo', $this->session->permisos)) {
            return view('permisos');
        }
        $data['active'] = 'pago';
        return view('pagos/index', $data);
    }

    public function listar()
    {
        if ($this->request->is('get')) {
            $data = $this->pagos
                ->select('pagos.*, p.id AS prestamo, d.cuota')
                ->join('detalle_prestamos AS d', 'pagos.id_detalle_prestamo = d.id')
                ->join('prestamos AS p', 'd.id_prestamo = p.id')
                ->orderBy('pagos.fecha_pago', 'DESC')
                ->findAll();
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
}
