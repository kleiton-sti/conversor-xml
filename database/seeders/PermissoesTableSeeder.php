<?php

namespace Database\Seeders;

use App\Models\Permissao;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissoesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permissao::create([
            'id'=>'1',
            'nome_permissao' => 'consultar.usuario',
        ]);

        Permissao::create([
            'id'=>'2',
            'nome_permissao' => 'gerenciar.usuario',
        ]);

        Permissao::create([
            'id'=>'3',
            'nome_permissao' => 'deletar_pessoa',
        ]);

        Permissao::create([
            'id'=>'4',
            'nome_permissao' => 'cadastrar_entidade',
        ]);

        Permissao::create([
            'id'=>'5',
            'nome_permissao' => 'visualizar_entidade',
        ]);

        Permissao::create([
            'id'=>'6',
            'nome_permissao' => 'editar_entidade',
        ]);

        Permissao::create([
            'id'=>'7',
            'nome_permissao' => 'inativar_entidade',
        ]);

        Permissao::create([
            'id'=>'8',
            'nome_permissao' => 'pesquisar_entidade',
        ]);





        Permissao::create([
            'id'=>'9',
            'nome_permissao' => 'cadastrar_unidade',
        ]);

        Permissao::create([
            'id'=>'10',
            'nome_permissao' => 'visualizar_unidade',
        ]);

        Permissao::create([
            'id'=>'11',
            'nome_permissao' => 'editar_unidade',
        ]);

        Permissao::create([
            'id'=>'12',
            'nome_permissao' => 'inativar_unidade',
        ]);

        Permissao::create([
            'id'=>'13',
            'nome_permissao' => 'pesquisar_unidade',
        ]);


        

        Permissao::create([
            'id'=>'14',
            'nome_permissao' => 'cadastrar.setor',
        ]);

        Permissao::create([
            'id'=>'15',
            'nome_permissao' => 'visualizar.setor',
        ]);

        Permissao::create([
            'id'=>'16',
            'nome_permissao' => 'editar.setor',
        ]);

        Permissao::create([
            'id'=>'17',
            'nome_permissao' => 'inativar.setor',
        ]);

        Permissao::create([
            'id'=>'18',
            'nome_permissao' => 'pesquisar.setor',
        ]);

       

        
    }
}
