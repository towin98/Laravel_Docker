<?php

namespace App\Jobs;

use App\Exports\TecnologiaExport;
use App\Http\Controllers\TecnologiaController;
use App\Models\Tecnologia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as Excell;


class GenerateReportJob implements ShouldQueue
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
            case 'PDF':
                $tecnologia = new TecnologiaController();
                // $jobId = $this->job->getJobId();
                $tecnologia->logicaPdf($this->params);
                break;
            case 'XLSX':
                $data[] = [];

                $bloques = 1600;
                ini_set('max_execution_time', 0); // 0 significa sin límite.
                ini_set('memory_limit', '1024M'); // Ajusta según lo necesario.

                Tecnologia::select([
                        'id',
                        'nombre',
                        'descripcion',
                        'estado',
                        'created_at'
                    ])
                    ->orderBy('id', 'desc')
                    // ->where('estado', 'ACTIVO')
                    ->chunk($bloques, function ($registros) use (&$data){
                        foreach ($registros as $registro) {
                            $data[] = [
                                $registro->id,
                                $registro->nombre,
                                $registro->descripcion,
                                $registro->estado,
                                $registro->created_at,
                            ];
                        }
                    });

                // Columnas títulos
                $headings = ['ID', 'Nombre', 'Descripción', 'Estado', 'Fecha Creación'];

                $parametros = array();
                $parametros['data']     = $data;
                $parametros['headings'] = $headings;
                $parametros['title']    = 'INFORME';

                Excel::store(new TecnologiaExport($parametros), 'tecnologias_excel/TECNOLOGIAS_'.date('YmdHis').'.xlsx', 's3', ExcelL::XLSX);
                break;
            case 'CSV':
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
                break;
        }
    }
}
