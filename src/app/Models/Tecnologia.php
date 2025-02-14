<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Tecnologia extends Model
{
    use HasFactory;

    protected $table = 'tecnologias';

    protected $fillable = ['nombre', 'descripcion', 'pdf', 'estado'];

    /**
     * Obtiene los usuarios que tienen la tecnología asignada.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'tecnologia_user')->withTimestamps();;
    }

    ## CONFIGURACIONES LARATABLES

    /**
     * Additional columns to be loaded for datatables.
     *
     * @return array
     */
    public static function laratablesAdditionalColumns()
    {
        return [
            'pdf'
        ];
    }

    /**
     * Retorna el html para la columna action
     *
     * @param \App\Models\Tecnologia
     * @return string
     */
    public static function laratablesCustomAction($tecnologia)
    {
        return view('tecnologias.optionDataTables.action', compact('tecnologia'))->render();
    }

    /**
     * Retorna el html para la columna Descargar
     *
     * @param \App\Models\Tecnologia
     * @return string
     */
    public static function laratablesCustomDescargar($tecnologia)
    {
        return view('tecnologias.optionDataTables.descargar', compact('tecnologia'))->render();
    }

    /**
     * Retorna el html para la columna Descargar
     *
     * @param \App\Models\Tecnologia
     * @return string
     */
    public static function laratablesCustomCheckbox()
    {
        return "";
    }

    /**
     * Searching the user(column merged) in the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder
     * @param string search term
     * @param \Illuminate\Database\Eloquent\Builder
     */
    public static function laratablesSearchCheckbox($query, $searchValue)
    {
        return $query;
    }

    /**
     * Manipulando collection.
     *
     * @param \Illuminate\Support\Collection
     * @return \Illuminate\Support\Collection
     */
    public static function laratablesModifyCollection($tecnologia)
    {
        return $tecnologia->map(function ($tecnologia) {
            $texto                   = substr($tecnologia->descripcion, 0, 50);

            $tecnologia->descripcion = strlen($tecnologia->descripcion) > 50 ? $texto . ' ...' : $texto;
            $tecnologia->pdf         = $tecnologia->pdf ?? null;
            return $tecnologia;
        });
    }
}
