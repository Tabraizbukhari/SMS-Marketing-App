<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SmsApi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'admin',
                'type' => 'admin',
            ],
        ];

        foreach ($users as $user) {
            $stored_data = User::create([
                                'name' => ucfirst($user['name']),
                                'email' => (isset($user['email'])) ? $user['email'] : $user['name'].'@example.com',
                                'email_verified_at' => now(),
                                'password' => Hash::make('1234567890'),
                                'api_token' => Str::random('80'),
                                'type' => $user['type'],
                                'sms' => 6000,
                                'price' => 0.1,
                            ]);

            if($user['type'] == 'admin'){
                SmsApi::create([
                    'user_id' => $stored_data->id,
                    'api_url' => 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?',
                    'api_username' => 'synctech',
                    'api_password'  =>'AAUKHlNrDzuEHqqE9yVO%2bUKQlEwe94F9npa7WmUWJ1q1rwe7shghLUEs1jctgTbxPQ%3d%3d' 
                ]);
            }
        }
    }
}
