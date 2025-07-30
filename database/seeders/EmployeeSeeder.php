<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employee = [
            [
                'employee_code'     => '1001',
                'employee_name'     => 'Engineer',
                'employee_phone'    => '0812918231',
                'employee_email'    => 'admin@gmail.com',
                'employee_address'  => 'jln. Jeruk Purut 12',
                'employee_salary'   => '1000000',
                'employee_end_contract' => now(),
                'bpjs_kesehatan'    => '8821938123',
                'bpjs_ketenagakerjaan'  => '2938192831',
                'employee_nik'      => '28391823',
                'employee_position' => 'Engineer',
            ]           
        ];

        foreach ($employee as $emp) {
            \App\Models\Employee::create($emp);
        }
    }
}
