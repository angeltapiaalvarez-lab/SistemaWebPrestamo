<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'identidad' => '401-091299-1011H',
            'nombre'    => 'CrediAdmin',
            'telefono'    => '900897537',
            'correo'    => 'angel.tapia78910@gmail.com',
            'direccion'    => 'Nicaragua, Masaya',
            'mensaje'    => 'GRACIAS POR LA OPORTUNIDAD DEL SISTEMA',
            'tasa_interes'    => '10',
            'cuotas'    => '18',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];
        // Using Query Builder
        $this->db->table('configuracion')->insert($data);
    }
}
