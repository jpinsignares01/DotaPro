<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Dispositivo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AsignacionDispositivoTest extends TestCase
{
    /**
     * Prueba sencilla de vinculación de dispositivo a persona
     *
     * @return void
     */
    public function test_vinculacion()
    {
        $this->withoutExceptionHandling();

        $vinculacion_response = $this->postJson('/api/v1/dispositivos/asignar', [
            'personas_id' => 1,
            'dispositivo' => array(
                'createdAt' => '2018-02-14T15:56:05Z',
                'id' => 101,
                'serial' => 101,
                'nombre' => 'Torre Dell',
                'sistema_operativo' => 'Linux',
                'tipo_dispositivo' => 'PC',
            ),
        ]);

        $vinculacion_response->assertStatus(200)
            ->assertJson([
                'code' => 201,
            ]);
    }

    /**
     * Prueba sencilla de desvinculación de dispositivo a persona
     *
     * @return void
     */
    public function test_desvinculacion()
    {
        $this->withoutExceptionHandling();

        $vinculacion_response = $this->postJson('/api/v1/dispositivos/desvincular', [
            'personas_id' => 1,
            'dispositivos_id' => 101,
        ]);

        $vinculacion_response->assertStatus(200)
            ->assertJson([
                'code' => 201,
            ]);
    }
}
