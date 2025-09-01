<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToPagos extends Migration
{
    public function up()
    {
        $fields = [
            'interes' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0,
                'after'      => 'monto',
            ],
            'mora' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0,
                'after'      => 'interes',
            ],
            'capital' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0,
                'after'      => 'mora',
            ],
            'cargos' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0,
                'after'      => 'capital',
            ],
        ];
        $this->forge->addColumn('pagos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pagos', ['interes','mora','capital','cargos']);
    }
}
