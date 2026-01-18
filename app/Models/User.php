<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'active',
        'last_login_at',
        'last_login_ip',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Relaciones
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function clubesGestionados(): HasMany
    {
        return $this->hasMany(Club::class, 'gestor_id');
    }

    public function cargoJugadores(): HasMany
    {
        return $this->hasMany(CargoJugador::class, 'user_id_jugador');
    }

    public function incidenciasCreadas(): HasMany
    {
        return $this->hasMany(Incidencia::class, 'user_id_creador');
    }

    public function mensajesIncidencia(): HasMany
    {
        return $this->hasMany(MensajeIncidencia::class);
    }

    public function pagosValidados(): HasMany
    {
        return $this->hasMany(Pago::class, 'validated_by');
    }

    // Helper methods
    public function hasRole(string $roleName): bool
    {
        return $this->role?->nombre === $roleName;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isProveedor(): bool
    {
        return $this->hasRole('proveedor');
    }

    public function isGestorClub(): bool
    {
        return $this->hasRole('gestor_club');
    }

    public function isJugador(): bool
    {
        return $this->hasRole('jugador');
    }
}
