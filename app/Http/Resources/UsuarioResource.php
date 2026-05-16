<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para transformar la respuesta del modelo Usuario.
 */
class UsuarioResource extends JsonResource
{
    /**
     * Transforma el recurso en un array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id_usuario" => $this->id_usuario,
            "correo_electronico" => $this->correo_electronico,
            "celular" => $this->celular,
        ];
    }
}
