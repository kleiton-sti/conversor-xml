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
        Schema::create('grupos_permissoes', function (Blueprint $table) {
            $table->id();      
            $table->bigInteger('grupo_id')->unsigned();
            $table->bigInteger('permissao_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('grupo_id')
                  ->references('id')
                  ->on('grupos');        
            $table->foreign('permissao_id')
                  ->references('id')
                  ->on('permissoes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos_permissoes');
    }
};
