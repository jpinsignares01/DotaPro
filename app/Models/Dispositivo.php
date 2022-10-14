<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dispositivo extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = "dispositivos";
    
    /**
     * Obtener Persona a la que se le asignó.
     */
    public function persona() {
        return $this->hasOne(Persona::class, 'id', 'personas_id');
    }
}
