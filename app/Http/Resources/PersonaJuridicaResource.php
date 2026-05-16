<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para transformar la respuesta del modelo PersonaJuridica.
 * La clave de acceso solo se incluye si está visible en el modelo.
 */
class PersonaJuridicaResource extends JsonResource
{
    /**
     * Transforma el recurso en un array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            "id_persona_juridica" => $this->id_persona_juridica,
            "id_usuario" => $this->id_usuario,
            "ruc" => $this->ruc,
            "razon_social" => $this->razon_social,
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
