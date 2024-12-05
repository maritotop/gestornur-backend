<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->renameColumn('fechaCreacion', 'created_at');
            $table->renameColumn('fechaModificacion', 'updated_at');
        });
    }
    
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->renameColumn('created_at', 'fechaCreacion');
            $table->renameColumn('updated_at', 'fechaModificacion');
        });
    }
    
};
