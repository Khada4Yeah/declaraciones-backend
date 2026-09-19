<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Crea la tabla 'archivos' para almacenar la metadata de los archivos
     * subidos por cada usuario, organizados por año.
     */
    public function up(): void
    {
        Schema::create("archivos", function (Blueprint $table) {
            $table->id("id_archivo");
            $table->bigInteger("id_usuario");
            $table->string("file_name", 255);
            $table->string("file_path", 512);
            $table->bigInteger("file_size");
            $table->string("file_extension", 20);
            $table->string("mime_type", 100);
            $table->integer("year");
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign("id_usuario")
                ->references("id_usuario")
                ->on("usuarios");

            $table->index(["id_usuario", "year"]);
        });
    }

    /**
     * Revierte la migración eliminando la tabla 'archivos'.
     */
    public function down(): void
    {
        Schema::dropIfExists("archivos");
    }
};
