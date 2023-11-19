<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert(
            [
                'name' => 'admin',
                'email' => env('MAIL_ADMIN', 'admin@gmail.com'),
                'password' => bcrypt('Amela@1234'),
                'group' => User::GROUP['admin'],
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),

            ]
        );
    }
}
