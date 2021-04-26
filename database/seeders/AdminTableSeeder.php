<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\AdminSmsApi;


class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin =  Admin::create([
            'name'              => ucfirst('admin'),
            'username'           => 'sync'.Str::random(2).''.ucfirst('admin'),
            'email'             => 'admin'.'@synctechsol.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('1234567890'),
            'api_token'         => Str::random('20'),
            'has_sms'           => 50000,
        ]);
            AdminSmsApi::create([
                'admin_id'       => $admin->id,
                'api_url'       => 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?',
                'api_username'  => 'synctech',
                'api_password'  => 'AAUKHlNrDzuEHqqE9yVO%2bUKQlEwe94F9npa7WmUWJ1q1rwe7shghLUEs1jctgTbxPQ%3d%3d',
            ]);
    }
}
