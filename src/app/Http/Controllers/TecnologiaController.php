<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Tecnologia;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\GenerateReportJob;
use App\Events\JobProgressUpdated;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\TecnologiaRequest;
use Illuminate\Support\Facades\Storage;

class TecnologiaController extends Controller
{
    private $carpetaSubirPdf = "pdf_cargados";

    /**
     * Muestra vista de datatable para tecnologias.
     *
     * @return \Illuminate\View\View
     */
    public function index(){
        return view('tecnologias.datatable');
    }

    /**
     * Método que consulta data de tecnologías por parametros de la paginación.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception Si ocurre un error durante la consulta.
     */
    public function dataTableListar(Request $request)
    {
        try {
            $draw = $request->query('draw', 1); // Es un contador propio de datatable
            $recordsTotal = Tecnologia::count();

            $skip = 0;
            $take = 10;

            $orderColumn = 'id';
            $order = 'asc';

            if ($request->filled('skip')) {
                $skip = $request->skip;
            }
            if ($request->filled('take')) {
                $take = $request->take;
            }
            if ($request->filled('orderColumn')) {
                $orderColumn = $request->orderColumn;
            }
            if ($request->filled('order')) {
                $order = $request->order;
            }

            $totalRecords = Tecnologia::select(['id', 'nombre', 'descripcion', 'estado', 'pdf'])
                ->where(function ($query) use ($request) {
                    if ($request->has('search') && $request->search!= '') {
                        $query->where('id', 'LIKE', '%'. $request->search. '%')
                            ->orWhere('nombre', 'LIKE', '%'. $request->search. '%')
                            ->orWhere('descripcion', 'LIKE', '%'. $request->search. '%')
                            ->orWhere('estado', 'LIKE', '%'. $request->search. '%');
                    }
                });

            $totalRecordsPage = $totalRecords->count();

            $tecnologias = $totalRecords
                ->skip($skip)
                ->take($take)
                ->orderBy($orderColumn, $order)
                ->get()
                ->map(function ($tecnologia) {
                    return [
                        'id'            => $tecnologia->id,
                        'nombre'        => $tecnologia->nombre,
                        'descripcion'   => $tecnologia->descripcion,
                        'estado'        => $tecnologia->estado,
                        'pdf'           => $tecnologia->pdf ? Storage::url($tecnologia->pdf) : null
                    ];
                });

            return response()->json([
                "draw" => intval($draw),
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $totalRecordsPage,
                "data" => $tecnologias
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Hubo un error al listar los tecnologías.' . $e
            ], 500);
        }
    }

    public function create()
    {
        return view('tecnologias.show');
    }

    public function store(TecnologiaRequest $request)
    {
        try {
            $tecnology = Tecnologia::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado
            ]);

            if ($request->filled('pdf')) {
                $pdfPath = $request->file('pdf')->storeAs($this->carpetaSubirPdf, 'tecnologia_'.$tecnology->id . '.pdf');
                $tecnology->update(['pdf' => $pdfPath]);
            }

