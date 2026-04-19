<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'date_of_birth',
        'birth_place',
        'sex',
        'birth_country',
        'nationality',
        'address',
        'locale',
        'password',
        'must_change_password',
        'role',
        'is_active',
        'admin_can_manage_users',
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
            'date_of_birth' => 'date',
            'must_change_password' => 'boolean',
            'admin_can_manage_users' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->isSuperAdmin()) {
                $user->role = 'admin';
                $user->admin_can_manage_users = true;
            }
        });

        static::deleting(function (User $user) {
            if ($user->isSuperAdmin()) {
                return false;
            }
        });
    }

    /**
     * Get exam registrations for this user
     */
    public function examRegistrations(): HasMany
    {
        return $this->hasMany(ExamRegistration::class);
    }

    /**
     * Get exam results for this user
     */
    public function examResults(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    /**
     * Get payment histories for this user
     */
    public function paymentHistories(): HasMany
    {
        return $this->hasMany(PaymentHistory::class);
    }

    /**
     * Get activity logs for this user
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get email verification record
     */
    public function emailVerification(): HasOne
    {
        return $this->hasOne(EmailVerification::class);
    }

    /**
     * Check if email is verified
     */
    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->isSuperAdmin();
    }

    /**
     * Check if user is super admin (fixed account)
     */
    public function isSuperAdmin(): bool
    {
        $superAdminEmail = config('app.super_admin_email');

        return !empty($superAdminEmail) && $this->email === $superAdminEmail;
    }

    /**
     * Check if user is examiner
     */
    public function isExaminer(): bool
    {
        return $this->role === 'examiner';
    }

    /**
     * Check if user is student
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if user can manage (activate/deactivate) users
     */
    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin() || ($this->isAdmin() && $this->admin_can_manage_users);
    }
}
