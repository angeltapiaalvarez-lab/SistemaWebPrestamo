<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Payments extends Migration
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
            'id_detalle_prestamo' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'monto' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'fecha_pago' => [
                'type' => 'DATETIME',
            ],
            'metodo' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_detalle_prestamo', 'detalle_prestamos', 'id', 'CASCADE', 'CASCADE', 'fk_detalle');
        $this->forge->createTable('pagos');
    }

    public function down()
    {
        $this->forge->dropTable('pagos');
    }
}
