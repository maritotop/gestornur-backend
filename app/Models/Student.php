<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si sigue la convención pluralizada)
    protected $table = 'students';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'nroRegistro',
        'nombreCompleto',
    ];

    // Relación con documentos si es necesaria
    public function documents()
    {
        return $this->hasMany(Document::class, 'estudianteId');
    }
}
