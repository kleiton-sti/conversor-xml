<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class GruposPermissoesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '1'
        ]);
        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '2'
        ]);
        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '3'
        ]);
        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '4'
        ]);
        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '5'
        ]);
        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '6'
        ]);
        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '7'
        ]);

        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '8'
        ]);

        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '9'
        ]);

        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '10'
        ]);

        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '11'
        ]);

        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '12'
        ]);

        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '13'
        ]);

        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '14'
        ]);

        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '15'
        ]);

        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '16'
        ]);

        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '17'
        ]);

        DB::table('grupos_permissoes')->insert([
            'grupo_id' => '1',
            'permissao_id' => '18'
        ]);
    }
}
