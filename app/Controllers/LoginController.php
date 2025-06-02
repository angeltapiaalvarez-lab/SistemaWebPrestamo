<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuariosModel;

class LoginController extends BaseController
{
    private $reglas, $usuarios, $session;

    public function __construct()
    {
        helper(['form']);
        $this->usuarios = new UsuariosModel();
        $this->session = session();
    }

    public function validar()
    {
        $this->reglas = [
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'El campo correo es requerido',
                    'valid_email' => 'Ingrese un correo valido',
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo contraseña es requerido'
                ]
            ]
        ];
        if ($this->request->is('post') && $this->validate($this->reglas)) {
            $result = $this->usuarios->where([
                    'correo' => $this->request->getVar('email'),
                    'estado' => '1',
                ])->first();
            if ($result != null) {
                if (password_verify($this->request->getVar('password'), $result['clave'])) {
                    $datos = [
                        'id_usuario' => $result['id'],
                        'correo' => $result['correo'],
                        'nombre' => $result['nombre']
                    ];
                    $this->session->set($datos);
                    return redirect()->to(base_url('dashboard'))->with('respuesta', [
                        'type' => 'success',
                        'msg' => 'HAS INICIADO SESION CORRECTAMENTE',
                    ]);
                } else {
                    return redirect()->to(base_url())->with('respuesta', [
                        'type' => 'warning',
                        'msg' => 'CONTRASEÑA INCORRECTA',
                    ]);
                }
            } else {
                return redirect()->to(base_url())->with('respuesta', [
                    'type' => 'warning',
                    'msg' => 'EL CORREO NO EXISTE',
                ]);
            }
        } else {
            $data['validator'] = $this->validator;
            return view('index', $data);
        }
    }
}
