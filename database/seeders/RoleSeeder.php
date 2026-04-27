<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $superAdmin = Role::create(['name' => 'SuperAdmin']);
        $admin = Role::create(['name' => 'Admin']);
        $therapist = Role::create(['name' => 'Fisioterapeuta']);

        // Permisos - Dashboard
        Permission::create(['name' => 'admin.home'])->syncRoles([$superAdmin, $admin, $therapist]);

        // Permisos - Usuarios
        Permission::create(['name' => 'admin.users.index'])->syncRoles([$superAdmin, $admin]);
        Permission::create(['name' => 'admin.users.create'])->syncRoles([$superAdmin, $admin]);
        Permission::create(['name' => 'admin.users.edit'])->syncRoles([$superAdmin, $admin]);
        Permission::create(['name' => 'admin.users.destroy'])->syncRoles([$superAdmin]);

        // Permisos - Pacientes
        Permission::create(['name' => 'admin.patients.index'])->syncRoles([$superAdmin, $admin, $therapist]);
        Permission::create(['name' => 'admin.patients.create'])->syncRoles([$superAdmin, $admin, $therapist]);
        Permission::create(['name' => 'admin.patients.show'])->syncRoles([$superAdmin, $admin, $therapist]);
        Permission::create(['name' => 'admin.patients.edit'])->syncRoles([$superAdmin, $admin, $therapist]);
        Permission::create(['name' => 'admin.patients.destroy'])->syncRoles([$superAdmin, $admin]);

        // Permisos - Consultas
        Permission::create(['name' => 'admin.consultations.index'])->syncRoles([$superAdmin, $admin, $therapist]);
        Permission::create(['name' => 'admin.consultations.create'])->syncRoles([$superAdmin, $admin, $therapist]);
        Permission::create(['name' => 'admin.consultations.edit'])->syncRoles([$superAdmin, $admin, $therapist]);
        Permission::create(['name' => 'admin.consultations.destroy'])->syncRoles([$superAdmin, $admin]);

        // Permisos - Pagos
        Permission::create(['name' => 'admin.payments.index'])->syncRoles([$superAdmin, $admin, $therapist]);
        Permission::create(['name' => 'admin.payments.create'])->syncRoles([$superAdmin, $admin, $therapist]);
        Permission::create(['name' => 'admin.payments.edit'])->syncRoles([$superAdmin, $admin]);
        Permission::create(['name' => 'admin.payments.destroy'])->syncRoles([$superAdmin]);
    }
}
