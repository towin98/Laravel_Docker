<?php

namespace App\Jobs;

use App\Models\Tecnologia;
use App\Services\TecnologiaExportExcelService;
use App\Services\TecnologiaExportPdfService;
use Exception;
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
     * Ejecuta el job.
     *
     * @param TecnologiaExportPdfService $pdfService Servicio para generar reportes en PDF.
     * @param TecnologiaExportExcelService $exportExcelService Servicio para exportar en Excel.
     * @return void
     */
    public function handle(
        TecnologiaExportPdfService $pdfService,
        TecnologiaExportExcelService $exportExcelService): void
    {
        try {
            switch ($this->params['tipo']) {
                case 'PDF':
                    $pdfService->reporteBackground($this->params);
                break;
                case 'XLSX':
                    $exportExcelService->export($this->params);
                break;
                case 'CSV':
                    $exportExcelService->exportCsvNoLibrary($this->params);
                break;
                default:
                    throw new Exception('Tipo de reporte no válido: ' . ($this->params['tipo'] ?? 'NULL'));
                break;
            }
        } catch (Exception $e) {
            Log::error('Error al generar reporte en GenerateReportJob: ' . $e->getMessage(), [
                'params' => $this->params
            ]);
        }
    }
}
