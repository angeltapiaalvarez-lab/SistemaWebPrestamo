<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSaldoToPrestamos extends Migration
{
    public function up()
    {
        $fields = [
            'saldo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
        ];
        $this->forge->addColumn('prestamos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('prestamos', 'saldo');
    }
}
