<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_registration_id',
        'exam_id',
        'user_id',
        'reading_score',
        'listening_score',
        'writing_score',
        'speaking_score',
        'total_score',
        'level_achieved',
        'status',
        'published_at',
        'remarks',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Get the exam registration
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(ExamRegistration::class);
    }

    /**
     * Get the exam
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate total score from individual scores based on selected modules
     */
    public function calculateTotalScore(): int
    {
        // Get selected modules from registration
        $selectedModules = $this->registration->selected_modules ?? [];

        $scores = [];

        if (in_array('reading', $selectedModules) && $this->reading_score !== null) {
            $scores[] = $this->reading_score;
        }
        if (in_array('writing', $selectedModules) && $this->writing_score !== null) {
            $scores[] = $this->writing_score;
        }
        if (in_array('listening', $selectedModules) && $this->listening_score !== null) {
            $scores[] = $this->listening_score;
        }
        if (in_array('speaking', $selectedModules) && $this->speaking_score !== null) {
            $scores[] = $this->speaking_score;
        }

        return !empty($scores) ? (int)(array_sum($scores) / count($scores)) : 0;
    }

    /**
     * Check if exam was passed
     */
    public function isPassed(): bool
    {
        return $this->status === 'passed';
    }

    /**
     * Publish results
     */
    public function publish(): void
    {
        $this->update([
            'published_at' => now(),
            'total_score' => $this->calculateTotalScore(),
        ]);
    }
}
