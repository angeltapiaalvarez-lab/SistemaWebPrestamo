<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'nombre'    => 'Angel',
            'apellido'    => 'Sifuentes',
            'telefono'    => '78374529',
            'correo'    => 'sys@gmail.com',
            'direccion'    => 'Mangua, portezuelo parque industrial',
            'clave'    => password_hash('admin1234', PASSWORD_DEFAULT),
            'estado'    => '1',
            'verify'    => '1',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
            'id_rol' => '1',
        ];

        // Using Query Builder
        $this->db->table('usuarios')->insert($data);
    }
}
