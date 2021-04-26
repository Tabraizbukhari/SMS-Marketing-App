<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UsersData;
use App\Models\ResellerCustomer;
use App\Models\User;
use App\Models\SmsApi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
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
                'first_name' => 'synctech',
                'last_name' => 'synctech',
                'email' => 'synctech@gmail.com',
                'api_token' => 'yZJGkhnIUBYlbb2fKGvWcX',
                'password' => '$2y$10$qcubYpScCB1v2yvHthPXPuJng5a9NtDEoT8WLqsGvicyr0dP19H1m',
                'username' => 'synctech_masking',
                'register_as' => 'reseller',
                'user_api' => array('name' => 'synctech','api_url' => 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?','api_username' => 'synctech','api_password' => 'AAUKHlNrDzuEHqqE9yVO%2bUKQlEwe94F9npa7WmUWJ1q1rwe7shghLUEs1jctgTbxPQ%3d%3d'),
                'userData'  => [
                    'has_sms'       =>    10606,
                    'price_per_sms' =>    2,
                    'login_url'     =>    '',
                ],
                'customers' => [
                    [
                        'first_name' => 'testing',
                        'last_name' => 'testing',
                        'email' => 'testing@synctechsol.com',
                        'api_token' => 'Fco9LslMXI1Po1LqrKJHXu',
                        'password' => '$2y$10$mVtndo4YlliV5gkoVCxcc.jtTkKACoXxepQequpnu8B1d2y1qAU8e',
                        'username' => 'sync_testing',
                        'register_as' => 'customer',
                        'user_api' => array('name' => 'testing','api_url' => 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?','api_username' => 'synctech','api_password' => 'AAUKHlNrDzuEHqqE9yVO%2bUKQlEwe94F9npa7WmUWJ1q1rwe7shghLUEs1jctgTbxPQ%3d%3d'),
                        'userData'  => [
                            'has_sms'       =>    6,
                            'price_per_sms' =>    0.25,
                            'login_url'     =>    '',
                        ],
                    ],

                    [
                        'first_name' => 'hcs',
                        'last_name' => 'hcs',
                        'email' => 'sohaib@hcsgroup.io',
                        'api_token' => 'l5XkaU8tWcIUpIit6IMUoI',
                        'password' => '$2y$10$z/xIAxE.X1iZ826L0f/1COI5nS7XS/yaaDGyHNnCgFLjfaxOIUrrC',
                        'username' => 'sync_hcs',
                        'register_as' => 'customer',
                        'user_api' => array('name' => 'hcs','api_url' => 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?','api_username' => 'hcs','api_password' => 'ANtZyB%2bxduV0bh0YxCzFM%2fjPg4tuMvgD3uyQQy3UIbde3Bi4v%2b7NDfCrpOLK94YeOQ%3d%3d'),
                        'userData'  => [
                            'has_sms'       =>    1000,
                            'price_per_sms' =>     0.25,
                            'login_url'     =>    '',
                        ],
                    ],


                ],
            ],

            [
                'first_name' => 'synctechcode',
                'last_name' => 'synctechcode',
                'email' => 'synctechcodex@gmail.com',
                'api_token' => '8eO3Jpg4981o29iw3CFYaui',
                'password' => '$2y$10$qcubYpScCB1v2yvHthPXPuJng5a9NtDEoT8WLqsGvicyr0dP19H1m',
                'username' => 'synctech_code',
                'register_as' => 'reseller',
                'type'  => 'code',
                'user_api' => array('name' => 'synctechcode','api_url' => 'http://smsctp1.eocean.us:24555/api?','api_username' => 'wrong','api_password' => 'wrong'),
                'userData'  => [
                    'has_sms'       =>    4928,
                    'price_per_sms' =>    1,
                    'login_url'     =>    '',
                ],
                'customers' => [
                    [
                        'first_name' => 'lubricant',
                        'last_name' => 'lubricant',
                        'email' => 'lubricant@synctechsol.com',
                        'api_token' => '5Jb5WJuC8IchqYYxOoIe',
                        'password' => '$2y$10$oE3G/GVprl7Te/2ZRLeB6Olvah9/Orng6K44EupcsBC3plowM.pTK',
                        'username' => 'sync_lubricant',
                        'register_as' => 'customer',
                        'type'  => 'code',
                         'user_api' => array('name' => 'lubricant','api_url' => 'http://smsctp3.eocean.us:24555/api?','api_username' => 'butt_lubricants','api_password' => 'pak@456'),
                        'userData'  => [
                            'has_sms'       =>    10000,
                            'price_per_sms' =>    0.75,
                            'login_url'     =>    '',
                        ],
                    ],

                    [
                        'first_name' => 'jasb',
                        'last_name' => 'jasb',
                        'email' => 'jibran@jasbconsulting.com',
                        'api_token' => '9U2MyXNB9wmrEXN7d',
                        'password' => '$2y$10$zy/A1JQUXTLVTVcTdrm0Pe1DWoQh9ougJwBRVSnJk1o7rm.51S.dW',
                        'username' => 'sync_jasb',
                        'register_as' => 'customer',
                        'type'  => 'code',
                         'user_api' => array('name' => 'jasb','api_url' => 'http://smsctp1.eocean.us:24555/api?','api_username' => 'jasb_99095','api_password' => 'shFSr457'),
                        'userData'  => [
                            'has_sms'       =>    1,
                            'price_per_sms' =>    0.5,
                            'login_url'     =>    '',
                        ],
                    ],
            
                    [
                        'first_name' => 'testingcode',
                        'last_name' => 'testingcode',
                        'email' => 'shomail@synctechsol.com',
                        'api_token' => 'NdloySFyRZziBBO0u3cO',
                        'password' => '$2y$10$Sa3sZizRhlXiDchxTwW63ufwKAefTzGbBylpILTh/ZoBln6PxvP2y',
                        'username' => 'sync_testingcode',
                        'register_as' => 'customer',
                        'type'  => 'code',
                         'user_api' => array('name' => 'testingcode','api_url' => 'http://smsctp1.eocean.us:24555/api?','api_username' => 'jasb_99095','api_password' => 'shFSr457'),
                        'userData'  => [
                            'has_sms'       =>    19,
                            'price_per_sms' =>    1,
                            'login_url'     =>    '',
                        ],
                    ],
            
                    
                    [
                        'first_name' => 'grocery_done',
                        'last_name' => 'grocery_done',
                        'email' => 'grocerydone@synctechsol.com',
                        'api_token' => 'gXn58YnUjEZNGqKfq1wHorL',
                        'password' => '$2y$10$CQ9KlEvl9AunM5aEaWwyLelH3lreCJulLe9LCbyrIDei7YALZZp3O',
                        'username' => 'sync_grocerydone',
                        'register_as' => 'customer',
                        'type'  => 'code',
                         'user_api' => array('name' => 'grocery_done','api_url' => 'http://smsctp3.eocean.us:24555/api?','api_username' => 'butt_lubricants','api_password' => 'pak@456'),
                        'userData'  => [
                            'has_sms'       =>    13445,
                            'price_per_sms' =>    0.36,
                            'login_url'     =>    '',
                        ],
                    ],
            
    
                    [
                        'first_name' => 'islamic_international',
                        'last_name' => 'islamic_international',
                        'email' => 'erum.jamil@iiu.edu.pk',
                        'api_token' => 'WoU9vXm2OeQlsfBBI8u6qPe',
                        'password' => '$2y$10$CTyy0rrC5shnJvHYkIIas.SqNyQYN4Y138BflonJaimXu/myxEMOm',
                        'username' => 'sync_IU',
                        'register_as' => 'customer',
                        'type'  => 'code',
                         'user_api' => array('name' => 'islamic_international','api_url' => 'http://smsctp1.eocean.us:24555/api?','api_username' => 'synctech','api_password' => 'HtsNEdh4'),
                        'userData'  => [
                            'has_sms'       =>    102,
                            'price_per_sms' =>    0.23,
                            'login_url'     =>    '',
                        ],
                    ],
            
                ],
            ],    
         
            [
                'first_name' =>'Asad',
                'last_name' =>'Asad',
                'email' =>'asad@aspirantedge.com',
                'api_token' =>'ANhOGKqyzREX7Yi8MrRC3',
                'password' =>'$2y$10$GJIguOUTBP7l3uzLN961LesMbOadghdGSEmNBMeucKIeTLwtXSUo6',
                'username' =>'sync_asad',
                'register_as' => 'reseller',
                'user_api' => array('name' => 'Asad','api_url' => 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?','api_username' => 'synctech','api_password' => 'AAUKHlNrDzuEHqqE9yVO%2bUKQlEwe94F9npa7WmUWJ1q1rwe7shghLUEs1jctgTbxPQ%3d%3d'),
                'userData'  => [
                    'has_sms'       =>    9904,
                    'price_per_sms' =>    0.2,
                    'login_url'     =>    'sms.aspirantedge.com',
                    'logo_img'     =>    'public/logos/2/KbvsodJE9ZIXc8kZz0LxflJNzHjBSMFDAAiDMhr4.png',

                ],
                'customers' => [
                    [
                        'first_name' => 'global',
                        'last_name' => 'global',
                        'username' => 'sync_global',
                        'email' => 'osama.aspirantedge@gmail.com',
                        'password' => '$2y$10$vxbCQb02Cb89eo5p4YHrnOIPoGA.pzCwNSnyOpesB7/XBvp3BopZm',
                        'register_as' => 'customer',
                        'api_token' => '9Jm9LvPBGmPuL1k8',
                        'register_as' => 'customer',
                        'user_api' => array('name' => 'global','api_url' => 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?','api_username' => NULL,'api_password' => NULL),
                        'userData'  => [
                                    'has_sms'       =>    8,
                                    'price_per_sms' =>    0.2,
                                ],

                    ],
            
                    [
                        'first_name' => 'Al huda',
                        'last_name' => 'Al huda',
                        'username' => 'sync_alhuda',
                        'email' => 'oshe1992@gmail.com',
                        'password' => '$2y$10$LvgHd2cmPUHCseUy9v1AmuGC9F2/n8WrqCssmtU3875rKUfl8TOZ6',
                        'register_as' => 'customer',
                        'api_token' => 'LzKNAREsJ2V2HhVhj',
                        'register_as' => 'customer',
                        'user_api' => array('name' => 'Al huda','api_url' => 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?','api_username' => NULL,'api_password' => NULL),
                       
                        'userData'  => [
                                    'has_sms'       =>    4,
                                    'price_per_sms' =>    0,
                                ],
                    ],
            
                    [
                        'first_name' => 'Mahir',
                        'last_name' => 'Mahir',
                        'username' => 'sync_mahir',
                        'email' => 'osama.aspirantedges@gmail.com',
                        'password' => '$2y$10$GPD4OSrzVu52J4Yk.rUu6ei./WphboStf.VSC0kFFzOcR7QngMhXK',
                        'register_as' => 'customer',
                        'user_api' => array('name' => 'Mahir','api_url' => 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?','api_username' => NULL,'api_password' => NULL),
                        'api_token' => 'JptgVmwe2MYFbUYW',
                        'userData'  => [
                                    'has_sms'       =>    5,
                                    'price_per_sms' =>    0,
                                ],
                    ],
            
                    [
                        'first_name' => 'Ahmad',
                        'last_name' => 'Ahmad',
                        'username' => 'sync_ahmad',
                        'email' => 'hangreebrand@gmail.com',
                        'password' => '$2y$10$oWFzWyYKznkZqojUGM1YcerjEyb.DteTsIPEcCfaXlB3d6eP0U5yC',
                        'api_token' => '3fdAPkZd5ey7y7BsoA',
                        'register_as' => 'customer',
                        'user_api' => array('name' => 'Ahmad','api_url' => 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?','api_username' => NULL,'api_password' => NULL),
                        'userData'  => [
                                'has_sms'       =>    0,
                                'price_per_sms' =>   0,
                            ],
                    ],
            
                    [
                        'first_name' => 'Ginsoy',
                        'last_name' => 'Ginsoy',
                        'username' => 'ginsoy',
                        'email' => 'saud.sahni@gmail.com',
                        'password' => '$2y$10$QxiMszBmv4jtMMATzYYSEeXhaIAJ9.BwBpylYNWReL0y6/frKA8Q.',
                        'api_token' => 'Q8DZbMKtL7KzybGm8ExopPjhYET8UF',
                        'register_as' => 'customer',
                        'user_api' => array('name' => 'Ginsoy','api_url' => 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?','api_username' => NULL,'api_password' => NULL),
                        'userData'  => [
                                    'has_sms'       =>    10,
                                    'price_per_sms' =>    0,
                                ],
                    ],
            
                    [
                        'first_name' => 'Creek Mart',
                        'last_name' => 'Creek Mart',
                        'username' => 'creekmart',
                        'email' => 'mustafajavedkhanani@outlook.com',
                        'password' => '$2y$10$bS9yqiYXhNnx0fCEwBkWRemqMU4Zh2TUJTwD9fU06JXHipeaKgZly',
                        'api_token' => 'aSTNprUatB5BOU6fmPSifC1vS8XeE3',
                        'register_as' => 'customer',
                        'user_api' => array('name' => 'Creek Mart','api_url' => 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?','api_username' => NULL,'api_password' => NULL),
                        'userData'  => [
                                'has_sms'       =>    102,
                                'price_per_sms' =>    0.23,
                            ],
                    ], 
                          
                    [
                        'first_name' => 'alkhaleej',
                        'last_name' => 'alkhaleej',
                        'email' => 'alkhaleejclinics@gmailcom',
                        'api_token' => 'kX6PKFAerI0M922BONPxVT2hPwJkGU',
                        'password' => '$2y$10$iJStGeuHMwgg2.YR0/63h.irXKxkwWum037axcd.NOpgBG/25RuCG',
                        'username' => 'alkhaleejclinics',
                        'register_as' => 'customer',
                        'user_api' => array('name' => 'alkhaleej','api_url' => 'https://sms.synctechsol.com/APIManagement/API/RequestAPI?','api_username' => NULL,'api_password' => NULL),
                        'userData'  => [
                            'has_sms'           =>    9,
                            'price_per_sms'     =>    0.24,
                            'login_url'         =>    '',
                            'Invoice_charges'   =>    24000,
                        ],
                    ],     
                ],
            ],

        ];
    

        foreach ($users as $user) {
            $stored_data = User::create([
                                'first_name'        => $user['first_name'],
                                'last_name'         => $user['last_name'],
                                'email'             => $user['email'],
                                'username'          => $user['username'],
                                'email_verified_at' => Carbon::now(),
                                'password'          => $user['password'],
                                'api_token'         => $user['api_token'],
                                'type'              => (isset($user['type']))? 'code':'masking', 
                                'register_as'       => 'reseller',
                            ]);
            if(isset($user['customers'])){
                foreach ($user['customers'] as $customer) {
                $stored_data2 =  User::create([
                        'first_name'        => $customer['first_name'],
                        'last_name'         => $customer['last_name'],
                        'email'             => $customer['email'],
                        'username'          => $customer['username'],
                        'email_verified_at' => Carbon::now(),
                        'password'          => $customer['password'],
                        'api_token'         => $customer['api_token'],
                        'type'              => (isset($user['type']))? 'code':'masking', 
                        'register_as'       => 'customer',
                    ]);
                    $customer['userData']['user_id'] = $stored_data2->id;
                    UsersData::create($customer['userData']);
        
                    SmsApi::create([
                        'user_id'       => $stored_data2->id,
                        'api_url'       => $customer['user_api']['api_url'],
                        'api_username'  => $customer['user_api']['api_username'],
                        'api_password'  => $customer['user_api']['api_password'], 
                    ]);

                    ResellerCustomer::create([
                    'user_id'  =>  $stored_data->id,
                    'customer_id' => $stored_data2->id,
                    ]);
                }
            }

            $user['userData']['user_id'] = $stored_data->id;
            UsersData::create($user['userData']);

            SmsApi::create([
                'user_id'       => $stored_data->id,
                'api_url'       => $user['user_api']['api_url'],
                'api_username'  => $user['user_api']['api_username'],
                'api_password'  => $user['user_api']['api_password'], 
            ]);
        }
    }
}
