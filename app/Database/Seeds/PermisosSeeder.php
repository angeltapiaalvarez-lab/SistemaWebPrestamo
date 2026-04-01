<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermisosSeeder extends Seeder
{
    public function run()
    {
        // -------------------------------------------------------------
        // REGISTRO DINAMICO DE MÓDULOS DEL SISTEMA
        // Para agregar un módulo nuevo, simplemente añade su nombre como llave
        // y un arreglo [ ] con todos sus permisos textuales.
        // -------------------------------------------------------------
        $permisos = [
            'usuarios'      => ['listar usuarios', 'nuevo usuario', 'editar usuario', 'eliminar usuario'],
            'configuracion' => ['actualizar empresa', 'backup'],
            'roles'         => ['listar roles', 'nuevo rol', 'editar rol', 'eliminar rol'],
            'clientes'      => ['listar clientes', 'nuevo cliente', 'editar cliente', 'eliminar cliente'],
            'prestamos'     => ['nuevo prestamo', 'historial prestamos', 'ver prestamo', 'eliminar prestamo', 'abono prestamo'],
            'cajas'         => ['ver saldo'],
            'reportes'      => ['pdf prestamos', 'excel prestamos', 'historial pagos', 'historial transacciones', 'historial prestamos'],
            'pagos'         => ['historial pagos'],
            'transacciones' => ['historial transacciones'],
        ];

        foreach ($permisos as $modulo => $campos) {
            // Verificar si el módulo específico ya existe en DB para no duplicarlo,
            // modelo Idempotente.
            $existe = $this->db->table('permisos')->where('modulo', $modulo)->countAllResults();
            
            if ($existe == 0) {
                // Inserción Pura
                $this->db->table('permisos')->insert([
                    'modulo'     => $modulo,
                    'campos'     => json_encode($campos),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                // Actualizar silenciosamente para reflejar campos recién añadidos al código 
                $this->db->table('permisos')->where('modulo', $modulo)->update([
                    'campos'     => json_encode($campos),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
}
