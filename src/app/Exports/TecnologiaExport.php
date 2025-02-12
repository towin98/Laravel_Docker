<?php

namespace App\Exports;

use App\Models\Tecnologia;
use DragonCode\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
// use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TecnologiaExport implements WithHeadings, WithTitle, FromQuery, ShouldQueue
{
    // private $data;
    private $headings;
    private $title;

    public function __construct(array $parametros)
    {
        // $this->data     = $parametros['data'];
        $this->headings = $parametros['headings'];
        $this->title    = $parametros['title'];
    }

    public function query()
    {
        return Tecnologia::select(['id', 'nombre', 'descripcion', 'estado', 'created_at'])->orderBy('id', 'desc');
    }

    /**
     * Título de las columnas
     *
     * @return void
     */
    public function headings(): array {
        return $this->headings;
    }

    /**
     * Título de la Hoja
     *
     * @return string
     */
    public function title(): string {
        return $this->title;
    }

    // Definir el tamaño del chunk
    public function chunkSize(): int
    {
        return 200;
    }

    /**
     * Array de la consulta enviada desde el controller.
     *
     * @return array $this->data
     */
    // public function array(): array {
    //     return $this->data;
    // }
}
