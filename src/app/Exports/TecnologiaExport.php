<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TecnologiaExport implements WithHeadings, FromArray, WithTitle
{
    private $data;
    private $headings;
    private $title;

    public function __construct(array $parametros)
    {
        $this->data     = $parametros['data'];
        $this->headings = $parametros['headings'];
        $this->title    = $parametros['title'];
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

    /**
     * Array de la consulta enviada desde el controller.
     *
     * @return array $this->data
     */
    public function array(): array {
        return $this->data;
    }
}
