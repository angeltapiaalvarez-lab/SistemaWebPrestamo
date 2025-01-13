<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RolesModel;
use App\Models\UsuariosModel;

class UsuariosController extends BaseController
{
    private $usuarios, $roles, $reglas;
    public function __construct()
    {
        $this->usuarios = new UsuariosModel();
        $this->roles = new RolesModel();
        helper(['form']);
    }
    public function index()
    {
        
        return view('usuarios/index');
    }

    public function listar()
    {
        $this->usuarios->select('usuarios.id, usuarios.nombre, usuarios.apellido, usuarios.telefono, usuarios.correo, usuarios.direccion, usuarios.estado, roles.nombre AS rol');
        $data = $this->usuarios->join('roles', 'usuarios.id_rol = roles.id')->where('usuarios.estado', '1')->findAll();
        echo json_encode($data);
        die();
    }

    public function new()
    {
        $data['roles'] = $this->roles->where('estado', '1')->findAll();
        return view('usuarios/nuevo', $data);
    }

    public function create()
    {
        $this->reglas = [
            'nombre' => [
                'rules' => 'required'
            ],
            'apellido' => [
                'rules' => 'required'
            ],
            'telefono' => [
                'rules' => 'required|min_length[9]|is_unique[usuarios.telefono]'
            ],
            'correo' => [
                'rules' => 'required|valid_email|is_unique[usuarios.correo]'
            ],
            'direccion' => [
                'rules' => 'required'
            ],
            'rol' => [
                'rules' => 'required'
            ],
            'clave' => [
                'rules' => 'required|min_length[5]'
            ],
            'confirmar' => [
                'rules' => 'required|min_length[5]|matches[clave]'
            ]
        ];

        if ($this->request->is('post') && $this->validate($this->reglas)) {
            $data = $this->usuarios->insert([
                'nombre' => $this->request->getVar('nombre'),
                'apellido' => $this->request->getVar('apellido'),
                'telefono' => $this->request->getVar('telefono'),
                'correo' => $this->request->getVar('correo'),
                'direccion' => $this->request->getVar('direccion'),
                'clave' => password_hash($this->request->getVar('clave'), PASSWORD_DEFAULT),
                'id_rol' => $this->request->getVar('rol'),
            ]);
            if ($data > 0) {
                return redirect()->to(base_url('usuarios'))->with('respuesta', [
                    'type' => 'success',
                    'msg' => 'USUARIO REGISTRADO',
                ]);
            } else {
                return redirect()->to(base_url('usuarios'))->with('respuesta', [
                    'type' => 'danger',
                    'msg' => 'ERROR AL REGISTRAR',
                ]);
            }            
            
        } else {
            $data['validacion'] = $this->validator;
            $data['roles'] = $this->roles->where('estado', '1')->findAll();
            $data['rol'] = $this->request->getVar('rol');
            return view('usuarios/nuevo', $data);
        }        
    }

    public function delete($idUsuario) {
        if ($this->request->is('delete')) {
            //$data = $this->usuarios->delete($idUsuario);
            $data = $this->usuarios->update($idUsuario, ['estado' => '0']);
            if ($data) {
                return redirect()->to(base_url('usuarios'))->with('respuesta', [
                    'type' => 'success',
                    'msg' => 'USUARIO DADO DE BAJA',
                ]);
            } else {
                return redirect()->to(base_url('usuarios'))->with('respuesta', [
                    'type' => 'danger',
                    'msg' => 'ERROR AL ELIMINAR',
                ]);
            } 
            
        }
    }

    public function edit($idUsuario){
        $data['roles'] = $this->roles->where('estado', '1')->findAll();
        $data['usuario'] = $this->usuarios->where('id', $idUsuario)->first();
        return view('usuarios/edit', $data);
    }

    public function update($idUsuario){
        $this->reglas = [
            'id_usuario'    => 'is_natural_no_zero',
            'nombre' => [
                'rules' => 'required'
            ],
            'apellido' => [
                'rules' => 'required'
            ],
            'telefono' => [
                'rules' => 'required|min_length[9]|is_unique[usuarios.telefono,id,{id_usuario}]'
            ],
            'correo' => [
                'rules' => 'required|valid_email|is_unique[usuarios.correo,id,{id_usuario}]'
            ],
            'direccion' => [
                'rules' => 'required'
            ],
            'rol' => [
                'rules' => 'required'
            ]
        ];

        if ($this->request->is('put') && $this->validate($this->reglas)) {
            $data = $this->usuarios->update($idUsuario, [
                'nombre' => $this->request->getVar('nombre'),
                'apellido' => $this->request->getVar('apellido'),
                'telefono' => $this->request->getVar('telefono'),
                'correo' => $this->request->getVar('correo'),
                'direccion' => $this->request->getVar('direccion'),
                'id_rol' => $this->request->getVar('rol')
            ]);
            if ($data > 0) {
                return redirect()->to(base_url('usuarios'))->with('respuesta', [
                    'type' => 'success',
                    'msg' => 'USUARIO MODIFICADO',
                ]);
            } else {
                return redirect()->to(base_url('usuarios'))->with('respuesta', [
                    'type' => 'danger',
                    'msg' => 'ERROR AL MODIFICAR',
                ]);
            }            
            
        } else {
            $data['validacion'] = $this->validator;
            $data['roles'] = $this->roles->where('estado', '1')->findAll();
            $data['rol'] = $this->request->getVar('rol');
            $data['usuario'] = $this->usuarios->where('id', $idUsuario)->first();
            return view('usuarios/edit', $data);
        } 
    }
}
