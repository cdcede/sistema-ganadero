<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Role Types
         *
         */
        $RoleItems = [
            [
                'name'        => 'SuperAdmin',
                'slug'        => 'superadmin',
                'description' => 'Rol Super Admin',
                'level'       => 5,
            ],
            [
                'name'        => 'Admin',
                'slug'        => 'admin',
                'description' => 'Rol Admin',
                'level'       => 4,
            ],
            /* [
                'name'        => 'Ganadero',
                'slug'        => 'ganadero',
                'description' => 'Rol Ganadero',
                'level'       => 1,
            ], */
            [
                'name'        => 'Trabajador',
                'slug'        => 'trabajador',
                'description' => 'Rol Trabajador',
                'level'       => 1,
            ],
        ];

        /*
         * Add Role Items
         *
         */
        foreach ($RoleItems as $RoleItem) {
            //$newRoleItem = config('roles.models.role')::where('slug', '=', $RoleItem['slug'])->first();
            $newRoleItem = null;
            if ($newRoleItem === null) {
                $newRoleItem = config('roles.models.role')::create([
                    'name'          => $RoleItem['name'],
                    'slug'          => $RoleItem['slug'],
                    'description'   => $RoleItem['description'],
                    'level'         => $RoleItem['level'],
                ]);
            }
        }
    }
}
