<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("personas_juridicas", function (Blueprint $table) {
            $table->id("id_persona_juridica");
            $table->bigInteger("id_usuario");
            $table->string("ruc", 13)->unique();
            $table->string("razon_social");
            $table->string("clave_acceso");
            $table->text("informacion_adicional")->nullable();
            $table
                ->foreign("id_usuario")
                ->references("id_usuario")
                ->on("usuarios");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("personas_juridicas");
    }
};