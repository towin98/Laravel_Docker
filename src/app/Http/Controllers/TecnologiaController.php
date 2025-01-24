<?php

namespace App\Http\Controllers;

use App\Http\Requests\TecnologiaRequest;
use Exception;
use App\Jobs\GenerateReportJob;
use App\Models\Tecnologia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TecnologiaController extends Controller
{
    public function index(int $skip = 0, int $take = 10){

        $totalRecords = Tecnologia::count(); // Total de registros en la tabla

        $tecnologias = Tecnologia::select([
                'id',
                'nombre',
                'descripcion',
                'estado'
            ])
            ->orderBy('id', 'desc')
            ->skip($skip)
            ->take($take)
            ->get();

        $currentPage = (int) ceil(($skip + 1) / $take); // Página actual
        $totalPages = (int) ceil($totalRecords / $take); // Total de páginas

        return view('tecnologias.index', compact('tecnologias', 'currentPage', 'totalPages', 'take'));
    }

    public function create(){
        return view('tecnologias.show');
    }

    public function store(TecnologiaRequest $request){
        try {
            Tecnologia::create($request->all());
            return redirect()->route('tecnologias.index')->with('success', 'Se creo con exito');
        } catch (Exception $e) {
            return view('tecnologias.show', ['error' => 'Hubo un error al crear.']);
        }
    }

    public function show($id){
        try {
            $tecnologia = Tecnologia::findOrFail($id);
            return view('tecnologias.show', compact('tecnologia'));
        } catch (Exception $e) {
            return redirect()->route('tecnologias.index', $id)->with('error', 'Hubo un error al actualizar la tecnología');
        }
    }

    public function update(Request $request, $id){
        try {
            $tecnologia = Tecnologia::findOrFail($id);
            $tecnologia->update($request->all());
            return redirect()->route('tecnologias.show', $id)->with('success', 'La tecnología ha sido actualizada correctamente');
        } catch (Exception $e) {
            return redirect()->route('tecnologias.show', $id)->with('error', 'Hubo un error al actualizar la tecnología');
        }
    }

    public function destroy($id){
        try {
            $tecnologia = Tecnologia::findOrFail($id);
            $tecnologia->delete();
            return redirect()->route('tecnologias.index')->with('success', 'La tecnología ha sido eliminada correctamente');
        } catch (Exception $e) {
            return redirect()->route('tecnologias.index')->with('error', 'Hubo un error al eliminar la tecnología');
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

    public function reporteBackground(Request $request){
        try {
            $params = ['estado' => $request->estado];
            GenerateReportJob::dispatch($params);
            return redirect()->route('tecnologias.index')->with('success', 'El reporte se esta generando en segundo plano.');
        } catch (Exception $e) {
            return redirect()->route('tecnologias.index')->with('error', 'Hubo un error generar reporte en segundo plano.');
        }
    }

    public function reportPdf(int $skip, int $take){
        try {
            $data = Tecnologia::select([
                    'id',
                    'nombre',
                    'descripcion',
                    'estado'
                ])
                ->orderBy('id', 'desc')
                ->skip($skip)
                ->take($take)
                ->get();

            // Renderizar la vista como PDF
            $pdf = Pdf::loadView('pdf.tecnologias', ['data' => $data]);

            // Descargando pdf
            return $pdf->download('tecnologias.pdf');
        } catch (Exception $e) {
            return redirect()->route('tecnologias.index')->with('error', 'Hubo un error al reporte PDF.');
        }
    }
}
