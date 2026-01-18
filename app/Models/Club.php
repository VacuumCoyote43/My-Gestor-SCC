<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Club extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clubes';

    protected $fillable = [
        'nombre',
        'cif',
        'email',
        'direccion',
        'gestor_id',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones
    public function gestor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cargoJugadores(): HasMany
    {
        return $this->hasMany(CargoJugador::class);
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
