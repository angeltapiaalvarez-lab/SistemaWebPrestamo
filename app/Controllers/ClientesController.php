<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientesModel;
use App\Models\TransaccionesModel;
use App\Models\PrestamosModel;

class ClientesController extends BaseController
{
    private $clientes, $transacciones, $prestamos, $session;
    public function __construct()
    {
        $this->clientes       = new ClientesModel();
        $this->transacciones  = new TransaccionesModel();
        $this->prestamos      = new PrestamosModel();
        helper(['form']);
        $this->session = session();
    }
    public function index()
    {
        if (!verificar('listar clientes', $this->session->permisos)) {
            return view('permisos');
        }
        $data['active'] = 'cliente';
        return view('clientes/index', $data);
    }

    public function listar()
    {
        $data = $this->clientes->findAll();
        foreach ($data as $key => $cliente) {
            $tieneActivo = $this->prestamos
                ->where('id_cliente', $cliente['id'])
                ->where('estado', '1')
                ->countAllResults();
            $data[$key]['prestamo_activo'] = $tieneActivo > 0 ? 'SI' : 'NO';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function new()
    {
        if (!verificar('nuevo cliente', $this->session->permisos)) {
            return view('permisos');
        }
        $data['active'] = 'cliente';
        return view('clientes/nuevo', $data);
    }

    public function create()
    {
        if ($this->request->is('post') && verificar('nuevo cliente', $this->session->permisos)){
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
                $data['active'] = 'cliente';
                return view('clientes/nuevo', $data);
            }
            return redirect()->to(base_url('clientes'))->with('respuesta', [
                'type' => 'success',
                'msg' => 'CLIENTE REGISTRADO',
            ]);
        }else{
            return view('permisos');
        }
              
    }

    public function edit($idCliente){
        if (!verificar('editar cliente', $this->session->permisos)) {
            return view('permisos');
        }
        $data['cliente'] = $this->clientes->where('id', $idCliente)->first();
        $data['active'] = 'cliente';
        return view('clientes/edit', $data);
    }

    public function update($idCliente)
    {
        if ($this->request->is('put') && verificar('editar cliente', $this->session->permisos)){
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
                $data['active'] = 'cliente';
                return view('clientes/edit', $data);
            }
            $this->transacciones->insert([
                'accion'      => 'EDITAR',
                'descripcion' => 'Cliente ID ' . $idCliente,
                'id_usuario'  => $this->session->id_usuario,
            ]);
            return redirect()->to(base_url('clientes'))->with('respuesta', [
                'type' => 'success',
                'msg' => 'CLIENTE MODIFICADO',
            ]);
        }else{
            return view('permisos');
        }
             
    }

    public function delete($idCliente) {
        if ($this->request->is('delete') && verificar('eliminar cliente', $this->session->permisos)) {
            $cliente = $this->clientes->where('id', $idCliente)->first();
            if (!$cliente) {
                return redirect()->to(base_url('clientes'))->with('respuesta', [
                    'type' => 'danger',
                    'msg' => 'CLIENTE NO ENCONTRADO',
                ]);
            }
            $nuevoEstado = ($cliente['estado'] == 1) ? 0 : 1;
            $data = $this->clientes->update($idCliente, ['estado' => $nuevoEstado]);
            if ($data) {
                $accion = ($nuevoEstado == 1) ? 'ACTIVAR' : 'ELIMINAR';
                $mensaje = ($nuevoEstado == 1) ? 'CLIENTE ACTIVADO' : 'CLIENTE DADO DE BAJA';
                $this->transacciones->insert([
                    'accion'      => $accion,
                    'descripcion' => 'Cliente ID ' . $idCliente,
                    'id_usuario'  => $this->session->id_usuario,
                ]);
                return redirect()->to(base_url('clientes'))->with('respuesta', [
                    'type' => 'success',
                    'msg' => $mensaje,
                ]);
            } else {
                return redirect()->to(base_url('clientes'))->with('respuesta', [
                    'type' => 'danger',
                    'msg' => 'ERROR AL ACTUALIZAR',
                ]);
            }
            
        }else{
            return view('permisos');
        }
    }
}
