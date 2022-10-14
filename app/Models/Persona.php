<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = "personas";
    
    /**
     * Obtener dispositivos asignados.
     */
    public function dispositivos() {
        return $this->hasMany(Dispositivo::class, 'personas_id', 'id');
    }
}