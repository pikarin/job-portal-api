<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('skills')->insert([
            ['name' => 'PHP'],
            ['name' => 'Laravel'],
            ['name' => 'MySQL'],
            ['name' => 'Python'],
            ['name' => 'Javascript'],
            ['name' => 'C++'],
            ['name' => 'C'],
            ['name' => 'C#'],
            ['name' => 'Swift'],
            ['name' => 'Objective-C'],
            ['name' => 'Go'],
            ['name' => 'Rust'],
            ['name' => 'TypeScript'],
            ['name' => 'HTML'],
            ['name' => 'CSS'],
            ['name' => 'SQL'],
            ['name' => 'Backend'],
            ['name' => 'Frontend'],
        ]);
    }
}
