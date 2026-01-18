<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre_legal',
        'nif_cif',
        'email',
        'direccion',
        'es_liga',
        'created_by',
    ];

    protected $casts = [
        'es_liga' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function facturasEmitidas(): MorphMany
    {
        return $this->morphMany(Factura::class, 'emisor');
    }

    public function facturasRecibidas(): MorphMany
    {
        return $this->morphMany(Factura::class, 'receptor');
    }
}
