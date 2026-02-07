<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'date',
        'registration_deadline',
        'exam_type',
        'available_modules',
        'max_participants',
        'registered_count',
        'price',
        'price_per_module',
        'location',
        'status',
    ];

    protected $casts = [
        'date' => 'datetime',
        'registration_deadline' => 'datetime',
        'price' => 'decimal:2',
        'price_per_module' => 'decimal:2',
        'available_modules' => 'array',
    ];

    public function getPricePerModuleAttribute($value)
    {
        return $value ?? '0.00';
    }

    /**
     * Get the registrations for this exam
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(ExamRegistration::class);
    }

    /**
     * Get the results for this exam
     */
    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    /**
     * Check if exam is available for registration
     */
    public function isAvailable(): bool
    {
        $deadlineOk = $this->registration_deadline
            ? $this->registration_deadline->gte(now())
            : true;

        return $this->status === 'upcoming' && 
               $this->registered_count < $this->max_participants &&
               $this->date > now() &&
               $deadlineOk;
    }

    /**
     * Get available spots
     */
    public function getAvailableSpotsAttribute(): int
    {
        return $this->max_participants - $this->registered_count;
    }

    /**
     * Refresh exam statuses based on current date/time.
     * - Upcoming -> Ongoing on exam day when time has started
     * - Upcoming/Ongoing -> Completed after the exam day
     */
    public static function refreshStatuses(): void
    {
        $now = now();
        $startOfDay = $now->copy()->startOfDay();
        $endOfDay = $now->copy()->endOfDay();

        // Past days => completed
        static::whereIn('status', ['upcoming', 'ongoing'])
            ->where('date', '<', $startOfDay)
            ->update(['status' => 'completed']);

        // Exam day and time reached => ongoing
        static::where('status', 'upcoming')
            ->whereBetween('date', [$startOfDay, $endOfDay])
            ->where('date', '<=', $now)
            ->update(['status' => 'ongoing']);
    }
}
