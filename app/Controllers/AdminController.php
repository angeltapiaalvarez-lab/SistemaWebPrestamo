<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\CajasModel;
use App\Models\ClientesModel;
use App\Models\PrestamosModel;
use App\Models\UsuariosModel;
use Config\Database;
use ZipArchive;

class AdminController extends BaseController
{
    private $admin, $session, $usuarios, $clientes, $prestamos, $cajas;

    public function __construct()
    {
        helper(['form']);
        $this->session = session();
        $this->admin = new AdminModel();
        $this->prestamos = new PrestamosModel();
    }

    public function index()
    {
        if (!verificar('actualizar empresa', $this->session->permisos)) {
            return view('permisos');
        }
        $data['active'] = 'config';
        $data['admin'] = $this->admin->first();
        return view('admin/index', $data);
    }

    public function dashboard()
    {
        $this->usuarios = new UsuariosModel();
        $this->clientes = new ClientesModel();
        $this->cajas = new CajasModel();
        $data['usuarios'] = $this->usuarios->where('estado', '1')->countAllResults();
        $data['clientes'] = $this->clientes->where('estado', '1')->countAllResults();
        $data['prestamos'] = $this->prestamos->where('estado', '1')->countAllResults();
        $data['cajas'] = [];
        foreach (currency_options() as $codigo => $nombre) {
            $resumen = $this->cajas->calcularMovimientos($this->session->id_usuario, $codigo);
            $resumen['nombre'] = $nombre;
            $data['cajas'][$codigo] = $resumen;
        }
        $data['active'] = 'dashboard';
        return view('admin/home', $data);
    }

