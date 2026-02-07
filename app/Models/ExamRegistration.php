<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'status',
        'payment_status',
        'payment_date',
        'amount_paid',
        'confirmed_at',
        'selected_modules',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'selected_modules' => 'array',
    ];

    /**
     * Get the user who registered
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the exam this registration is for
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the result for this registration
     */
    public function result(): HasOne
    {
        return $this->hasOne(ExamResult::class);
    }

    /**
     * Get payment histories for this registration
     */
    public function paymentHistories(): HasMany
    {
        return $this->hasMany(PaymentHistory::class, 'exam_registration_id');
    }

    /**
     * Check if registration is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Confirm registration
     */
    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_date' => now(),
            'confirmed_at' => now(),
        ]);
    }
}
