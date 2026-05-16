<?php

namespace App\Services;

use App\Models\PersonaJuridica;
use App\Models\Usuario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * Servicio que encapsula la lógica de negocio de las personas jurídicas.
 * Maneja transacciones, encriptación y relaciones con el modelo Usuario.
 */
class PersonaJuridicaService
{
    /**
     * Obtiene todas las personas jurídicas con su usuario asociado.
     *
     * @return Collection
     */
    public function obtenerTodas(): Collection
    {
        return PersonaJuridica::with("usuario")->get();
    }

    /**
     * Obtiene una persona jurídica por su ID, incluyendo la clave de acceso desencriptada.
     *
     * @param int $id ID de la persona jurídica.
     * @return PersonaJuridica
     */
    public function obtenerPorId(int $id): PersonaJuridica
    {
        $persona = PersonaJuridica::with("usuario")->findOrFail($id);
        $persona->makeVisible("clave_acceso");
        $persona->clave_acceso = Crypt::decryptString($persona->clave_acceso);

        return $persona;
    }

    /**
     * Crea una persona jurídica y su usuario asociado dentro de una transacción.
     *
     * @param array $datos Datos validados del formulario.
     * @return PersonaJuridica Persona jurídica creada con su relación usuario cargada.
     * @throws \Exception Si ocurre un error durante la creación.
     */
    public function crear(array $datos): PersonaJuridica
    {
        return DB::transaction(function () use ($datos) {
            $usuario = Usuario::create([
                "correo_electronico" => $datos["correo_electronico"],
                "celular" => $datos["celular"],
            ]);

            $persona = PersonaJuridica::create([
                "id_usuario" => $usuario->id_usuario,
                "ruc" => $datos["ruc"],
                "razon_social" => $datos["razon_social"],
                "clave_acceso" => Crypt::encryptString($datos["clave_acceso"]),
                "informacion_adicional" => $datos["informacion_adicional"] ?? null,
            ]);

            $persona->load("usuario");

            return $persona;
        });
    }

    /**
     * Actualiza una persona jurídica y su usuario asociado dentro de una transacción.
     *
     * @param int $id ID de la persona jurídica a actualizar.
     * @param array $datos Datos validados del formulario.
     * @return PersonaJuridica Persona jurídica actualizada con su relación usuario cargada.
     * @throws \Exception Si la persona jurídica no existe o si ocurre un error.
     */
    public function actualizar(int $id, array $datos): PersonaJuridica
    {
        $persona = PersonaJuridica::findOrFail($id);

        return DB::transaction(function () use ($persona, $datos) {
            $usuario = Usuario::findOrFail($persona->id_usuario);
            $usuario->update([
                "correo_electronico" => $datos["correo_electronico"],
                "celular" => $datos["celular"],
            ]);

            $persona->update([
                "ruc" => $datos["ruc"],
                "razon_social" => $datos["razon_social"],
                "clave_acceso" => Crypt::encryptString($datos["clave_acceso"]),
                "informacion_adicional" => $datos["informacion_adicional"] ?? null,
            ]);

            $persona->load("usuario");

            return $persona;
        });
    }

    /**
     * Elimina una persona jurídica y su usuario asociado dentro de una transacción.
     *
     * @param int $id ID de la persona jurídica a eliminar.
     * @return void
     * @throws \Exception Si la persona jurídica no existe o si ocurre un error.
     */
    public function eliminar(int $id): void
    {
        $persona = PersonaJuridica::findOrFail($id);

        DB::transaction(function () use ($persona) {
            $persona->delete();
            Usuario::findOrFail($persona->id_usuario)->delete();
        });
    }
}
