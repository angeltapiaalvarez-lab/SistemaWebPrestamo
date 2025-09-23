<?php

namespace App\Models;

use CodeIgniter\Model;

class CajasModel extends Model
{
    protected $table            = 'cajas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['monto_inicial', 'moneda', 'fecha_apertura', 'ganancia', 'estado', 'id_usuario'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'id_caja' => 'is_natural',
        'moneda' => 'required|in_list[NIO,USD]',
        'monto_inicial'    => [
            'rules'  => 'required',
            'errors' => [
                'required' => 'El monto es requerido'
            ],
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function calcularMovimientos($id_usuario, string $moneda = 'NIO') {
        $moneda = strtoupper($moneda);
        if (!array_key_exists($moneda, currency_options())) {
            $moneda = 'NIO';
        }
        $prestamos = new PrestamosModel();
        $inicial = $this->select('monto_inicial')->where([
            'estado' => '1',
            'id_usuario' => $id_usuario,
            'moneda' => $moneda,
        ])->first();

        $incialSaldo = (empty($inicial)) ? 0 : $inicial['monto_inicial'];

        $egreso = $prestamos->selectSum('importe')->where([
            'estado' => '1',
            'id_usuario' => $id_usuario,
            'moneda' => $moneda,
        ])->first();

        $detPrestamo = new DetPrestamoModel();
        $detalles = $detPrestamo
            ->select('detalle_prestamos.importe_cuota, detalle_prestamos.estado, p.importe, p.cuotas')
            ->join('prestamos AS p', 'detalle_prestamos.id_prestamo = p.id')
            ->where('p.id_usuario', $id_usuario)
            ->where('p.estado !=', '0')
            ->where('p.moneda', $moneda)
            ->findAll();

        $capital = 0;
        $interes = 0;
        foreach ($detalles as $detalle) {
            if ($detalle['estado'] == 0) {
                $capitalCuota  = $detalle['importe'] / $detalle['cuotas'];
                $interesCuota  = $detalle['importe_cuota'] - $capitalCuota;
                $capital      += $capitalCuota;
                $interes      += $interesCuota;
            }
        }

        $totalIngreso = $capital + $interes;

        $data['inicial'] = $incialSaldo;
        $data['egreso']  = ($egreso['importe'] != null) ? $egreso['importe'] : 0;
        $data['capital'] = $capital;
        $data['interes'] = $interes;
        $data['ingreso'] = $totalIngreso; // capital + interes
        //CALCULAR SALDO
        $data['saldo'] = ($data['inicial'] - $data['egreso']) + $data['ingreso'];
        
        $data['decimales'] = [
            'inicial' => number_format($data['inicial'], 2),
            'egreso'  => number_format($data['egreso'], 2),
            'capital' => number_format($data['capital'], 2),
            'interes' => number_format($data['interes'], 2),
            'ingreso' => number_format($data['ingreso'], 2),
            'saldo'   => number_format($data['saldo'], 2)
        ];
        $data['moneda'] = $moneda;
        $data['simbolo'] = currency_symbol($moneda);
        return $data;
    }
}
