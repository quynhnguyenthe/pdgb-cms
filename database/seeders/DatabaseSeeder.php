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
                'email' => env('MAIL_ADMIN', 'admin@gmail.com'),
                'password' => bcrypt('Amela@1234'),
                'group' => User::GROUP['admin'],
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),

            ]
        );

        DB::table('sports_disciplines')->insert(
            [
                [
                    'name' => 'Bóng đá',
                    'number_of_participants' => '14',
                    'number_of_reserves' => '6',
                ],
                [
                    'name' => 'Cầu lông',
                    'number_of_participants' => '4',
                    'number_of_reserves' => '0',
                ],
                [
                    'name' => 'Bóng bàn',
                    'number_of_participants' => '4',
                    'number_of_reserves' => '2',
                ],
                [
                    'name' => 'Bi-a',
                    'number_of_participants' => '4',
                    'number_of_reserves' => '2',
                ],
                [
                    'name' => 'Bi-lắc',
                    'number_of_participants' => '8',
                    'number_of_reserves' => '4',
                ],
            ]
        );
    }
}
