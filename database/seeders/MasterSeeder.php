<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //ROLES
        DB::table('roles')->updateOrInsert(['id' => 1], [
            'name'       => 'SUPER_ADMIN',
            'created_at' => Carbon::now(),
        ]);
        DB::table('roles')->updateOrInsert(['id' => 2], [
            'name'       => 'ADMIN',
            'created_at' => Carbon::now(),
        ]);
        DB::table('roles')->updateOrInsert(['id' => 3], [
            'name'       => 'TECNICO',
            'created_at' => Carbon::now(),
        ]);
        DB::table('roles')->updateOrInsert(['id' => 4], [
            'name'       => 'CLIENTE',
            'created_at' => Carbon::now(),
        ]);

        DB::table('roles')->updateOrInsert(['id' => 5], [
            'name'       => 'GERENTE',
            'created_at' => Carbon::now(),
        ]);
        //USER
        DB::table('users')->updateOrInsert(['id' => 1], [
            'name'       => 'ADMIN ADMIN',
            'ci'         => 0,
            'status'     => 1,
            'role_id'    => 1,
            'email'      => 'admin@admin.com',
            'password'   => bcrypt('password'),
            'created_at' => Carbon::now(),
        ]);
    }
}
