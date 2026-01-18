<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CargoConcepto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cargo_conceptos';

    protected $fillable = [
        'cargo_jugador_id',
        'descripcion',
        'total_linea',
        'created_by',
    ];

    protected $casts = [
        'total_linea' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones
    public function cargoJugador(): BelongsTo
    {
        return $this->belongsTo(CargoJugador::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
