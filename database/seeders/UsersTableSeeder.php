<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nome' => 'Kleiton Ferreira',
            'email' => 'kleiton.silva@caraguatatuba.sp.gov.br',
            'cpf' => '09906847417',
            'registro'=> '25606',
            'password'=> Hash::make('password1!'),
            'grupo_id'=>'1',
            'setor_id'=>'1'
        ]);
    }
}
