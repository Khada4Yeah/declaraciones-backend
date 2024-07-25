<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonaJuridica extends Model
{
    use HasFactory;

    protected $table = "personas_juridicas";
    protected $primaryKey = "id_persona_juridica";
    public $timestamps = false;
    protected $fillable = [
        "id_usuario",
        "ruc",
        "razon_social",
        "clave_acceso",
        "informacion_adicional",
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, "id_usuario", "id_usuario");
    }
}