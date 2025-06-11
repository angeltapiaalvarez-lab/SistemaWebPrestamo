<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'nombre'    => 'ANGEL',
            'apellido'    => 'SIFUENTES',
            'telefono'    => '900897537',
            'correo'    => 'info@angelsifuentes.net',
            'direccion'    => 'Perú',
            'clave'    => password_hash('admin123456789', PASSWORD_DEFAULT),
            'verify'    => '1',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
            'id_rol'    => 1,
        ];
        // Using Query Builder
        $this->db->table('usuarios')->insert($data);
    }
}
