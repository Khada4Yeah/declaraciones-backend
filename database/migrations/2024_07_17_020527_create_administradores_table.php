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
        Schema::create("administradores", function (Blueprint $table) {
            $table->id("id_administrador");
            $table->bigInteger("id_usuario");
            $table->string("nombres");
            $table->string("apellido_p");
            $table->string("apellido_m");
            $table->string("clave");
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
        Schema::dropIfExists("administradores");
    }
};
