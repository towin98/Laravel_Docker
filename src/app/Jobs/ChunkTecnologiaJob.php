<?php

namespace App\Jobs;

use App\Models\Tecnologia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChunkTecnologiaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $batch;

    public function __construct($batch)
    {
        $this->batch = $batch;
    }

    public function handle()
    {
        $data = [];

        foreach ($this->batch as $fila) {
            $data[] = [
                'nombre' => $fila['nombre'],
                'descripcion' => $fila['descripcion'],
                'estado' => $fila['estado'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Tecnologia::insert($data);
    }
}
