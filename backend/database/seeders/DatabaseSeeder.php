<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $admin = new User();
        $admin->email = 'admin@admin.com';
        $admin->name = 'admin';
        $admin->password = Hash::make('password');
        $admin->save();
        User::factory(100)->create();
    }
}
