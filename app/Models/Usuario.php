<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = "usuarios";
    protected $primaryKey = "id_usuario";
    protected $dateFormat = "Y-m-d\TH:i:s";

    protected $fillable = ["correo_electronico", "celular"];

    public function administrador()
    {
        return $this->hasOne(Administrador::class, "id_usuario", "id_usuario");
    }

    public function personaJuridica()
    {
        return $this->hasOne(
            PersonaJuridica::class,
            "id_usuario",
            "id_usuario",
        );
    }

    public function personaNatural()
    {
        return $this->hasOne(PersonaNatural::class, "id_usuario", "id_usuario");
    }
}