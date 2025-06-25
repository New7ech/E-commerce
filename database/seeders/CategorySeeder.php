<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Électronique', 'slug' => Str::slug('Électronique')],
            ['name' => 'Mode', 'slug' => Str::slug('Mode')],
            ['name' => 'Agroalimentaire', 'slug' => Str::slug('Agroalimentaire')],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
