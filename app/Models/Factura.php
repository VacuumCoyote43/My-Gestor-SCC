<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Factura extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero',
        'serie',
        'fecha_factura',
        'fecha_vencimiento',
        'estado',
        'subtotal',
        'impuestos',
        'total',
        'emisor_type',
        'emisor_id',
        'receptor_type',
        'receptor_id',
        'created_by',
    ];

    protected $casts = [
        'fecha_factura' => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
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
    public function emisor(): MorphTo
    {
        return $this->morphTo();
    }

    public function receptor(): MorphTo
    {
        return $this->morphTo();
    }

    public function conceptos(): HasMany
    {
        return $this->hasMany(FacturaConcepto::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
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
            && $this->fecha_factura 
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
