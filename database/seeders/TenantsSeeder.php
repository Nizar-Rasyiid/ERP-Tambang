<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = [            
            [
                'name'      => 'Utama',
                'token'     => 'ERPKJP',
                'database'  => 'erptambang',
                'status'    => 'active',
            ]
        ];

        foreach ($tenant as $ten) {
            \App\Models\Tenants::create($ten);
        }
    }
}
