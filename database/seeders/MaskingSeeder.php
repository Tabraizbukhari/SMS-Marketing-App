<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Masking;
class MaskingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        $data = [
            'Fair View',
            'AspirantLab',
            'SyncTech',
            'UCAAZ',
            'Ginsoy',
            'Creek Mart',
            'AL KHALEEJ',
            'Ginsoy Live',
            'Rahyab',
            'HOME TUTION',
        ];

        foreach ($data as $v) {
            Masking::create([
                'title' => $v,
            ]);
        }
    }
}
