<?php

namespace Database\Seeders;

use App\Models\Employees;
use Illuminate\Container\Container;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Generator;

class EmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Container::getInstance()->make(Generator::class);
        for($i=0; $i< 500; $i++) {
            for($v=0; $v< 1000; $v++) {
                $data[] = [
                    'name' => $faker->name,
                    'email' => $faker->email,
                    'password' => Hash::make('123456789'),
                ];
            }
            $chunks = array_chunk($data, 1000);
            foreach ($chunks as $chunk) {
                Employees::query()->insert($chunk);
            }
        }
    }
}
