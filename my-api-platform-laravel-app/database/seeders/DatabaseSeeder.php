<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->call(DemoDataSeeder::class);
    }
}

