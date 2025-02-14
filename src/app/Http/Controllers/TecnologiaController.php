<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Tecnologia;
use Illuminate\Http\Request;
use App\Jobs\GenerateReportJob;
use App\Jobs\ImportTecnologiasJob;
use Freshbitsweb\Laratables\Laratables;
use App\Http\Requests\TecnologiaRequest;
use App\Repositories\TecnologiaRepository;
use App\Services\TecnologiaExportPdfService;
use App\Http\Requests\TecnologiaPaginationRequest;

class TecnologiaController extends Controller
{
    private $tecnologiaRepository;
    private $tecnologiaExportPdfService;

    public function __construct(
        TecnologiaRepository $tecnologiaRepository,
        TecnologiaExportPdfService $tecnologiaExportPdfService)
    {
        $this->tecnologiaRepository = $tecnologiaRepository;
        $this->tecnologiaExportPdfService = $tecnologiaExportPdfService;
    }

    /**
     * Mostrando la página de inicio.
     *
     * @return Illuminate\Http\Response
     **/
    public function index()
    {
        return view('tecnologias.index');
    }

    /**
     * Obteniendo datos paginados con laraTables
     *
     * @return array
     */
    public function dataTableListar(Request $request)
    {
        return Laratables::recordsOf(Tecnologia::class, function($query) use ($request)
        {
            return $query->when($request->filled('dateDesde') && $request->filled('dateHasta'), function($query) use ($request){
                $query->whereBetween('created_at', [
                    $request->dateDesde . ' 00:00:00', $request->dateHasta . ' 23:59:59'
                ]);
            });
        });
    }

    public function create()
    {
        return view('tecnologias.show');
    }

    /**
     * Guardar Tecnologia
     *
     * @param TecnologiaRequest $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function store(TecnologiaRequest $request)
    {
        try {
            $this->tecnologiaRepository->store($request->all());
            return redirect()->route('laravel-datatable')->with('success', 'Se creó con éxito');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    /**
     * Undocumented function
     *
     * @param [type] $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(Tecnologia $tecnologia)
    {
        try {
            return view('tecnologias.show', compact('tecnologia'));
        } catch (Exception $e) {
            return redirect()->route('laravel-datatable')->with('error', 'Hubo un error al consultar la tecnología');
        }
    }

    /**
     * Actualizar tecnología
     *
     * @param TecnologiaRequest $request
     * @param [type] $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TecnologiaRequest $request, Tecnologia $tecnologia)
    {
        try {
            $this->tecnologiaRepository->update($request->all(), $tecnologia);
            return redirect()->route('tecnologias.show', $tecnologia->id)->with('success', 'Actualización exitosa');
        } catch (Exception $e) {
            return redirect()->route('tecnologias.show', $tecnologia->id)->with('error', 'No se pudo actualizar la tecnología. Inténtalo nuevamente.');
        }
    }

    /**
     * Elimina una tecnologia
     *
     * @param [type] $id Tecnología
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Tecnologia $tecnologia)
    {
        try {
            $this->tecnologiaRepository->delete($tecnologia);
            return redirect()->route('laravel-datatable')->with('success', 'La tecnología ha sido eliminada correctamente');
        } catch (Exception $e) {
            return redirect()->route('laravel-datatable')->with('error', 'No se pudo eliminar tecnología, Inténtalo nuevamente.');
        }
    }

    /**
     * Método que genere Excel con porte de tecnologias en segundo plano
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reporteBackground(Request $request)
    {
        try {
            $params = ['tipo' => "XLSX"];
            GenerateReportJob::dispatch($params);
            return redirect()->route('laravel-datatable')->with('success', 'El reporte se esta generando en segundo plano.');
        } catch (Exception $e) {
            return redirect()->route('laravel-datatable')->with('error', 'Hubo un error generar reporte en segundo plano.');
        }
    }

    /**
     * Metodo que genera reporte en pdf de la pagina actual
     *
     * @param integer $skip
     * @param integer $take
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function reportPdfScreen(TecnologiaPaginationRequest $request)
    {
        try {
            $orderColumn = $request->filled('orderColumn') ? $request->orderColumn : 'id';
            $order = $request->filled('order') ? $request->order : 'asc';

            $request->merge([
                'orderColumn' => $orderColumn,
                'order'       => $order,
            ]);

            $data = $this->tecnologiaRepository->obtenerDataPaginacion($request->all());
            return $this->tecnologiaExportPdfService->reporteScreen($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Validación de Datos',
                'errors' => "Error inesperado al generar reporte pdf: ". $e
            ], 409);
        }
    }

    /**
     * Metodo que genera reporte en pdf de la pagina actual en segundo plano.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reportPdfBackground(TecnologiaPaginationRequest $request)
    {
        try {
            $orderColumn = $request->filled('orderColumn') ? $request->orderColumn : 'id';
            $order = $request->filled('order') ? $request->order : 'asc';

            $params = [
                'expiration'  => 1,
                'tipo'        => "PDF",
                'skip'        => $request->skip,
                'take'        => $request->take,
                'orderColumn' => $orderColumn,
                'order'       => $order,
                'search'      => $request->search
            ];

            if ($request->filled('dateDesde')) {
                $params['dateDesde'] = $request->dateDesde;
            }

            if ($request->filled('dateHasta')) {
                $params['dateHasta'] = $request->dateHasta;
            }
            GenerateReportJob::dispatch($params);

            return response()->json([
                'messages'      => "Generando reporte en segundo plano",
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Validación de Datos',
                'errors' => "Error inesperado al generar reporte pdf en segundo plano."
            ], 409);
        }
    }

    /**
     * Método que permite importar tecnologías desde un archivo.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importTecnologias(Request $request)
    {
        try {
            $nombreArchivo = "";
            switch ($request->tipo) {
                case 'CSV':
                    $nombreArchivo = "import_csv_" . date('YmdHis') . ".csv";
                    break;
                default:
                    return response()->json([
                        'message' => 'Validación de Datos',
                        'errors' => "Formato de archivo no soportado."
                    ], 409);
                    break;
            }

            $request->file('file')->storeAs('import', $nombreArchivo);

            $params = [
                'tipo'          => $request->tipo,
                'path'          => "import/".$nombreArchivo,
                'batchSize'     => 400
            ];
            ImportTecnologiasJob::dispatch($params);

            return response()->json([
                'message' => 'Archivo importado correctamente y en espera de ser procesado en segundo plano.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Validación de Datos',
                'errors' => "Error inesperado al importar tecnologías."
            ], 409);
        }
    }

    /**
     * Retorna Url temporal de archivo en s3
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewPdfMinIo(Request $request)
    {
        try {
            return response()->json([
                'url' => $this->tecnologiaRepository->getTemporaryUrl($request->pathFile, 1)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error inesperado al generar link.',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
