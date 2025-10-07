<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateReportPermissions extends Migration
{
    public function up()
    {
        $reportPermissions = ['pdf prestamos', 'excel prestamos', 'historial pagos', 'historial transacciones', 'historial prestamos'];
        $this->db->table('permisos')
            ->where('modulo', 'reportes')
            ->update([
                'campos' => json_encode($reportPermissions),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->db->table('permisos')
            ->whereIn('modulo', ['pagos', 'transacciones'])
            ->delete();
    }

    public function down()
    {
        $originalReportPermissions = ['pdf prestamos', 'excel prestamos'];
        $this->db->table('permisos')
            ->where('modulo', 'reportes')
            ->update([
                'campos' => json_encode($originalReportPermissions),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $timestamp = date('Y-m-d H:i:s');
        $this->db->table('permisos')->insertBatch([
            [
                'modulo' => 'pagos',
                'campos' => json_encode(['historial pagos']),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'modulo' => 'transacciones',
                'campos' => json_encode(['historial transacciones']),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }
}
