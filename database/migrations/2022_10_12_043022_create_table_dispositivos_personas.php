<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDispositivosPersonas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispositivos_personas', function (Blueprint $table) {
            $table->id();
            $table->string('dispositivos_id')->references('id')->on('dispositivos')->onDelete('cascade');
            $table->foreignId('personas_id')->references('id')->on('personas')->onDelete('cascade');
            //
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dispositivos_personas');
    }
}
