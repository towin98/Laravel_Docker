<?php
namespace App\Services;

use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Events\JobProgressUpdated;
use Illuminate\Support\Facades\Storage;
use App\Repositories\TecnologiaRepository;

class TecnologiaExportPdfService
{
    private $tecnologiaRepository;

    public function __construct(TecnologiaRepository $tecnologiaRepository)
    {
        $this->tecnologiaRepository = $tecnologiaRepository;
    }

    /**
     * Genera un reporte en PDF a partir de los datos proporcionados.
     *
     */
    public function reporteScreen(array $data)
    {
        try {
            $htmlTable = "";

            foreach ($data as $registro) {
                $htmlTable .= '
                    <tr>
                        <td>' . $registro['id'] . '</td>
                        <td>' . $registro['nombre'] . '</td>
                        <td>' . $registro['descripcion'] . '</td>
                        <td>' . $registro['estado'] . '</td>
                    </tr>';
            }

            // Renderizar la vista como PDF
            $pdf = Pdf::loadView('pdf.tecnologias', ['tabla' => $htmlTable]);
            // Descargando pdf
            return $pdf->download('TECNOLOGIAS_PAGINADO' . date('YmdHis') . '.pdf');
        } catch (Exception $e) {
            throw new Exception("Hubo un error al reporte PDF: " . $e->getMessage());
        }
    }

    /**
     * Función que genera un reporte PDF de tecnologías en segundo plano, procesando los datos en bloques para evitar sobrecargar la memoria.
     *
     * @param array $params
     * @return void
     * @throws Exception Si ocurre un error durante la generación del PDF.
     */
    public function reporteBackground(array $params)
    {
        try {
            ini_set('memory_limit', '1024M');

            $tecnologias = $this->tecnologiaRepository->obtenerDataPaginacion($params);

            $bodyTable = '';
            $registrosProcesados = 0;
            $progresoEmitido = 0;

            foreach ($tecnologias as $registro) {
                $registrosProcesados++;
                $bodyTable .= "
                    <tr>
                        <td>".$registro['id']."</td>
                        <td>".$registro['nombre']."</td>
                        <td>".$registro['descripcion']."</td>
                        <td>".$registro['estado']."</td>
                    </tr>";

                // Emitir evento cada 5%
                $nuevoProgreso = floor(($registrosProcesados / $params['take']) * 92);
                if ($nuevoProgreso >= ($progresoEmitido + 5)) {
                    $progresoEmitido = $nuevoProgreso;
                    event(new JobProgressUpdated($progresoEmitido));
                }
            }

            $content = PDF::loadView('pdf.tecnologias', ['tabla' => $bodyTable])->output();

            $nombreArchivo = "reporte_pdf_" . date('YmdHis') . ".pdf";
            Storage::put('tecnologias_pdf/' . $nombreArchivo, $content);
            event(new JobProgressUpdated(100, $this->tecnologiaRepository->getTemporaryUrl("tecnologias_pdf/" . $nombreArchivo, $params['expiration']), $nombreArchivo));
        } catch (Exception $e) {
            throw new Exception("Error al generar Pdf: " . $e->getMessage());
        }
    }
}
