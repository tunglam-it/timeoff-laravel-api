<?php

namespace Database\Seeders;

use App\Models\Leaves;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Database\Seeder;

class LeavesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Container::getInstance()->make(Generator::class);
        for ($i = 0; $i < 500; $i++) {
            for ($v = 0; $v < 1000; $v++) {
                $data[] = [
                    'employee_id' => $faker->randomNumber,
                    'type' => $faker->numberBetween(1, 3),
                    'start_date' => $faker->dateTimeThisMonth,
                    'end_date' => $faker->dateTimeThisMonth,
                    'reason' => $faker->realText,
                    'estimate' => $faker->numberBetween(1, 100),
                ];
            }
            $chunks = array_chunk($data, 1000);
            foreach ($chunks as $chunk) {
                Leaves::query()->insert($chunk);
            }
        }
    }
}
