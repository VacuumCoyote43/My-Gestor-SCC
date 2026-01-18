<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'admin',
                'descripcion' => 'Administrador del sistema - Gestiona usuarios, clubes, proveedores e incidencias',
            ],
            [
                'nombre' => 'proveedor',
                'descripcion' => 'Proveedor - Emite facturas a clubes u otros proveedores',
            ],
            [
                'nombre' => 'gestor_club',
                'descripcion' => 'Gestor de Club - Gestiona uno o más clubes, registra pagos y crea cargos a jugadores',
            ],
            [
                'nombre' => 'jugador',
                'descripcion' => 'Jugador - Consulta deudas y registra pagos',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['nombre' => $roleData['nombre']],
                ['descripcion' => $roleData['descripcion']]
            );
        }
    }
}
