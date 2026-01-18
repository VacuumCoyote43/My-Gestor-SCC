<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Pago extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'factura_id',
        'cargo_jugador_id',
        'importe',
        'fecha_pago',
        'metodo_pago',
        'estado_pago',
        'validated_at',
        'validated_by',
        'created_by',
    ];

    protected $casts = [
        'importe' => 'decimal:2',
        'fecha_pago' => 'date',
        'validated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Estados posibles
    const ESTADO_REGISTRADO = 'registrado';
    const ESTADO_VALIDADO = 'validado';
    const ESTADO_RECHAZADO = 'rechazado';

    // Relaciones
    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    public function cargoJugador(): BelongsTo
    {
        return $this->belongsTo(CargoJugador::class);
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function archivoAdjuntos(): MorphMany
    {
        return $this->morphMany(ArchivoAdjunto::class, 'documentable');
    }

    public function modificaciones(): MorphMany
    {
        return $this->morphMany(Modificacion::class, 'modifiable');
    }

    // Helper methods
    public function isValidado(): bool
    {
        return $this->estado_pago === self::ESTADO_VALIDADO;
    }

    public function isRechazado(): bool
    {
        return $this->estado_pago === self::ESTADO_RECHAZADO;
    }

    public function canBeValidated(): bool
    {
        return $this->estado_pago === self::ESTADO_REGISTRADO;
    }
}
