<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CajasModel;

class CajasController extends BaseController
{
    private $cajas, $session, $monedas;
    public function __construct()
    {
        helper(['form', 'moneda']);
        $this->cajas = new CajasModel();
        $this->session = session();
        $this->monedas = currency_options();
    }
    public function index()
    {
        if (!verificar('ver saldo', $this->session->permisos)) {
            return view('permisos');
        }
        $registros = $this->cajas->where([
            'estado' => '1',
            'id_usuario' => $this->session->id_usuario
        ])->findAll();

        $data['cajas'] = [];
        foreach ($registros as $caja) {
            $codigo = strtoupper($caja['moneda'] ?? 'NIO');
            $data['cajas'][$codigo] = $caja;
        }

        $data['monedas'] = $this->monedas;
        $seleccionada = $this->request->getGet('moneda');
        if (!array_key_exists($seleccionada, $this->monedas)) {
            $seleccionada = array_key_first($this->monedas);
        }
        $data['monedaSeleccionada'] = $seleccionada;

        $data['resumen'] = [];
        foreach (array_keys($this->monedas) as $codigo) {
            $data['resumen'][$codigo] = $this->cajas->calcularMovimientos($this->session->id_usuario, $codigo);
        }
        $data['active'] = 'caja';
        return view('cajas/index', $data);
    }

    public function new()
    {
        if (!verificar('ver saldo', $this->session->permisos)) {
            return view('permisos');
        }
        $seleccionada = $this->request->getGet('moneda');
        if (!array_key_exists($seleccionada, $this->monedas)) {
            $seleccionada = array_key_first($this->monedas);
        }
        $data['monedas'] = $this->monedas;
        $data['monedaSeleccionada'] = $seleccionada;
        $data['active'] = 'caja';
        return view('cajas/nuevo', $data);
    }

    public function create()
    {
        if ($this->request->is('post') && verificar('ver saldo', $this->session->permisos)) {
            $moneda = strtoupper($this->request->getVar('moneda'));
            if (!array_key_exists($moneda, $this->monedas)) {
                $moneda = array_key_first($this->monedas);
            }
            $data = [
                'id_caja' => $this->request->getVar('id_caja'),
                'monto_inicial' => $this->request->getVar('monto'),
                'fecha_apertura' => date('Y-m-d H:i:s'),
                'moneda' => $moneda,
                'estado' => '1',
                'id_usuario' => $this->session->id_usuario
            ];
            $consulta = $this->cajas->where([
                'estado' => '1',
                'id_usuario' => $this->session->id_usuario,
                'moneda' => $moneda
            ])->first();
            if (empty($consulta)) {
                if ($this->cajas->insert($data) === false) {
                    $data['errors'] = $this->cajas->errors();
                    $data['monedas'] = $this->monedas;
                    $data['monedaSeleccionada'] = $moneda;
                    $data['active'] = 'caja';
                    return view('cajas/nuevo', $data);
                }
                return redirect()->to(base_url('cajas'))->with('respuesta', [
                    'type' => 'success',
                    'msg' => 'MONTO REGISTRADO',
                ]);
            } else {
                return redirect()->to(base_url('cajas'))->with('respuesta', [
                    'type' => 'danger',
                    'msg' => 'YA TIENES UN MONTO INICIAL PARA ' . currency_name($moneda),
                ]);
            }
        } else {
            return view('permisos');
        }
    }

    public function edit($id)
    {
        if (!verificar('ver saldo', $this->session->permisos)) {
            return view('permisos');
        }
        $data['caja'] = $this->cajas->where('id', $id)->first();
        $data['monedas'] = $this->monedas;
        $data['active'] = 'caja';
        return view('cajas/edit', $data);
    }

    public function update($id)
    {
        if ($this->request->is('put') && verificar('ver saldo', $this->session->permisos)) {
            $moneda = strtoupper($this->request->getVar('moneda'));
            if (!array_key_exists($moneda, $this->monedas)) {
                $moneda = array_key_first($this->monedas);
            }
            $data = [
                'id_caja' => $this->request->getVar('id_caja'),
                'monto_inicial' => $this->request->getVar('monto'),
                'moneda' => $moneda,
            ];

            if ($this->cajas->update($id, $data) === false) {
                $data['errors'] = $this->cajas->errors();
                $data['caja'] = $this->cajas->where('id', $id)->first();
                $data['monedas'] = $this->monedas;
                $data['active'] = 'caja';
                return view('cajas/edit', $data);
            }
            return redirect()->to(base_url('cajas'))->with('respuesta', [
                'type' => 'success',
                'msg' => 'MONTO MODIFICADO',
            ]);
        } else {
            return view('permisos');
        }
    }

    public function movimientos()
    {
        if (!verificar('ver saldo', $this->session->permisos)) {
            $data = [];
        }else{
            $moneda = $this->request->getGet('moneda');
            if (!array_key_exists($moneda, $this->monedas)) {
                $moneda = array_key_first($this->monedas);
            }
            $data = $this->cajas->calcularMovimientos($this->session->id_usuario, $moneda);
            $data['moneda_nombre'] = currency_name($moneda);
            echo json_encode($data);
        }
        die();
    }
}
