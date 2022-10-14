<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Persona;
use App\Models\Dispositivo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class DispositivosApiController extends Controller
{
    /* 
    * Listado de dispositivos existentes desde el endpoint externo
    */
    public function index(Request $request)
    {
        //Recibimos el listado de dispositivos desde endpoint externo
        $dispositivos = json_decode(Http::get('https://mockend.com/jpinsignares01/dispositivos_tecnologia/dispositivos'));
        //Preparamos los registros de dispositivos asignados para limpiar la lista y que no se muestren
        $dispositivosAsignadosGeneral = Dispositivo::all()->pluck('id')->toArray();
        $dispositivosAsignados = Persona::with('dispositivos')->find($request->personas_id)->dispositivos->toArray();

        //Eliminamos del arreglo todos aquellos dispositivos que ya se encuentran asignados en la base de datos
        try {
            foreach ($dispositivos as $key => $dispositivo) {
                if (array_search($dispositivo->id, $dispositivosAsignadosGeneral) !== false) unset($dispositivos[$key]);
            }
        } catch (Exception $e) {
            return response('Error: Se ha presentado un error solicitando el listado de dispositivos disponibles.', 400);
        }
        //
        $response = [
            'code' => 200,
            'data' => [
                "no_asignados" => $dispositivos,
                "asignados" => $dispositivosAsignados,
            ],
        ];
        //
        return json_decode(json_encode($response), true);
    }

    /* 
    * Validar si el dispositivo está disponible para asignarse
    */
    public function busqueda(Request $request)
    {
        $dispositivo = Dispositivo::with('persona')->where('serial', $request->dispositivo['id'])->first();
        //
        return ($dispositivo ?? false);
    }
}
