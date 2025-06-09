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
        Schema::create('tarea_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("tarea_id");
            $table->unsignedBigInteger("material_id");
            $table->timestamps();

            $table->foreign("tarea_id")->on("tareas")->references("id");
            $table->foreign("material_id")->on("materials")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarea_materials');
    }
};
