<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    public function users() {
        return $this->belongsToMany(User::class, 'tecnologia_user')->withTimestamps();;
    }
}
