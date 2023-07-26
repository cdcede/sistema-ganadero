<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userRole = config('roles.models.role')::where('name', '=', 'Ganadero')->first();
        $adminRole = config('roles.models.role')::where('name', '=', 'Admin')->first();
        $superAdminRole = config('roles.models.role')::where('name', '=', 'SuperAdmin')->first();
        $permissions = config('roles.models.permission')::all();

        /*
         * Add Users
         *
         */
        if (config('roles.models.defaultUser')::where('email', '=', 'admin@admin.com')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'username'     => 'superadmin',
                'first_name'     => 'Doris',
                'last_name'     => 'Borja',
                'email'    => 'dorisborja98@gmail.com',
                'password' => bcrypt('password'),
                'status' => true,
            ]);

            $newUser->attachRole($superAdminRole);
            foreach ($permissions as $permission) {
                $newUser->attachPermission($permission);
            }
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'user@user.com')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'username'     => 'admin',
                'first_name'     => 'Cesar',
                'last_name'     => 'Cedeno',
                'email'    => 'cdcede91@gmail.com',
                'password' => bcrypt('password'),
                'status' => true,
            ]);

            $newUser->attachRole($adminRole);
        }
    }
}
