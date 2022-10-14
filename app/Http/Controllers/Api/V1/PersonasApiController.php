<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Persona;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PersonasApiController extends Controller
{
    /* 
    * Listado de personas existentes
    */
    public function index(Request $request)
    {
        return Persona::with('dispositivos')->get()->toArray();
    }
}
