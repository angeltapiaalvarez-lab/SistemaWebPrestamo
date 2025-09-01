<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToDetallePrestamos extends Migration
{
    public function up()
    {
        $fields = [
            'capital' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'after'      => 'importe_cuota',
            ],
            'interes' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'after'      => 'capital',
            ],
            'mora' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0,
                'after'      => 'interes',
            ],
            'cargos' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0,
                'after'      => 'mora',
            ],
        ];
        $this->forge->addColumn('detalle_prestamos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('detalle_prestamos', ['capital','interes','mora','cargos']);
    }
}
