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
        Schema::create("personas_naturales", function (Blueprint $table) {
            $table->id("id_persona_natural");
            $table->bigInteger("id_usuario");
            $table->string("identificacion", 13)->unique();
            $table->string("nombres");
            $table->string("apellido_p")->nullable();
            $table->string("apellido_m")->nullable();
            $table->string("clave_acceso", 512);
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
        Schema::dropIfExists("personas_naturales");
    }
};