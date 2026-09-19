<?php

namespace App\Services;

use App\Models\Archivo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Servicio que encapsula la lógica de negocio para la gestión de archivos.
 * Maneja almacenamiento en disco, consultas filtradas y generación de ZIPs.
 */
class ArchivoService
{
    /**
     * Obtiene los archivos activos de un usuario filtrados por año.
     *
     * @param int $idUsuario ID del usuario.
     * @param int $year Año de la carpeta.
     * @return Collection Colección de archivos.
     */
    public function obtenerPorUsuarioYAnio(
        int $idUsuario,
        int $year
    ): Collection {
        return Archivo::where("id_usuario", $idUsuario)
            ->where("year", $year)
            ->orderBy("created_at", "desc")
            ->get();
    }

    /**
     * Obtiene los años disponibles que tienen archivos para un usuario.
     *
     * @param int $idUsuario ID del usuario.
     * @return array Lista de años con archivos.
     */
    public function obtenerAniosDisponibles(int $idUsuario): array
    {
        return Archivo::where("id_usuario", $idUsuario)
            ->select("year")
            ->distinct()
            ->orderBy("year", "desc")
            ->pluck("year")
            ->toArray();
    }

    /**
     * Almacena uno o múltiples archivos en el disco y registra en la base de datos.
     * Los archivos se guardan en: private/files/{id_usuario}/{year}/
     * Se sanitiza el nombre del archivo para evitar path traversal.
     *
     * @param int $idUsuario ID del usuario.
     * @param int $year Año/carpeta destino.
     * @param array $files Array de archivos UploadedFile.
     * @return Collection Colección de archivos creados.
     */
    public function almacenar(
        int $idUsuario,
        int $year,
        array $files
    ): Collection {
        $archivosCreados = collect();
        $basePath = "private/files/{$idUsuario}/{$year}";

        foreach ($files as $file) {
            // Sanitizar el nombre del archivo para evitar path traversal
            $originalName = $file->getClientOriginalName();
            $safeName = $this->sanitizarNombreArchivo($originalName);

            // Si ya existe un archivo con el mismo nombre, agregar sufijo numérico
            $finalName = $safeName;
            $counter = 1;
            while (Storage::disk("local")->exists("{$basePath}/{$finalName}")) {
                $nameWithoutExt = pathinfo($safeName, PATHINFO_FILENAME);
                $ext = pathinfo($safeName, PATHINFO_EXTENSION);
                $finalName = "{$nameWithoutExt}_{$counter}.{$ext}";
                $counter++;
            }

            // Almacenar archivo en disco
            $storedPath = $file->storeAs($basePath, $finalName, "local");

            // Crear registro en la base de datos
            $archivo = Archivo::create([
                "id_usuario" => $idUsuario,
                "file_name" => $finalName,
                "file_path" => $storedPath,
                "file_size" => $file->getSize(),
                "file_extension" => strtolower(
                    $file->getClientOriginalExtension()
                ),
                "mime_type" => $file->getMimeType(),
                "year" => $year,
            ]);

            $archivosCreados->push($archivo);
        }

        return $archivosCreados;
    }

    /**
     * Obtiene la ruta completa de un archivo para su descarga.
     *
     * @param int $idArchivo ID del archivo.
     * @return Archivo Modelo del archivo encontrado.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si no existe.
     */
    public function obtenerArchivo(int $idArchivo): Archivo
    {
        return Archivo::findOrFail($idArchivo);
    }

    /**
     * Genera un archivo ZIP temporal con los archivos seleccionados.
     *
     * @param array $idsArchivos Array de IDs de archivos.
     * @return string Ruta completa al archivo ZIP temporal.
     * @throws \Exception Si no se puede crear el ZIP o no se encuentran archivos.
     */
    public function generarZip(array $idsArchivos): string
    {
        $archivos = Archivo::whereIn("id_archivo", $idsArchivos)->get();

        if ($archivos->isEmpty()) {
            throw new \Exception("No se encontraron archivos para descargar");
        }

        $zipFileName =
            "archivos_" . now()->format("YmdHis") . "_" . Str::random(6) . ".zip";
        $zipPath = storage_path("app/private/temp/{$zipFileName}");

        // Asegurarse de que el directorio temporal exista
        if (!file_exists(storage_path("app/private/temp"))) {
            mkdir(storage_path("app/private/temp"), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            throw new \Exception("No se pudo crear el archivo ZIP");
        }

        foreach ($archivos as $archivo) {
            $fullPath = storage_path("app/{$archivo->file_path}");
            if (file_exists($fullPath)) {
                $zip->addFile($fullPath, $archivo->file_name);
            }
        }

        $zip->close();

        return $zipPath;
    }

    /**
     * Realiza el soft delete de un archivo y elimina el archivo físico del disco.
     *
     * @param int $idArchivo ID del archivo a eliminar.
     * @return void
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si no existe.
     */
    public function eliminar(int $idArchivo): void
    {
        $archivo = Archivo::findOrFail($idArchivo);

        // Eliminar archivo físico del disco
        if (Storage::disk("local")->exists($archivo->file_path)) {
            Storage::disk("local")->delete($archivo->file_path);
        }

        // Soft delete del registro en la base de datos
        $archivo->delete();
    }

    /**
     * Sanitiza el nombre de un archivo eliminando caracteres peligrosos
     * y previniendo ataques de path traversal.
     *
     * @param string $fileName Nombre original del archivo.
     * @return string Nombre sanitizado del archivo.
     */
    private function sanitizarNombreArchivo(string $fileName): string
    {
        // Remover cualquier ruta relativa o absoluta
        $fileName = basename($fileName);

        // Remover caracteres peligrosos, mantener alfanuméricos, puntos, guiones y guiones bajos
        $fileName = preg_replace('/[^\w\-\.\s]/', '', $fileName);

        // Reemplazar espacios múltiples por un solo guion bajo
        $fileName = preg_replace('/\s+/', '_', $fileName);

        // Si el nombre queda vacío, generar uno aleatorio
        if (empty(pathinfo($fileName, PATHINFO_FILENAME))) {
            $fileName = "archivo_" . Str::random(8) . "." . pathinfo($fileName, PATHINFO_EXTENSION);
        }

        return $fileName;
    }
}
