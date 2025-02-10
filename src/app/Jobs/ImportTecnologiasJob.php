<?php

namespace App\Jobs;

use App\Models\Tecnologia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SplFileObject;

class ImportTecnologiasJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Parametros de job.
     */
    protected $params;

    /**
     * Create a new job instance.
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        switch ($this->params['tipo']) {
            case 'CSV':
                // Descargar el archivo a una ubicación temporal
                Storage::disk('local')->put('temp.csv', Storage::disk('s3')->get($this->params['path']));

                $fullPath = storage_path('app/temp.csv');

                // Lee linea por linea solo manteniendo en memoria la linea actual. aunque tambien existe la otra posibilidad de fgetcsv
                $file = new SplFileObject($fullPath);
                $file->setFlags(SplFileObject::READ_CSV);
                $file->setCsvControl(';'); // Define el separador (en este caso, una coma)

                $batch = [];

                $errorPath = storage_path('app/errores_importacion.csv');
                $hasErrors = false;
                $index = 0;
                $errorFile = fopen($errorPath, 'w');
                fputcsv($errorFile, array('LINEA','ERRORES AL SUBIR TECNOLOGIAS'), ";");

                foreach ($file as $index => $row) {
                    if ($index === 0) {
                        continue;
                    }

                    // Verifica si la fila no está vacía.
                    // Si no se proporciona callback, todas las entradas de array iguales a false serán eliminadas.
                    if (!empty(array_filter($row))) {
                        $data = array_combine(['nombre', 'descripcion', 'estado'], $row);
                        $errorMessage = '';

                        // Validaciones
                        if (empty($data['nombre'])) {
                            $errorMessage = "El campo 'Nombre' es obligatorio.";
                        }

                        if (strlen($data['nombre']) > 50) {
                            $errorMessage = "El campo 'Nombre' no debe superar los 255 carácteres.";
                        }

                        if (empty($data['descripcion'])) {
                            $errorMessage = "El campo 'Descripción' es obligatorio.";
                        }

                        if (strlen($data['descripcion']) > 255) {
                            $errorMessage = "El campo 'Descripción' no debe superar los 255 carácteres.";
                        }

                        if (!in_array($data['estado'], ['ACTIVO', 'INACTIVO'])) {
                            $errorMessage = "El 'Estado' debe ser 'ACTIVO' o 'INACTIVO'.";
                        }

                        // Si hay errores, guardarlos en el CSV de errores
                        if (!empty($errorMessage)) {
                            fputcsv($errorFile, array_merge(array(($index + 1), $errorMessage)), ';');
                            $hasErrors = true;
                            continue;
                        }

                        $batch[] = $data;
                    }

                    // Si no errores en todo el archivo leido y el lote se completa.
                    if (!$hasErrors && count($batch) >= $this->params['batchSize']) {
                        dispatch(new ChunkTecnologiaJob($batch)); // Se envia el chunk en segundo plano
                        $batch = [];
                    }
                }

                // Si no errores proceso las filas restantes
                if (!$hasErrors && !empty($batch)) {
                    dispatch(new ChunkTecnologiaJob($batch));
                }

                // Cerrar el archivo de errores
                fclose($errorFile);

                // Si no hay errores, eliminar el archivo de errores
                if (!$hasErrors) {
                    unlink($errorPath);
                }

                // Eliminar el archivo temporal después de procesarlo
                unlink($fullPath);

                break;
        }
    }
}
