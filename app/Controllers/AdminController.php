<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class AdminController extends BaseController
{
    private $admin, $session;

    public function __construct()
    {
        helper(['form']);
        $this->session = session();
        $this->admin = new AdminModel();
    }

    public function index()
    {
        $data['admin'] = $this->admin->first();
        return view('admin/index', $data);
    }

    public function dashboard()
    {
        return view('admin/home');
    }

    public function update($id){
        if ($this->request->is('put')){
            $img = $this->request->getFile('logo');
            $data = [
                'id' => $id,
                'identidad' => $this->request->getVar('identidad'),
                'nombre' => $this->request->getVar('nombre'),
                'telefono' => $this->request->getVar('telefono'),
                'correo' => $this->request->getVar('correo'),
                'direccion' => $this->request->getVar('direccion'),
                'mensaje' => $this->request->getVar('mensaje'),
                'tasa_interes' => $this->request->getVar('tasa_interes'),
                'cuotas' => $this->request->getVar('cuotas')
            ];
            if ($this->admin->update($id, $data) === false) {
                $data['admin'] = $this->admin->first();
                $data['errors'] = $this->admin->errors();
                return view('admin/index', $data);
            } 
            if (!empty($img->getName()) && $img->getClientMimeType() === 'image/png') {
                if (!$img->hasMoved()) {
                    $ruta = WRITEPATH . 'uploads/logo.png';
                    if (file_exists($ruta)) {
                        unlink($ruta);
                    }
                    $img->move(WRITEPATH . 'uploads', 'logo.png');
                }
            }     
            
            return redirect()->to(base_url('admin'))->with('respuesta', [
                'type' => 'success',
                'msg' => 'DATOS MODIFICADO',
            ]);
        }
        
        
    }
}
