<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Eloquent para la tabla 'archivos'.
 * Representa un archivo subido por un usuario, organizado por año.
 * Utiliza SoftDeletes para borrado lógico.
 */
class Archivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "archivos";
    protected $primaryKey = "id_archivo";

    protected $fillable = [
        "id_usuario",
        "file_name",
        "file_path",
        "file_size",
        "file_extension",
        "mime_type",
        "year",
    ];

    /**
     * Relación: el archivo pertenece a un usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, "id_usuario", "id_usuario");
    }
}
