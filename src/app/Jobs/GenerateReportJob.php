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

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The parameters for the job (e.g., filters, user ID, etc.).
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
        $data = Tecnologia::select([
                'id',
                'nombre',
                'descripcion',
                'created_at'
            ])
            // ->where($this->params)
            ->get();

        // Creando el nombre del archivo y ruta
        $filename = 'reports/report_' . now()->timestamp . '.csv';
        $csv = fopen('php://temp', 'r+');

        // Titulos
        fputcsv($csv, ['ID', 'Nombre', 'Descripcion', 'Fecha Creacion']);

        // Add data
        foreach ($data as $row) {
            fputcsv($csv, [
                $row->id,
                $row->nombre,
                $row->descripcion,
                $row->created_at
            ]);
        }

        // Save to storage
        rewind($csv);
        Storage::disk('local')->put($filename, stream_get_contents($csv));
        fclose($csv);
    }
}
