<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->string("codigo", 255)->unique();
            $table->bigInteger("nro_cod")->unique();
            $table->text("descripcion");
            $table->unsignedBigInteger("area_id");
            $table->unsignedBigInteger("producto_id");
            $table->unsignedBigInteger("user_id");
            $table->string("estado");
            $table->date("fecha_registro");
            $table->timestamps();

            $table->foreign("area_id")->on("areas")->references("id");
            $table->foreign("producto_id")->on("productos")->references("id");
            $table->foreign("user_id")->on("users")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
