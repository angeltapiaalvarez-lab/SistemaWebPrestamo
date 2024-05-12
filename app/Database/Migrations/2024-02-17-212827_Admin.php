<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Admin extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'identidad' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'correo' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'unique'     => true,
            ],
            'direccion' => [
                'type'       => 'TEXT'
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'tasa_interes' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'mensaje' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'cuotas' => [
                'type'       => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('configuracion');
    }

    public function down()
    {
        $this->forge->dropTable('configuracion');
    }
}
