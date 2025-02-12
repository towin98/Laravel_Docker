<?php
namespace App\Services;

use App\Models\Tecnologia;
use App\Exports\TecnologiaExport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as Excell;

class TecnologiaExportExcelService
{

    public function export(array $params)
    {
        // $data[] = [];

        // $bloques = 1600;
        ini_set('max_execution_time', 0); // sin límite de tiempo de ejecucion script.
        ini_set('memory_limit', '1024M'); //Permite que el script use hasta 1 GB de RAM.

        // Tecnologia::select([
        //         'id',
        //         'nombre',
        //         'descripcion',
        //         'estado',
        //         'created_at'
        //     ])
        //     ->orderBy('id', 'desc')
        //     // ->where('estado', 'ACTIVO')
        //     ->chunk($bloques, function ($registros) use (&$data){
        //         foreach ($registros as $registro) {
        //             $data[] = [
        //                 $registro->id,
        //                 $registro->nombre,
        //                 $registro->descripcion,
        //                 $registro->estado,
        //                 $registro->created_at,
        //             ];
        //         }
        //     });

        // Columnas títulos
        $headings = ['ID', 'Nombre', 'Descripción', 'Estado', 'Fecha Creación'];

        $parametros = array();
        // $parametros['data']     = $data;
        $parametros['headings'] = $headings;
        $parametros['title']    = 'INFORME';

        // Excel::store(new TecnologiaExport($parametros), 'tecnologias_excel/TECNOLOGIAS_'.date('YmdHis').'.xlsx', 's3', ExcelL::XLSX);
        return Excel::store(new TecnologiaExport($parametros), 'tecnologias_excel/TECNOLOGIAS_' . date('YmdHis') . '.xlsx', 's3', \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function exportCsvNoLibrary()
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
