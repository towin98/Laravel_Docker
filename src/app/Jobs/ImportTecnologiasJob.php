<?php

namespace App\Jobs;

use App\Services\TecnologiaImportCsvService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
    public function handle(TecnologiaImportCsvService $tecnologiaImportCsvService): void
    {
        switch ($this->params['tipo']) {
            case 'CSV':
                $tecnologiaImportCsvService->import($this->params);
                break;
        }
    }
}
