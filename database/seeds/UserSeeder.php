<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            'avatar' => '/uploads/users/ali-karabay-2020-05-24-232607.jpg',
            'name' => 'Ali Karabay',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'roll_id' => '1',
            'roll' => 'admin',
            'user_status' => 'on',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
