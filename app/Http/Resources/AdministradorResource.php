<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para transformar la respuesta del modelo Administrador.
 * Oculta la clave de autenticación por seguridad.
 */
class AdministradorResource extends JsonResource
{
    /**
     * Transforma el recurso en un array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id_administrador" => $this->id_administrador,
            "id_usuario" => $this->id_usuario,
            "nombres" => $this->nombres,
            "apellido_p" => $this->apellido_p,
            "apellido_m" => $this->apellido_m,
            "usuario" => new UsuarioResource($this->whenLoaded("usuario")),
        ];
    }
}
