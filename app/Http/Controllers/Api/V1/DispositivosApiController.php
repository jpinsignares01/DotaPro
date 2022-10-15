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
                $dispositivo->serial = $dispositivo->id;
            }
        } catch (Exception $e) {
            return response('Se ha presentado un error solicitando el listado de dispositivos disponibles. Error: ' . $e->getMessage(), 400);
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
    * Asignar el dispositivo a persona
    */
    public function asignar(Request $request)
    {
        if (Dispositivo::where('serial', $request->dispositivo['id'])->first()) {
            return response('El dispositivo ya se encuentra asignado.', 400);
        } else {
            $nuevoDispositivo = new Dispositivo;
            $nuevoDispositivo->serial = $request->dispositivo['id'];
            $nuevoDispositivo->nombre = $request->dispositivo['nombre'];
            $nuevoDispositivo->tipo_dispositivo = $request->dispositivo['tipo_dispositivo'];
            $nuevoDispositivo->sistema_operativo = $request->dispositivo['sistema_operativo'];
            $nuevoDispositivo->personas_id = $request->personas_id;
            $nuevoDispositivo->save();
        }
        //
        $response = [
            'code' => 201,
            'data' => [
                "dispositivo_asignado" => $nuevoDispositivo,
            ],
        ];
        //
        return json_decode(json_encode($response), true);
    }

    /* 
    * Desvincular el dispositivo a persona
    */
    public function desvincular(Request $request)
    {
        $dispositivo = Dispositivo::where('serial', $request->dispositivos_id)->where('personas_id', $request->personas_id)->first()->delete();
        //
        $response = [
            'code' => 201,
            'data' => "Se ha desvinculado el dispositivo.",
        ];
        //
        return json_decode(json_encode($response), true);
    }
}
