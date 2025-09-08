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
        Schema::create('unidades', function (Blueprint $table) {
            $table->id();
            $table->string('unidade',50);
            $table->timestamps();
            $table->softDeletes();

            $table->bigInteger('entidade_id')->unsigned()->comment('Chave estrangeira que faz a relacao com a tabela entidades');

            $table->foreign('entidade_id')
                ->references('id')
                ->on('entidades');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades');
    }
};
