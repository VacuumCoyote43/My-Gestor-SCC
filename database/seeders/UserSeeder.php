<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Club;
use App\Models\Proveedor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles
        $adminRole = Role::where('nombre', 'admin')->first();
        $proveedorRole = Role::where('nombre', 'proveedor')->first();
        $gestorClubRole = Role::where('nombre', 'gestor_club')->first();
        $jugadorRole = Role::where('nombre', 'jugador')->first();

        // Crear usuario admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@mgscc.local'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'active' => true,
            ]
        );

        // Crear usuario proveedor de ejemplo
        $userProveedor = User::firstOrCreate(
            ['email' => 'proveedor@example.com'],
            [
                'name' => 'Proveedor Demo',
                'password' => Hash::make('password'),
                'role_id' => $proveedorRole->id,
                'active' => true,
                'created_by' => $admin->id,
            ]
        );

        // Crear proveedor asociado
        $proveedor = Proveedor::firstOrCreate(
            ['nif_cif' => 'B12345678'],
            [
                'nombre_legal' => 'Proveedor Demo S.L.',
                'email' => 'proveedor@example.com',
                'direccion' => 'Calle Ejemplo 123',
                'es_liga' => false,
                'created_by' => $admin->id,
            ]
        );

        // Crear usuario gestor de club
        $userGestor = User::firstOrCreate(
            ['email' => 'gestor@club.com'],
            [
                'name' => 'Gestor Club Demo',
                'password' => Hash::make('password'),
                'role_id' => $gestorClubRole->id,
                'active' => true,
                'created_by' => $admin->id,
            ]
        );

        // Crear club de ejemplo
        $club = Club::firstOrCreate(
            ['nombre' => 'Club Deportivo Demo'],
            [
                'cif' => 'G87654321',
                'email' => 'club@demo.com',
                'direccion' => 'Avenida Deporte 456',
                'gestor_id' => $userGestor->id,
                'created_by' => $admin->id,
            ]
        );
        
        // Actualizar gestor_id si el club ya existía
        if (!$club->gestor_id) {
            $club->update(['gestor_id' => $userGestor->id]);
        }

        // Crear usuario jugador
        $jugador = User::firstOrCreate(
            ['email' => 'jugador@demo.com'],
            [
                'name' => 'Jugador Demo',
                'password' => Hash::make('password'),
                'role_id' => $jugadorRole->id,
                'active' => true,
                'created_by' => $admin->id,
            ]
        );

        $this->command->info('Usuarios de ejemplo creados:');
        $this->command->info('Admin: admin@mgscc.local / password');
        $this->command->info('Proveedor: proveedor@example.com / password');
        $this->command->info('Gestor Club: gestor@club.com / password');
        $this->command->info('Jugador: jugador@demo.com / password');
    }
}
