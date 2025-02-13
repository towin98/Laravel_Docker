<?php
namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BaseRepository
{
    /**
     * Obtiene la URL temporal de un archivo almacenado en S3 (MinIO).
     *
     * @param string $pathFile
     * @param int $minutes
     * @return string
     */
    public function getTemporaryUrl(string $pathFile, int $minutes = 30): string
    {
        try {
            return Storage::temporaryUrl($pathFile, now()->addMinutes($minutes));
        } catch (Exception $e) {
            Log::error('Error al generar link en minIo: ' . $e->getMessage());
            throw new Exception("Error al generar link en minIo");
        }
    }
}
