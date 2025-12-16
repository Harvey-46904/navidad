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
        Schema::create('regalos', function (Blueprint $table) {
             $table->id(); // int unsigned auto_increment
            $table->integer('id_usuario')->nullable();

            $table->string('nombre')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('donde')->nullable();

            $table->string('regalo')->nullable(); // imagen regalo
            $table->string('lugar')->nullable();  // imagen lugar

            $table->string('estado')->nullable()->default('disponible');

            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regalos');
    }
};
