<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'created_at' => '2021-05-25 07:48:50',
                'email' => 'franecki.colin@example.net',
                'id' => 1,
                'name' => 'Ronny Ziemann',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'remember_token' => 'n0rBPnDoRM',
                'updated_at' => '2021-05-25 07:48:50',
                'verification_token' => NULL,
                'verified' => 0,
            ),
            1 => 
            array (
                'created_at' => '2021-05-25 07:48:50',
                'email' => 'mmills@example.net',
                'id' => 2,
                'name' => 'Dr. Cathy Corwin',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'remember_token' => 'vHEZb18F79',
                'updated_at' => '2021-05-25 07:48:50',
                'verification_token' => NULL,
                'verified' => 0,
            ),
        ));
        
        
    }
}