            return redirect()->route('laravel-datatable')->with('success', 'Se creo con exito');
        } catch (Exception $e) {
            return view('tecnologias.show', ['error' => 'Hubo un error al crear.']);
        }
    }

    public function show($id)
    {
        try {
            $tecnologia = Tecnologia::findOrFail($id);
            return view('tecnologias.show', compact('tecnologia'));
        } catch (Exception $e) {
            return redirect()->route('laravel-datatable')->with('error', 'Hubo un error al actualizar la tecnología');
        }
    }

    /**
     * Actualizar tecnología
     *
     * @param TecnologiaRequest $request
     * @param [type] $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TecnologiaRequest $request, $id)
    {
        try {
            $tecnologia = Tecnologia::findOrFail($id);
            $nameFileNew = 'tecnologia_'. $id . '_'.date('YmdHis').'.pdf';

            if ($request->has('pdf')) {
                // Guardo el archivo en el bucket S3
                $request->file('pdf')->storeAs($this->carpetaSubirPdf, $nameFileNew);

                if ($tecnologia->pdf) {
                    // Elimino el archivo anterior si es que existe en el bucket S3
                    if (Storage::disk('s3')->exists($tecnologia->pdf)) {
                        Storage::disk('s3')->delete($tecnologia->pdf);
                    }
                }
            }

            $tecnologia->update([
                    'nombre'        => $request->nombre,
                    'descripcion'   => $request->descripcion,
                    'estado'        => $request->estado,
                    'pdf'           => $this->carpetaSubirPdf."/".$nameFileNew
                ]
            );
            return redirect()
                ->route('tecnologias.show', $id)
                ->with('success', 'La tecnología ha sido actualizada correctamente');
        } catch (Exception $e) {
            return redirect()
                ->route('tecnologias.show', $id)
                ->with('error', 'Hubo un error al actualizar la tecnología'. $e);
        }
    }

    public function destroy($id)
    {
        try {
            $tecnologia = Tecnologia::findOrFail($id);
            $tecnologia->delete();
            return redirect()
                ->route('laravel-datatable')
                ->with('success', 'La tecnología ha sido eliminada correctamente');
        } catch (Exception $e) {
            return redirect()
                ->route('laravel-datatable')
                ->with('error', 'Hubo un error al eliminar la tecnología');
        }
    }

    /**
     * Método que descarga archivo
     *
     * @param string $filename Nombre del archivo a descargar
     * @return void
     */
    public function download($filename)
    {
        $filePath = "reports/{$filename}";

        if (Storage::exists($filePath)) {
            return Storage::download($filePath);
        }
        abort(404, 'Archivo no encontrado.');
    }

    /**
     * Método que genere Excel con porte de tecnologias en segundo plano
     *
     * @param Request $request
     * @return void
     */
    public function reporteBackground(Request $request)
    {
        try {
            $params = ['tipo' => "XLSX"];
            GenerateReportJob::dispatch($params);
            return redirect()
                ->route('laravel-datatable')
                ->with('success', 'El reporte se esta generando en segundo plano.');
        } catch (Exception $e) {
            return redirect()
                ->route('laravel-datatable')
                ->with('error', 'Hubo un error generar reporte en segundo plano.');
        }
    }

    /**
     * Metodo que genera reporte en pdf de la pagina actual
     *
     * @param integer $skip
     * @param integer $take
     * @return void
     */
    public function reportPdf(Request $request)
    {
        try {
            $htmlTable = "";

            $orderColumn = 'id';
            $order = 'asc';
            if ($request->filled('orderColumn')) {
                $orderColumn = $request->orderColumn;
            }
            if ($request->filled('order')) {
                $order = $request->order;
            }

            $data = Tecnologia::select(['id', 'nombre', 'descripcion', 'estado'])
                ->where(function ($query) use ($request) {
                    if ($request->has('search') && $request->search!= '') {
                        $query->where('id', 'LIKE', '%'. $request->search. '%')
                            ->orWhere('nombre', 'LIKE', '%'. $request->search. '%')
                            ->orWhere('descripcion', 'LIKE', '%'. $request->search. '%')
                            ->orWhere('estado', 'LIKE', '%'. $request->search. '%');
                    }
                })
                ->skip($request->skip)
                ->take($request->take)
                ->orderBy($orderColumn, $order)
                ->get();

            foreach ($data as $registro) {
                $htmlTable .= '
                    <tr>
                        <td>' . $registro->id . '</td>
                        <td>' . $registro->nombre . '</td>
                        <td>' . $registro->descripcion . '</td>
                        <td>' . $registro->estado . '</td>
                    </tr>';
            }

            // Renderizar la vista como PDF
            $pdf = Pdf::loadView('pdf.tecnologias', ['tabla' => $htmlTable]);
            // Descargando pdf
            return $pdf->download('TECNOLOGIAS_PAGINADO' . date('YmdHis') . '.pdf');
        } catch (Exception $e) {
            return redirect()
                ->route('laravel-datatable')
                ->with('error', 'Hubo un error al reporte PDF.' . $e);
        }
    }

    /**
     * Metodo que genera reporte en pdf de la pagina actual en segundo plano.
     *
     * @param Request $request
     * @return void
     */
    public function reportPdfBackground(Request $request)
    {
        try {
            $orderColumn = 'id';
            $order = 'asc';
            if ($request->filled('orderColumn')) {
                $orderColumn = $request->orderColumn;
            }
            if ($request->filled('order')) {
                $order = $request->order;
            }

            $params = [
                'tipo'        => "PDF",
                'skip'        => $request->skip,
                'take'        => $request->take,
                'orderColumn' => $orderColumn,
                'order'       => $order,
                'search'      => $request->search
            ];
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
     * Función que genera un reporte PDF de tecnologías en segundo plano, procesando los datos en bloques para evitar sobrecargar la memoria.
     *
     * @param array $params
     * @return void
     */
    public function logicaPdf(array $params)
    {
        ini_set('memory_limit', '1024M');

        $registrosProcesados = 0; // Contador de registros procesados
        $progresoEmitido = 0; // Progreso emitido para el evento

        $htmlTable = ''; // Variable para construir solo las filas de la tabla

        $tecnologias = Tecnologia::select(['id', 'nombre', 'descripcion', 'estado'])
            ->where(function ($query) use ($params) {
                if ($params['search']!= '') {
                    $query->where('id', 'LIKE', '%'. $params['search']. '%')
                        ->orWhere('nombre', 'LIKE', '%'. $params['search']. '%')
                        ->orWhere('descripcion', 'LIKE', '%'. $params['search']. '%')
                        ->orWhere('estado', 'LIKE', '%'. $params['search']. '%');
                }
            })
            ->skip($params['skip'])
            ->take($params['take'])
            ->orderBy($params['orderColumn'], $params['order'])
            ->get();

        // Log::info("Tamaño:".$tecnologias->count());

        foreach ($tecnologias as $registro) {
            // Log::info($registro);
            $registrosProcesados++;
            $htmlTable .= '
                    <tr>
                        <td>' . $registro->id . '</td>
                        <td>' . $registro->nombre . '</td>
                        <td>' . $registro->descripcion . '</td>
                        <td>' . $registro->estado . '</td>
                    </tr>';

            // Emitir evento cada 5%
            $nuevoProgreso = floor(($registrosProcesados / $params['take']) * 92);
            if ($nuevoProgreso >= ($progresoEmitido + 5)) {
                $progresoEmitido = $nuevoProgreso;
                event(new JobProgressUpdated($progresoEmitido));
            }
        }

        $content = PDF::loadView('pdf.tecnologias', ['tabla' => $htmlTable])->output();
        $nombreArchivo = "reporte_pdf_" . date('YmdHis') . ".pdf";
        // Storage::disk('public')->put($nombreArchivo, $content);
        //Guardando archivo en minio
        Storage::put('tecnologias_pdf/'.$nombreArchivo, $content);

        //Libero memoria
        unset($htmlTable);

        // Descargando pdf
        event(new JobProgressUpdated(100, Storage::url("tecnologias_pdf/".$nombreArchivo), $nombreArchivo));
    }
}
