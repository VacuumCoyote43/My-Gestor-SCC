<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CargoJugador extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cargo_jugadores';

    protected $fillable = [
        'total',
        'fecha_emision',
        'fecha_vencimiento',
        'estado',
        'club_id',
        'user_id_jugador',
        'created_by',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Estados posibles
    const ESTADO_DRAFT = 'draft';
    const ESTADO_PENDING_PAYMENT = 'pending_payment';
    const ESTADO_PAYMENT_REGISTERED = 'payment_registered';
    const ESTADO_PAID = 'paid';
    const ESTADO_CANCELLED = 'cancelled';

    // Relaciones
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function jugador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_jugador');
    }

    public function conceptos(): HasMany
    {
        return $this->hasMany(CargoConcepto::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
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
    public function getTotalPagadoAttribute(): float
    {
        return $this->pagos()->where('estado_pago', 'validado')->sum('importe');
    }

    public function isPaid(): bool
    {
        return $this->estado === self::ESTADO_PAID;
    }

    public function canBeEmitted(): bool
    {
        return $this->estado === self::ESTADO_DRAFT 
            && $this->conceptos()->count() > 0;
    }

    public function recalcularEstado(): void
    {
        $totalPagado = $this->getTotalPagadoAttribute();
        
        if ($totalPagado >= $this->total) {
            $this->estado = self::ESTADO_PAID;
        } elseif ($totalPagado > 0) {
            $this->estado = self::ESTADO_PAYMENT_REGISTERED;
        } else {
            $this->estado = self::ESTADO_PENDING_PAYMENT;
        }
        
        $this->save();
    }
}
