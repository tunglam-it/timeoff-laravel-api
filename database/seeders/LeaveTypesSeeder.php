<?php

namespace Database\Seeders;

use App\Models\LeaveTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name'=>'Sức khoẻ'
            ],[
                'name'=>'Gia đình'
            ],[
                'name'=>'Tôn giáo'
            ],[
                'name'=>'Phương tiện đi lại'
            ],[
                'name'=>'Tâm lý'
            ],[
                'name'=>'Học tập'
            ],
        ];

        foreach ($data as $datum){
            LeaveTypes::create($datum);
        }
    }
}
