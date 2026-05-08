<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Eletrônicos', 'description' => 'Produtos eletrônicos gerais'],
            ['name' => 'Informática', 'description' => 'Computadores e periféricos'],
            ['name' => 'Periféricos', 'description' => 'Periféricos de computador'],
            ['name' => 'Acessórios', 'description' => 'Acessórios diversos'],
            ['name' => 'Software', 'description' => 'Softwares e licenças'],
            ['name' => 'Outros', 'description' => 'Outros produtos'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate($category);
        }
    }
}
