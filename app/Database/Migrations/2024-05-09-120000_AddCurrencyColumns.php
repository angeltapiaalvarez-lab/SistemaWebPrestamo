<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCurrencyColumns extends Migration
{
    public function up()
    {
        $this->forge->addColumn('cajas', [
            'moneda' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
                'default'    => 'NIO',
                'after'      => 'monto_inicial',
            ],
        ]);

        $this->db->table('cajas')->set(['moneda' => 'NIO'])->update();

        $this->forge->addColumn('prestamos', [
            'moneda' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
                'default'    => 'NIO',
                'after'      => 'importe',
            ],
        ]);

        $this->db->table('prestamos')->set(['moneda' => 'NIO'])->update();
    }

    public function down()
    {
        $this->forge->dropColumn('cajas', 'moneda');
        $this->forge->dropColumn('prestamos', 'moneda');
    }
}
