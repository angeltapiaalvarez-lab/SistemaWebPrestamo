<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'identidad' => '123456789',
            'nombre'    => 'VIDA INFORMÁTICO',
            'telefono'    => '900897537',
            'correo'    => 'info@angelsifuentes.net',
            'direccion'    => 'Perú',
            'mensaje'    => 'GRACIAS POR ADQUIRIR EL CURSO',
            'tasa_interes'    => '10',
            'cuotas'    => '18',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];
        // Using Query Builder
        $this->db->table('configuracion')->insert($data);
    }
}