    public function update($id)
    {
        if ($this->request->is('put')) {
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
                $data['active'] = 'config';
                return view('admin/index', $data);
            }
            if (!empty($img->getName()) && $img->getClientMimeType() === 'image/png') {
                if (!$img->hasMoved()) {
                    $ruta = 'assets/img/logo.png';
                    if (file_exists($ruta)) {
                        unlink($ruta);
                    }
                    $img->move('assets/img', 'logo.png');
                }
            }

            return redirect()->to(base_url('admin'))->with('respuesta', [
                'type' => 'success',
                'msg' => 'DATOS MODIFICADO',
            ]);
        }
    }

    public function prestamosMes($anio)
    {
        $desde = $anio . '-01-01 00:00:00';
        $hasta = $anio . '-12-31 23:59:59';
        $id_usuario = $this->session->id_usuario;
        
        $wherePrestamos = "fecha BETWEEN '$desde' AND '$hasta' AND estado != 0 AND id_usuario = $id_usuario";
        $data['total'] = $this->prestamos->select("
        SUM(IF(MONTH(fecha) = 1, importe, 0)) AS ene,
        SUM(IF(MONTH(fecha) = 2, importe, 0)) AS feb,
        SUM(IF(MONTH(fecha) = 3, importe, 0)) AS mar,
        SUM(IF(MONTH(fecha) = 4, importe, 0)) AS abr,
        SUM(IF(MONTH(fecha) = 5, importe, 0)) AS may,
        SUM(IF(MONTH(fecha) = 6, importe, 0)) AS jun,
        SUM(IF(MONTH(fecha) = 7, importe, 0)) AS jul,
        SUM(IF(MONTH(fecha) = 8, importe, 0)) AS ago,
        SUM(IF(MONTH(fecha) = 9, importe, 0)) AS sep,
        SUM(IF(MONTH(fecha) = 10, importe, 0)) AS oct,
        SUM(IF(MONTH(fecha) = 11, importe, 0)) AS nov,
        SUM(IF(MONTH(fecha) = 12, importe, 0)) AS dic")
            ->where($wherePrestamos)->first();

        // Ingresos Reales:
        $pagosModel = new \App\Models\PagosModel();
        $wherePagos = "fecha_pago BETWEEN '$desde' AND '$hasta' AND id_usuario = $id_usuario";
        $data['ganancia'] = $pagosModel->select("
        SUM(IF(MONTH(fecha_pago) = 1, monto, 0)) AS ene,
        SUM(IF(MONTH(fecha_pago) = 2, monto, 0)) AS feb,
        SUM(IF(MONTH(fecha_pago) = 3, monto, 0)) AS mar,
        SUM(IF(MONTH(fecha_pago) = 4, monto, 0)) AS abr,
        SUM(IF(MONTH(fecha_pago) = 5, monto, 0)) AS may,
        SUM(IF(MONTH(fecha_pago) = 6, monto, 0)) AS jun,
        SUM(IF(MONTH(fecha_pago) = 7, monto, 0)) AS jul,
        SUM(IF(MONTH(fecha_pago) = 8, monto, 0)) AS ago,
        SUM(IF(MONTH(fecha_pago) = 9, monto, 0)) AS sep,
        SUM(IF(MONTH(fecha_pago) = 10, monto, 0)) AS oct,
        SUM(IF(MONTH(fecha_pago) = 11, monto, 0)) AS nov,
        SUM(IF(MONTH(fecha_pago) = 12, monto, 0)) AS dic")
            ->where($wherePagos)->first();

        $totales = [
            $data['total']['ene'] ?? 0, $data['total']['feb'] ?? 0,
            $data['total']['mar'] ?? 0, $data['total']['abr'] ?? 0,
            $data['total']['may'] ?? 0, $data['total']['jun'] ?? 0,
            $data['total']['jul'] ?? 0, $data['total']['ago'] ?? 0,
            $data['total']['sep'] ?? 0, $data['total']['oct'] ?? 0,
            $data['total']['nov'] ?? 0, $data['total']['dic'] ?? 0,
        ];
        $ganancias = [
            $data['ganancia']['ene'] ?? 0, $data['ganancia']['feb'] ?? 0,
            $data['ganancia']['mar'] ?? 0, $data['ganancia']['abr'] ?? 0,
            $data['ganancia']['may'] ?? 0, $data['ganancia']['jun'] ?? 0,
            $data['ganancia']['jul'] ?? 0, $data['ganancia']['ago'] ?? 0,
            $data['ganancia']['sep'] ?? 0, $data['ganancia']['oct'] ?? 0,
            $data['ganancia']['nov'] ?? 0, $data['ganancia']['dic'] ?? 0,
        ];
        
        $data['totales_year'] = array_sum($totales);
        $data['ingresos_year'] = array_sum($ganancias);
        
        $maxImporte = max(max($totales), max($ganancias));
        $data['max'] = ['importe' => ($maxImporte * 1.1) + 100];
        
        echo json_encode($data);
        exit;
    }

    public function createBackup()
    {
        if (verificar('backup', $this->session->permisos)) {
            // Configuración de la base de datos
            $db = Database::connect();
            $tables = $db->listTables();

            // Generar el respaldo
            $backupData = '';
            foreach ($tables as $table) {
                $query = $db->query("SELECT * FROM $table");
                $result = $query->getResultArray();

                if (!empty($result)) {
                    $backupData .= "-- --------------------------------------------------------\n";
                    $backupData .= "-- Estructura de tabla para $table\n";
                    $backupData .= "-- --------------------------------------------------------\n\n";

                    $schemaQuery = $db->query("SHOW CREATE TABLE $table");
                    $schema = $schemaQuery->getRow()->{'Create Table'};
                    $backupData .= $schema . ";\n\n";

                    $backupData .= "-- --------------------------------------------------------\n";
                    $backupData .= "-- Datos de tabla para $table\n";
                    $backupData .= "-- --------------------------------------------------------\n\n";

                    foreach ($result as $row) {
                        $values = array_map(function ($value) {
                            return "'" . str_replace("'", "''", $value) . "'";
                        }, $row);

                        $backupData .= "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
                    }

                    $backupData .= "\n";
                }
            }

            // Guardar el respaldo en un archivo
            $backupFilename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            if (!file_exists(WRITEPATH . 'backups')) {
                mkdir(WRITEPATH . 'backups');
            }
            $backupPath = WRITEPATH . 'backups/' . $backupFilename;

            if (!empty($backupData)) {
                if (file_put_contents($backupPath, $backupData) !== false) {
                    $zipPath = WRITEPATH . 'backups/backup.zip';
                    if ($this->crearZip($backupPath, $zipPath)) {
                        unlink($backupPath);
                        // Ruta y nombre del archivo a descargar
                        $filePath = $zipPath;
                        $fileName = 'archivo.zip';

                        // Verifica si el archivo existe
                        if (file_exists($filePath)) {
                            header('Content-Type: application/octet-stream');
                            header('Content-Disposition: attachment; filename="' . $fileName . '"');
                            header('Content-Length: ' . filesize($filePath));

                            // Lee y envía el contenido del archivo
                            readfile($filePath);
                            unlink($filePath);
                            exit;
                        } else {
                            return redirect()->to(base_url('dashboard'))->with('respuesta', [
                                'type' => 'danger',
                                'msg' => 'EL ARCHIVO NO SE ENCONTRO'
                            ]);
                        }
                    } else {
                        $mensajeError = (!class_exists('ZipArchive')) 
                            ? 'ERROR: EXtensión ZIP de PHP no habilitada en tu servidor XAMPP.' 
                            : 'ERROR AL CREAR ZIP';
                        return redirect()->to(base_url('dashboard'))->with('respuesta', [
                            'type' => 'danger',
                            'msg' => $mensajeError
                        ]);
                    }
                } else {
                    return redirect()->to(base_url('dashboard'))->with('respuesta', [
                        'type' => 'danger',
                        'msg' => 'ERROR AL CREAR RESPALDO'
                    ]);
                }
            } else {
                return redirect()->to(base_url('dashboard'))->with('respuesta', [
                    'type' => 'danger',
                    'msg' => 'NO SE ENCONTRARON DATOS PARA RESPALDAR',
                ]);
            }
        } else {
            echo view("permisos");
        }
    }

    public function crearZip($ruta, $zipFilename)
    {
        if (!class_exists('ZipArchive')) {
            return false;
        }
        $zip = new ZipArchive();

        // Abre el archivo ZIP en modo de creación
        if ($zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // Agrega el archivo PHP al archivo ZIP
            $zip->addFile($ruta, basename($ruta));

            // Cierra el archivo ZIP
            $zip->close();

            return true;
        } else {
            return false;
        }
    }
}
