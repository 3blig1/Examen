<?php

namespace App\Exports;

use App\Models\ExamRegistration;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExamResultsTemplateExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly int $examId)
    {
    }

    public function collection(): Collection
    {
        return ExamRegistration::query()
            ->where('exam_id', $this->examId)
            ->where('status', '!=', 'cancelled')
            ->with(['user', 'result'])
            ->orderBy('id')
            ->get()
            ->map(function (ExamRegistration $registration): array {
                $modules = $registration->selected_modules ?: ['reading', 'listening', 'writing', 'speaking'];

                return [
                    'first_name' => $registration->user->first_name,
                    'last_name' => $registration->user->last_name,
                    'date_of_birth' => $registration->user->date_of_birth?->format('Y-m-d'),
                    'selected_modules' => implode(',', $modules),
                    'reading_score' => in_array('reading', $modules, true) ? $registration->result?->reading_score : null,
                    'listening_score' => in_array('listening', $modules, true) ? $registration->result?->listening_score : null,
                    'writing_score' => in_array('writing', $modules, true) ? $registration->result?->writing_score : null,
                    'speaking_score' => in_array('speaking', $modules, true) ? $registration->result?->speaking_score : null,
                    'level_achieved' => $registration->result?->level_achieved,
                    'status' => $registration->result?->status,
                    'remarks' => $registration->result?->remarks,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'first_name',
            'last_name',
            'date_of_birth',
            'selected_modules',
            'reading_score',
            'listening_score',
            'writing_score',
            'speaking_score',
            'level_achieved',
            'status',
            'remarks',
        ];
    }
}
