<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'avatar', 'phone', 'is_active',
        'branch_id', 'department_id', 'position_id', 'hire_date', 'contract_type',
        'crmv', 'emergency_contact', 'emergency_phone',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'hire_date' => 'date',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function hasRole($roles): bool
    {
        if ($roles instanceof \Illuminate\Support\Collection) {
            $roles = $roles->pluck('name')->toArray();
        } elseif (is_string($roles)) {
            $roles = [$roles];
        } elseif ($roles instanceof \Spatie\Permission\Models\Role) {
            $roles = [$roles->name];
        }
        if ($this->role && in_array($this->role->slug, $roles)) {
            return true;
        }
        return !empty(array_intersect($roles, $this->getRoleNames()->toArray()));
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'vet_id');
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'vet_id');
    }

    public function createdAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'created_by');
    }
}
