<?php

namespace App\Services;

use App\Models\PersonaNatural;
use App\Models\Usuario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * Servicio que encapsula la lógica de negocio de las personas naturales.
 * Maneja transacciones, encriptación y relaciones con el modelo Usuario.
 */
class PersonaNaturalService
{
    /**
     * Obtiene todas las personas naturales con su usuario asociado.
     *
     * @return Collection
     */
    public function obtenerTodas(): Collection
    {
        return PersonaNatural::with("usuario")->get();
    }

    /**
     * Obtiene una persona natural por su ID, incluyendo la clave de acceso desencriptada.
     *
     * @param int $id ID de la persona natural.
     * @return PersonaNatural
     */
    public function obtenerPorId(int $id): PersonaNatural
    {
        $persona = PersonaNatural::with("usuario")->findOrFail($id);
        $persona->makeVisible("clave_acceso");
        $persona->clave_acceso = Crypt::decryptString($persona->clave_acceso);

        return $persona;
    }

    /**
     * Crea una persona natural y su usuario asociado dentro de una transacción.
     *
     * @param array $datos Datos validados del formulario.
     * @return PersonaNatural Persona natural creada con su relación usuario cargada.
     * @throws \Exception Si ocurre un error durante la creación.
     */
    public function crear(array $datos): PersonaNatural
    {
        return DB::transaction(function () use ($datos) {
            $usuario = Usuario::create([
                "correo_electronico" => $datos["correo_electronico"],
                "celular" => $datos["celular"],
            ]);

            $persona = PersonaNatural::create([
                "id_usuario" => $usuario->id_usuario,
                "identificacion" => $datos["identificacion"],
                "nombres" => $datos["nombres"],
                "apellido_p" => $datos["apellido_p"] ?? null,
                "apellido_m" => $datos["apellido_m"] ?? null,
                "clave_acceso" => Crypt::encryptString($datos["clave_acceso"]),
                "informacion_adicional" => $datos["informacion_adicional"] ?? null,
            ]);

            $persona->load("usuario");

            return $persona;
        });
    }

    /**
     * Actualiza una persona natural y su usuario asociado dentro de una transacción.
     *
     * @param int $id ID de la persona natural a actualizar.
     * @param array $datos Datos validados del formulario.
     * @return PersonaNatural Persona natural actualizada con su relación usuario cargada.
     * @throws \Exception Si la persona natural no existe o si ocurre un error.
     */
    public function actualizar(int $id, array $datos): PersonaNatural
    {
        $persona = PersonaNatural::findOrFail($id);

        return DB::transaction(function () use ($persona, $datos) {
            $usuario = Usuario::findOrFail($persona->id_usuario);
            $usuario->update([
                "correo_electronico" => $datos["correo_electronico"],
                "celular" => $datos["celular"],
            ]);

            $persona->update([
                "identificacion" => $datos["identificacion"],
                "nombres" => $datos["nombres"],
                "apellido_p" => $datos["apellido_p"] ?? null,
                "apellido_m" => $datos["apellido_m"] ?? null,
                "clave_acceso" => Crypt::encryptString($datos["clave_acceso"]),
                "informacion_adicional" => $datos["informacion_adicional"] ?? null,
            ]);

            $persona->load("usuario");

            return $persona;
        });
    }

    /**
     * Elimina una persona natural y su usuario asociado dentro de una transacción.
     *
     * @param int $id ID de la persona natural a eliminar.
     * @return void
     * @throws \Exception Si la persona natural no existe o si ocurre un error.
     */
    public function eliminar(int $id): void
    {
        $persona = PersonaNatural::findOrFail($id);

        DB::transaction(function () use ($persona) {
            $persona->delete();
            Usuario::findOrFail($persona->id_usuario)->delete();
        });
    }
}
