<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientesModel;

class ClientesController extends BaseController
{
    private $clientes;
    public function __construct()
    {
        $this->clientes = new ClientesModel();
        helper(['form']);
    }
    public function index()
    {
        return view('clientes/index');
    }

    public function listar()
    {
        $data = $this->clientes
            ->select('clientes.*, IF(p.id IS NULL, 0, 1) AS prestamo_activo')
            ->join('prestamos AS p', 'p.id_cliente = clientes.id AND p.estado = 1', 'left')
            ->findAll();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function new()
    {
        return view('clientes/nuevo');
    }

    public function create()
    {
        if ($this->request->is('post')){
            $data = [
                'id_cliente' => $this->request->getVar('id_cliente'),
                'identidad' => $this->request->getVar('identidad'),
                'num_identidad' => $this->request->getVar('num_identidad'),
                'nombre' => $this->request->getVar('nombre'),
                'apellido' => $this->request->getVar('apellido'),
                'telefono' => $this->request->getVar('telefono'),
                'whatsapp' => $this->request->getVar('whatsapp'),
                'correo' => $this->request->getVar('correo'),
                'direccion' => $this->request->getVar('direccion')
            ];
            if ($this->clientes->insert($data) === false) {
                $data['errors'] = $this->clientes->errors();
                return view('clientes/nuevo', $data);
            }
        }
        return redirect()->to(base_url('clientes'))->with('respuesta', [
            'type' => 'success',
            'msg' => 'CLIENTE REGISTRADO',
        ]);      
    }

    public function edit($idCliente){
        $data['cliente'] = $this->clientes->where('id', $idCliente)->first();
        return view('clientes/edit', $data);
    }

    public function update($idCliente)
    {
        if ($this->request->is('put')){
            $data = [
                'id_cliente' => $this->request->getVar('id_cliente'),
                'identidad' => $this->request->getVar('identidad'),
                'num_identidad' => $this->request->getVar('num_identidad'),
                'nombre' => $this->request->getVar('nombre'),
                'apellido' => $this->request->getVar('apellido'),
                'telefono' => $this->request->getVar('telefono'),
                'whatsapp' => $this->request->getVar('whatsapp'),
                'correo' => $this->request->getVar('correo'),
                'direccion' => $this->request->getVar('direccion')
            ];
            if ($this->clientes->update($idCliente, $data) === false) {
                $data['errors'] = $this->clientes->errors();
                $data['cliente'] = $this->clientes->where('id', $idCliente)->first();
                return view('clientes/edit', $data);
            }
        }
        return redirect()->to(base_url('clientes'))->with('respuesta', [
            'type' => 'success',
            'msg' => 'CLIENTE MODIFICADO',
        ]);      
    }

    public function delete($idCliente) {
        if ($this->request->is('delete')) {
            //$data = $this->usuarios->delete($idUsuario);
            $data = $this->clientes->update($idCliente, ['estado' => '0']);
            if ($data) {
                return redirect()->to(base_url('clientes'))->with('respuesta', [
                    'type' => 'success',
                    'msg' => 'CLIENTE DADO DE BAJA',
                ]);
            } else {
                return redirect()->to(base_url('clientes'))->with('respuesta', [
                    'type' => 'danger',
                    'msg' => 'ERROR AL ELIMINAR',
                ]);
            }

        }
    }

    public function estado($idCliente)
    {
        if ($this->request->is('put')) {
            $estado = $this->request->getVar('estado') == '1' ? '1' : '0';
            $mensaje = $estado === '1' ? 'CLIENTE ACTIVADO' : 'CLIENTE DADO DE BAJA';
            $data = $this->clientes->update($idCliente, ['estado' => $estado]);
            if ($data) {
                return redirect()->to(base_url('clientes'))->with('respuesta', [
                    'type' => 'success',
                    'msg' => $mensaje,
                ]);
            } else {
                return redirect()->to(base_url('clientes'))->with('respuesta', [
                    'type' => 'danger',
                    'msg' => 'ERROR AL CAMBIAR ESTADO',
                ]);
            }
        }
    }
}
