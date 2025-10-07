<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransaccionesModel;

class TransaccionesController extends BaseController
{
    private $transacciones, $session;

    public function __construct()
    {
        $this->transacciones = new TransaccionesModel();
        $this->session = session();
    }

    public function index()
    {
        if (!verificar('historial transacciones', $this->session->permisos)) {
            return view('permisos');
        }
        $data['active'] = 'transaccion';
        return view('transacciones/index', $data);
    }

    public function listar()
    {
        if ($this->request->is('get')) {
            $data = $this->transacciones
                ->select('transacciones.*, CONCAT(u.nombre, " ", u.apellido) AS usuario')
                ->join('usuarios AS u', 'transacciones.id_usuario = u.id')
                ->orderBy('transacciones.created_at', 'DESC')
                ->findAll();
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
}
