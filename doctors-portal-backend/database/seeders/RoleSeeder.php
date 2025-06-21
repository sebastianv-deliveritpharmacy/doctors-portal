<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = ['super_admin', 'admin', 'doctor'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Assign roles to existing users (customize as needed)
        $superAdmin = User::where('email', 'sebastian.v@deliveritpharmacy.com')->first();
        if ($superAdmin) {
            $superAdmin->assignRole('super_admin');
        }

        // $admin = User::where('email', 'admin@example.com')->first();
        // if ($admin) {
        //     $admin->assignRole('admin');
        // }

        $doctor = User::where('email', 'svizcarragradilla@gmail.com')->first();
        if ($doctor) {
            $doctor->assignRole('doctor');
        }
    }
}
