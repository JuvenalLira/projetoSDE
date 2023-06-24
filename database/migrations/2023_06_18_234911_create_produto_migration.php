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
        Schema::create('produtos', function (Blueprint $table){
            $table->id();
            $table->string('nome');
            $table->text('descricao');
            $table->string('codigoDeBarras');
            $table->string('peso');
            $table->string('altura');
            $table->string('profundidade');
            $table->string('precoCompra');
            $table->string('precoVenda');

            $table->foreing('fonecedorId')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExistis('produtos');
    }
};
