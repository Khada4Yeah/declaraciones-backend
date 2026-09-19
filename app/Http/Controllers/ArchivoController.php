<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArchivoRequest;
use App\Services\ArchivoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Controlador de archivos.
 * Delega toda la lógica de negocio al ArchivoService.
 * Gestiona la subida, descarga, listado y eliminación de archivos por usuario.
 */
class ArchivoController extends Controller
{
    public function __construct(
        private ArchivoService $archivoService,
    ) {}

    /**
     * Lista los archivos de un usuario filtrados por año.
     *
     * @param int $idUsuario ID del usuario.
     * @param Request $request Contiene el query param 'year'.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $idUsuario, Request $request)
    {
        $year = $request->query("year");

        if (!$year) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "El parámetro 'year' es obligatorio",
                ],
                400,
            );
        }

        $archivos = $this->archivoService->obtenerPorUsuarioYAnio(
            $idUsuario,
            (int) $year,
        );

        return response()->json([
            "status" => "success",
            "data" => $archivos,
        ]);
    }

    /**
     * Obtiene los años disponibles que tienen archivos para un usuario.
     *
     * @param int $idUsuario ID del usuario.
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerAnios(int $idUsuario)
    {
        $anios = $this->archivoService->obtenerAniosDisponibles($idUsuario);

        return response()->json([
            "status" => "success",
            "data" => $anios,
        ]);
    }

    /**
     * Sube uno o múltiples archivos para un usuario en un año específico.
     *
     * @param ArchivoRequest $request Datos validados (id_usuario, year, files[]).
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ArchivoRequest $request)
    {
        try {
            $archivos = $this->archivoService->almacenar(
                $request->input("id_usuario"),
                $request->input("year"),
                $request->file("files"),
            );

            return response()->json(
                [
                    "status" => "success",
                    "message" => "Archivos subidos exitosamente",
                    "data" => $archivos,
                ],
                201,
            );
        } catch (\Exception $e) {
            Log::error("Error al subir archivos: " . $e->getMessage());
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Error al subir los archivos",
                ],
                500,
            );
        }
    }

    /**
     * Descarga un archivo individual.
     *
     * @param int $idArchivo ID del archivo a descargar.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function descargar(int $idArchivo)
    {
        try {
            $archivo = $this->archivoService->obtenerArchivo($idArchivo);
            $fullPath = storage_path("app/{$archivo->file_path}");

            if (!file_exists($fullPath)) {
                return response()->json(
                    [
                        "status" => "error",
                        "message" =>
                            "El archivo no se encuentra en el servidor",
                    ],
                    404,
                );
            }

            return response()->download(
                $fullPath,
                $archivo->file_name,
                ["Content-Type" => $archivo->mime_type],
            );
        } catch (\Exception $e) {
            Log::error("Error al descargar archivo: " . $e->getMessage());
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Error al descargar el archivo",
                ],
                500,
            );
        }
    }

    /**
     * Descarga múltiples archivos empaquetados en un ZIP.
     *
     * @param Request $request Contiene 'ids' con los IDs de archivos.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function descargarZip(Request $request)
    {
        $request->validate([
            "ids" => "required|array|min:1",
            "ids.*" => "required|integer|exists:archivos,id_archivo",
        ]);

        try {
            $zipPath = $this->archivoService->generarZip($request->input("ids"));

            return response()
                ->download($zipPath, basename($zipPath))
                ->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error("Error al generar ZIP: " . $e->getMessage());
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Error al generar el archivo ZIP",
                ],
                500,
            );
        }
    }

    /**
     * Elimina un archivo (soft delete + eliminación física).
     *
     * @param int $idArchivo ID del archivo a eliminar.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $idArchivo)
    {
        try {
            $this->archivoService->eliminar($idArchivo);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error("Error al eliminar archivo: " . $e->getMessage());
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Error al eliminar el archivo",
                ],
                500,
            );
        }
    }
}
