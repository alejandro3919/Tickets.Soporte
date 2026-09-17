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
    Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        $table->string('titulo'); // Texto obligatorio
        $table->string('solicitante'); // Nombre de quien reporta
        $table->string('correo'); // Correo valido
        $table->enum('categoria', ['Hardware', 'Software', 'Red', 'Otro']); // Opciones fijas
        $table->enum('prioridad', ['Baja', 'Media', 'Alta']); // Opciones fijas
        $table->enum('estado', ['Abierto', 'En proceso', 'Cerrado'])->default('Abierto'); // Estado por defecto
        $table->text('descripcion'); // Descripcion del problema
        $table->timestamps(); // created_at y updated_at automaticos
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
