<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MensajeIncidencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mensaje_incidencias';

    protected $fillable = [
        'incidencia_id',
        'user_id',
        'mensaje',
        'tipo',
        'fecha_mensaje',
        'created_by',
    ];

    protected $casts = [
        'fecha_mensaje' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Tipos de mensaje
    const TIPO_PUBLICO = 'publico';
    const TIPO_INTERNO = 'interno';

    // Relaciones
    public function incidencia(): BelongsTo
    {
        return $this->belongsTo(Incidencia::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function archivoAdjuntos(): MorphMany
    {
        return $this->morphMany(ArchivoAdjunto::class, 'documentable');
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
    public function isInterno(): bool
    {
        return $this->tipo === self::TIPO_INTERNO;
    }

    public function isPublico(): bool
    {
        return $this->tipo === self::TIPO_PUBLICO;
    }
}
