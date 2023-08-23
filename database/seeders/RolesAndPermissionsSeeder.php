<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Crear roles
         $adminRole = Role::create(['name' => 'Administrador', 'guard_name' => 'web']);
         $compraRole = Role::create(['name' => 'Compra', 'guard_name' => 'web']);
         //$ventaRole = Role::create(['name' => 'venta', 'guard_name' => 'web']);
         //$userRole = Role::create(['name' => 'User', 'guard_name' => 'web']);
 
         // Crear permisos
         $creaeRolPermission = Permission::create(['name' => 'crear roles', 'guard_name' => 'web']);
         $editarRolPermission = Permission::create(['name' => 'editar roles', 'guard_name' => 'web']);
 
         $verPerfilPermission = Permission::create(['name' => 'ver perfil', 'guard_name' => 'web']);
         $actualizarPerfilPermission = Permission::create(['name' => 'actualizar perfil', 'guard_name' => 'web']);
         $cambioPerfilPermission = Permission::create(['name' => 'actualizar contraseña', 'guard_name' => 'web']);
 
         // Asignar permisos a roles
         $adminRole->givePermissionTo($creaeRolPermission);
         $adminRole->givePermissionTo($editarRolPermission);
 
         $compraRole->givePermissionTo($verPerfilPermission);
         $compraRole->givePermissionTo($actualizarPerfilPermission);
         $compraRole->givePermissionTo($cambioPerfilPermission);
 
         // Asignar roles a usuarios
         $adminUser = User::find(1); // Cambia el ID por el del usuario
         $adminUser->assignRole($adminRole);
 
         $ventaUser = User::find(2); // Cambia el ID por el del usuario
         $ventaUser->assignRole($compraRole);
    }
}
