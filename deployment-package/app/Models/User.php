<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
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
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function cashBooks(): HasMany
    {
        return $this->hasMany(CashBook::class, 'created_by');
    }

    public function journeyVouchers(): HasMany
    {
        return $this->hasMany(JourneyVoucher::class, 'created_by');
    }

    public function vehicleBills(): HasMany
    {
        return $this->hasMany(VehicleBill::class, 'created_by');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    public function isAccountant(): bool
    {
        return $this->hasRole('Accountant');
    }

    public function isFleetManager(): bool
    {
        return $this->hasRole('Fleet Manager');
    }

    public function hasAdminAccess(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function canAccessCompleteBackups(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canManageDailyBackups(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canRestoreBackups(): bool
    {
        return $this->isSuperAdmin();
    }
}
