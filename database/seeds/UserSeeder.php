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
            'name' => 'Consumer Support',
            "firstname" => "Consumer",
            "lastname" => "Support",
            "birthday" => "1992-03-13",
            "username" => "Admin",
            "mobile" => "+23412345678",
            'email' => 'support@upaychat.com',
            'password' => Hash::make('12345678'),
            'roll_id' => '1',
            'roll' => 'support',
            'user_status' => 'on',
            "fcm_token" => "",
            "locked" => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
