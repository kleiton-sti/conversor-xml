<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setores', function (Blueprint $table) {
            $table->id();
            $table->string('setor', 50);
            $table->timestamps();
            $table->softDeletes();

            $table->bigInteger('unidade_id')->unsigned()->comment('Chave estrangeira que faz a relacao com a tabela unidades');

            $table->foreign('unidade_id')
                ->references('id')
                ->on('unidades');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setores');
    }
};
