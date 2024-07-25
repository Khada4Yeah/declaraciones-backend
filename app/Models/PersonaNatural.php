<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonaNatural extends Model
{
    use HasFactory;

    protected $table = "personas_naturales";
    protected $primaryKey = "id_persona_natural";
    public $timestamps = false;
    protected $fillable = [
        "id_usuario",
        "identificacion",
        "nombres",
        "apellido_p",
        "apellido_m",
        "clave_acceso",
        "informacion_adicional",
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, "id_usuario", "id_usuario");
    }
}