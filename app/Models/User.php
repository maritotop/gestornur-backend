<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol', // Mantén 'rol' aquí como está en la base de datos
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Setea la contraseña de manera segura
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // Relación con la tabla de sesiones
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    // Relación con los tokens de restablecimiento de contraseña
    public function passwordResetTokens()
    {
        return $this->hasMany(PasswordResetToken::class);
    }

    // Método para acceder al campo 'rol' como 'role'
    public function getRoleAttribute()
    {
        return $this->attributes['rol'];
    }

    // Método para establecer el valor de 'rol' cuando se asigna 'role'
    public function setRoleAttribute($value)
    {
        $this->attributes['rol'] = $value;
    }
}

