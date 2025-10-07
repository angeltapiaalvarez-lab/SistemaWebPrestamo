<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\UsuariosModel;
use App\Models\TransaccionesModel;
use App\Models\PermisosModel;

class LoginController extends BaseController
{
    private $reglas, $usuarios, $transacciones, $session;

    public function __construct()
    {
        helper(['form', 'email']);
        $this->usuarios = new UsuariosModel();
        $this->transacciones = new TransaccionesModel();
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

            $result = $this->usuarios
                ->select('usuarios.*, r.nombre AS rol, r.permisos')
                ->join('roles AS r', 'usuarios.id_rol = r.id')
                ->where([
                    'usuarios.correo' => $this->request->getVar('email'),
                    'usuarios.estado' => '1',
                ])->first();
            if ($result != null) {
                if (password_verify($this->request->getVar('password'), $result['clave'])) {
                    $permisos = [];
                    if ($result['id'] == 1) {
                        $permisosModel = new PermisosModel();
                        $permisosSistema = $permisosModel->findAll();
                        foreach ($permisosSistema as $permiso) {
                            $campos = json_decode($permiso['campos'], true);
                            if (is_array($campos)) {
                                $permisos = array_merge($permisos, $campos);
                            }
                        }
                        $permisos = array_values(array_unique(array_merge($permisos, ['historial pagos', 'historial transacciones', 'historial prestamos'])));
                    } else {
                        $permisos = ($result['permisos'] != null) ? json_decode($result['permisos'], true) : [];
                    }
                    if (!is_array($permisos)) {
                        $permisos = [];
                    }
                    $datos = [
                        'id_usuario' => $result['id'],
                        'rol' => $result['rol'],
                        'nombre' => $result['nombre'],
                        'perfil' => $result['perfil'],
                        'permisos' => $permisos,
                    ];
                    $this->session->set($datos);
                    $this->transacciones->insert([
                        'accion'      => 'LOGIN',
                        'descripcion' => 'Inicio de sesión del usuario ID ' . $result['id'],
                        'id_usuario'  => $result['id'],
                    ]);
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

    public function forgot()
    {
        return view('usuarios/forgot');
    }

    public function reset()
    {
        $this->reglas = [
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'El campo correo es requerido',
                    'valid_email' => 'Ingrese un correo valido',
                ]
            ]
        ];
        if ($this->request->is('post') && $this->validate($this->reglas)) {
            $correo = $this->request->getVar('email');
            $user = $this->usuarios->where([
                'correo' => $correo,
                'estado' => '1'
            ])->first();
            if (empty($user)) {
                return redirect()->to(base_url('forgot'))->with('respuesta', [
                    'type' => 'danger',
                    'msg' => 'EL CORREO NO EXISTE EN EL SISTEMA'
                ]);
            } else {
                $datos = new AdminModel();
                $empresa = $datos->first();
                $token = bin2hex(random_bytes(16));
                $body = 'Has pedido restablecer tu contraseña, si no has sido tu omite este mensaje '
                    . '<a href="' . base_url('restablecer/' . $token) . '">CLIC AQUI PARA CAMBIAR</a>';

                if (sendEmail(
                    $correo,
                    $user['nombre'],
                    'Olvidaste tu contraseña - ' . $empresa['nombre'],
                    $body,
                    $empresa['correo'],
                    $empresa['nombre']
                )) {
                    $this->usuarios->update($user['id'], ['token' => $token]);
                    return redirect()->to(base_url('forgot'))->with('respuesta', [
                        'type' => 'success',
                        'msg' => 'CORREO ENVIADO',
                    ]);
                }

                return redirect()->to(base_url('forgot'))->with('respuesta', [
                    'type' => 'danger',
                    'msg' => 'ERROR AL ENVIAR CORREO',
                ]);
            }
        } else {
            $data['validator'] = $this->validator;
            return view('usuarios/forgot', $data);
        }
    }

    public function restablecer($token)
    {
        $consulta = $this->usuarios->where([
            'token' => $token,
            'estado' => '1'
        ])->first();
        if (empty($consulta)) {
            return redirect()->to(base_url())->with('respuesta', [
                'type' => 'danger',
                'msg' => 'TOKEN ALTERADO',
            ]);
        }else{
            $data['token'] = $token;
            return view('usuarios/restablecer', $data);
        }
        
    }

    public function restablecerPass() {
        $this->reglas = [
            'nueva' => [
                'rules' => 'required|min_length[5]'
            ],
            'confirmar' => [
                'rules' => 'required|min_length[5]|matches[nueva]'
            ]
        ];

        if ($this->request->is('put') && $this->validate($this->reglas)) {
            $consulta = $this->usuarios->where([
                'token' => $this->request->getVar('token'),
                'estado' => '1'
            ])->first();

            if (!empty($consulta)) {
                $data = $this->usuarios->update($consulta['id'], [
                    'clave' => password_hash($this->request->getVar('nueva'), PASSWORD_DEFAULT),
                    'token' => null
                ]);
                if ($data) {
                    return redirect()->to(base_url())->with('respuesta', [
                        'type' => 'success',
                        'msg' => 'CONTRASEÑA RESTABLECIDA',
                    ]);
                } else {
                    return redirect()->to(base_url())->with('respuesta', [
                        'type' => 'danger',
                        'msg' => 'ERROR AL RESTABLECER',
                    ]);
                }
            }else{
                return redirect()->to(base_url())->with('respuesta', [
                    'type' => 'warning',
                    'msg' => 'TOKEN ALTERA',
                ]);
            }
            
        } else {
            $data['validacion'] = $this->validator;
            $data['token'] = $this->request->getVar('token');
            return view('usuarios/restablecer', $data);
        }
    }
}
