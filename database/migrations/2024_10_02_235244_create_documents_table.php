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
        Schema::create('documents', function (Blueprint $table) {
           $table->id();
           $table->foreignId('estudianteId')->constrained('students')->onDelete('restrict');
           $table->enum('tipo', [
                'certificadoNacimiento',
                'tituloBachiller',
                'carnetIdentidad',
                'certificadoEstudio',
                'otroDocumento'
            ]);
            $table->timestamp('fechaCreacion')->useCurrent();
            $table->timestamp('fechaModificacion')->useCurrent()->nullable()->onUpdate('CURRENT_TIMESTAMP');
            $table->enum('estado', [
                'activo',
                'archivado',
                'eliminado'
            ]);
            $table->String ('documentoURL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
