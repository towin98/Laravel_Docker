<?php

namespace App\Repositories;

use Exception;
use App\Models\Tecnologia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TecnologiaRepository extends BaseRepository
{
    protected $model;
    protected $carpetaSubirPdf = 'pdf_cargados';

    /**
     * PostRepository constructor.
     *
     * @param Tecnologia $tecnologia
     */
    public function __construct(Tecnologia $tecnologia)
    {
        $this->model = $tecnologia;
    }

    /**
     * Guarda tecnología
     *
     * @param array $data
     * @return void
     */
    public function store(array $data)
    {
        try {
            $tecnology = $this->model->create([
                'nombre'      => strtoupper($data['nombre']),
                'descripcion' => strtoupper($data['descripcion']),
                'estado'      => strtoupper($data['estado'])
            ]);

            if (isset($data['pdf'])) {
                $nameFileNew = 'tecnologia_' . $tecnology->id . '_' . date('YmdHis') . '.pdf';
                $data['pdf']->storeAs($this->carpetaSubirPdf, $nameFileNew);
                $tecnology->update([
                    'pdf' => $this->carpetaSubirPdf."/".$nameFileNew
                ]);
            }
        } catch (Exception $e) {
            Log::error('Error al guardar tecnología: ' . $e->getMessage());
            throw new Exception('No se pudo guardar la tecnología. Inténtalo de nuevo.');
        }
    }

    /**
     * Método que actualiza tecnología
     *
     * @param array $data Data a actualizar
     * @param [type] $tecnologia
     * @return void
     * @throws Exception
     */
    public function update(array $data, $tecnologia)
    {
        try {
            $updateData = [
                'nombre'      => strtoupper($data['nombre']),
                'descripcion' => strtoupper($data['descripcion']),
                'estado'      => strtoupper($data['estado'])
            ];

            if (isset($data['pdf'])) {
                $nameFileNew = 'tecnologia_' . $tecnologia->id . '_' . date('YmdHis') . '.pdf';
                $data['pdf']->storeAs($this->carpetaSubirPdf, $nameFileNew);

                // Eliminar el archivo anterior si existe
                if ($tecnologia->pdf && Storage::disk('s3')->exists($tecnologia->pdf)) {
                    Storage::disk('s3')->delete($tecnologia->pdf);
                }

                $updateData['pdf'] = $this->carpetaSubirPdf . "/" . $nameFileNew;
            }

            $tecnologia->update($updateData);
        } catch (Exception $e) {
            Log::error('Error al actualizar tecnología: ' . $e->getMessage());
            throw new Exception('Error al actualizar tecnología. ');
        }
    }

    /**
     * Método que elimina tecnología
     *
     * @param Tecnologia $tecnologia
     * @return void
     * @throws Exception
     */
    public function delete(Tecnologia $tecnologia)
    {
        try {
            // Verificar y eliminar el archivo PDF del S3
            if ($tecnologia->pdf && Storage::disk('s3')->exists($tecnologia->pdf)) {
                Storage::disk('s3')->delete($tecnologia->pdf);
            }

            // Eliminar el registro de la base de datos
            $tecnologia->delete();
        } catch (Exception $e) {
            throw new Exception("Error al eliminar la tecnología: " . $e->getMessage());
        }
    }

    /**
     * Método que obtiene la data del datatable.
     *
     * @param array $params Parametros de paginación
     * @return array
     * @throws Exception
     */
    public function obtenerDataPaginacion(array $params)
    {
        try {
            return $this->model
                ->select(['id', 'nombre', 'descripcion', 'estado'])
                ->when(!empty($params['search']), function ($query) use ($params) {
                    $query->where('id', 'LIKE', '%' . $params['search'] . '%')
                        ->orWhere('nombre', 'LIKE', '%' . $params['search'] . '%')
                        ->orWhere('descripcion', 'LIKE', '%' . $params['search'] . '%')
                        ->orWhere('estado', 'LIKE', '%' . $params['search'] . '%');
                })
                ->when(array_key_exists('dateDesde', $params) && !empty($params['dateDesde']) &&
                    array_key_exists('dateHasta', $params) && !empty($params['dateHasta']), function($query) use ($params){
                        $query->whereBetween('created_at', [
                            $params['dateDesde']  .' 00:00:00', $params['dateHasta'] .' 23:59:59'
                        ]);
                })
                ->skip($params['skip'])
                ->take($params['take'])
                ->orderBy($params['orderColumn'], $params['order'])
                ->get()
                ->toArray();
        } catch (Exception $e) {
            throw new Exception("Error al obtener datos de paginación: " . $e->getMessage());
        }
    }
}
