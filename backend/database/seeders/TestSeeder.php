<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Video;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create test user
        $user = User::create([
            'first_name' => 'fnametest',
            'last_name' => 'lnametest',
            'username' => 'unametest',
            'email' => 'testuser@example.com',
            'password' => Hash::make('validpassword'),
        ]);

        // create test video associated with the user
        $video = Video::factory()
            ->for($user)
            ->create();
    }
}
