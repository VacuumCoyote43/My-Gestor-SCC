<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Incidencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id_creador',
        'asunto',
        'categoria',
        'prioridad',
        'estado',
        'fecha_apertura',
        'fecha_cierre',
        'created_by',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Estados posibles
    const ESTADO_ABIERTA = 'abierta';
    const ESTADO_EN_PROGRESO = 'en_progreso';
    const ESTADO_CERRADA = 'cerrada';

    // Prioridades
    const PRIORIDAD_BAJA = 'baja';
    const PRIORIDAD_MEDIA = 'media';
    const PRIORIDAD_ALTA = 'alta';
    const PRIORIDAD_URGENTE = 'urgente';

    // Relaciones
    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_creador');
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(MensajeIncidencia::class);
    }

    public function modificaciones(): MorphMany
    {
        return $this->morphMany(Modificacion::class, 'modifiable');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper methods
    public function isCerrada(): bool
    {
        return $this->estado === self::ESTADO_CERRADA;
    }

    public function cerrar(): void
    {
        $this->estado = self::ESTADO_CERRADA;
        $this->fecha_cierre = now();
        $this->save();
    }
}
