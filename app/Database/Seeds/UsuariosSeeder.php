<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'nombre'    => 'ANGEL',
            'apellido'    => 'TAPIA',
            'telefono'    => '900897537',
            'correo'    => 'angel.tapia78910@gmail.com',
            'direccion'    => 'Nicaragua/Masaya',
            'clave'    => password_hash('admin1234', PASSWORD_DEFAULT),
            'verify'    => '1',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
            'id_rol'    => 1,
        ];
        // Using Query Builder
        $this->db->table('usuarios')->insert($data);
    }
}
