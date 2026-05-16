<?php

namespace App\Services;

use App\Models\Administrador;
use App\Models\Usuario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Servicio que encapsula la lógica de negocio de los administradores.
 * Maneja transacciones, hashing de claves y relaciones con el modelo Usuario.
 */
class AdministradorService
{
    /**
     * Obtiene todos los administradores con su usuario asociado.
     *
     * @return Collection
     */
    public function obtenerTodos(): Collection
    {
        return Administrador::with("usuario")->get();
    }

    /**
     * Obtiene un administrador por su ID con su usuario asociado.
     *
     * @param int $id ID del administrador.
     * @return Administrador
     */
    public function obtenerPorId(int $id): Administrador
    {
        return Administrador::with("usuario")->findOrFail($id);
    }

    /**
     * Crea un administrador y su usuario asociado dentro de una transacción.
     *
     * @param array $datos Datos validados del formulario.
     * @return Administrador Administrador creado con su relación usuario cargada.
     * @throws \Exception Si ocurre un error durante la creación.
     */
    public function crear(array $datos): Administrador
    {
        return DB::transaction(function () use ($datos) {
            $usuario = Usuario::create([
                "correo_electronico" => $datos["correo_electronico"],
                "celular" => $datos["celular"],
            ]);

            $administrador = Administrador::create([
                "id_usuario" => $usuario->id_usuario,
                "nombres" => $datos["nombres"],
                "apellido_p" => $datos["apellido_p"],
                "apellido_m" => $datos["apellido_m"],
                "clave" => bcrypt($datos["clave"]),
            ]);

            $administrador->load("usuario");

            return $administrador;
        });
    }

    /**
     * Actualiza un administrador y su usuario asociado dentro de una transacción.
     *
     * @param int $id ID del administrador a actualizar.
     * @param array $datos Datos validados del formulario.
     * @return Administrador Administrador actualizado con su relación usuario cargada.
     * @throws \Exception Si el administrador no existe o si ocurre un error.
     */
    public function actualizar(int $id, array $datos): Administrador
    {
        $administrador = Administrador::findOrFail($id);

        return DB::transaction(function () use ($administrador, $datos) {
            $usuario = Usuario::findOrFail($administrador->id_usuario);
            $usuario->update([
                "correo_electronico" => $datos["correo_electronico"],
                "celular" => $datos["celular"],
            ]);

            $updateData = [
                "nombres" => $datos["nombres"],
                "apellido_p" => $datos["apellido_p"] ?? null,
                "apellido_m" => $datos["apellido_m"] ?? null,
            ];

            // Solo actualizar la clave si fue proporcionada
            if (!empty($datos["clave"])) {
                $updateData["clave"] = bcrypt($datos["clave"]);
            }

            $administrador->update($updateData);
            $administrador->load("usuario");

            return $administrador;
        });
    }

    /**
     * Elimina un administrador y su usuario asociado dentro de una transacción.
     *
     * @param int $id ID del administrador a eliminar.
     * @return void
     * @throws \Exception Si el administrador no existe o si ocurre un error.
     */
    public function eliminar(int $id): void
    {
        $administrador = Administrador::findOrFail($id);

        DB::transaction(function () use ($administrador) {
            $administrador->delete();
            Usuario::findOrFail($administrador->id_usuario)->delete();
        });
    }
}
