<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'identidad' => '123456789',
            'nombre'    => 'SysPrey',
            'telefono'    => '78374529',
            'correo'    => 'sys@gmail.com',
            'direccion'    => 'Mangua, portezuelo parque industrial',
            'mensaje'    => 'gracias por elegir sysprey',
            'tasa_interes'    => '16',
            'cuotas'    => '18',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        // Using Query Builder
        $this->db->table('configuracion')->insert($data);
    }
}
