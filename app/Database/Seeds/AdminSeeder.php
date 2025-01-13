<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'identidad' => '401-091299-1011H',
            'nombre'    => 'Angel Tapia',
            'telefono'    => '78374529',
            'correo'    => 'angel.tapia78910@gmail.com',
            'direccion'    => 'Nicaragua',
            'mensaje'    => 'si',
            'tasa_interes'    => '10',
            'cuotas'    => '18',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];
        // Using Query Builder
        $this->db->table('configuracion')->insert($data);
    }
}
