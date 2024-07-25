<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class Administrador extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = "administradores";
    protected $primaryKey = "id_administrador";
    public $timestamps = false;
    protected $fillable = [
        "id_usuario",
        "nombres",
        "apellido_p",
        "apellido_m",
        "clave",
    ];
    protected $hidden = ["clave"];

    public function usuario()
    {
        return $this->hasOne(Usuario::class, "id_usuario", "id_usuario");
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}