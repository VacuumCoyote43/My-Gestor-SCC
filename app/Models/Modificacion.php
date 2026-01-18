<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Modificacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'modificaciones';

    protected $fillable = [
        'modifiable_type',
        'modifiable_id',
        'accion',
        'cambios',
        'comentario',
        'created_by',
    ];

    protected $casts = [
        'cambios' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Acciones posibles
    const ACCION_CREATED = 'created';
    const ACCION_UPDATED = 'updated';
    const ACCION_DELETED = 'deleted';
    const ACCION_EMITTED = 'emitted';
    const ACCION_PAYMENT_REGISTERED = 'payment_registered';
    const ACCION_VALIDATED = 'validated';
    const ACCION_REJECTED = 'rejected';
    const ACCION_STATUS_CHANGED = 'status_changed';

    // Relaciones
    public function modifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
