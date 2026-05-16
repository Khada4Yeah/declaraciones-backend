<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para transformar la respuesta del modelo PersonaNatural.
 * La clave de acceso solo se incluye si está visible en el modelo.
 */
class PersonaNaturalResource extends JsonResource
{
    /**
     * Transforma el recurso en un array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            "id_persona_natural" => $this->id_persona_natural,
            "id_usuario" => $this->id_usuario,
            "identificacion" => $this->identificacion,
            "nombres" => $this->nombres,
            "apellido_p" => $this->apellido_p,
            "apellido_m" => $this->apellido_m,
            "informacion_adicional" => $this->informacion_adicional,
            "usuario" => new UsuarioResource($this->whenLoaded("usuario")),
        ];

        // Solo incluir clave_acceso si fue hecha visible explícitamente
        if (!in_array("clave_acceso", $this->getHidden())) {
            $data["clave_acceso"] = $this->clave_acceso;
        }

        return $data;
    }
}
