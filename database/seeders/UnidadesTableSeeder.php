<?php

namespace Database\Seeders;

use App\Models\Unidade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnidadesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unidade::create([
            'unidade' => 'SECRETARIA DE TECNOLOGIA DA INFORMACAO',
            'entidade_id' => '1',
        ]);
    }
}
