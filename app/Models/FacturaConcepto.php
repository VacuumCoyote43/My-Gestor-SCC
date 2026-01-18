<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaConcepto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'factura_conceptos';

    protected $fillable = [
        'factura_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'tipo_impuesto',
        'total_linea',
        'created_by',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'tipo_impuesto' => 'decimal:2',
        'total_linea' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones
    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
