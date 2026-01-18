<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class ArchivoAdjunto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'archivo_adjuntos';

    protected $fillable = [
        'nombre_original',
        'ruta',
        'tipo',
        'tamano',
        'documentable_type',
        'documentable_id',
        'created_by',
    ];

    protected $casts = [
        'tamano' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper methods
    public function getUrl(): string
    {
        return Storage::url($this->ruta);
    }

    public function download()
    {
        return Storage::download($this->ruta, $this->nombre_original);
    }

    public function getTamanoFormateado(): string
    {
        $bytes = $this->tamano;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
