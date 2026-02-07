<?php

namespace App\Exports;

use App\Models\ExamRegistration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExamResultsExport implements FromCollection, WithHeadings, WithMapping
{
    private int $examId;

    public function __construct(int $examId)
    {
        $this->examId = $examId;
    }

    public function collection()
    {
        return ExamRegistration::where('exam_id', $this->examId)
            ->whereHas('result', function ($query) {
                $query->whereNotNull('published_at');
            })
            ->with(['user', 'result', 'exam'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Student',
            'Email',
            'Level Achieved',
            'Total Score',
            'Status',
            'Published At',
        ];
    }

    public function map($registration): array
    {
        $result = $registration->result;

        return [
            $registration->user->first_name . ' ' . $registration->user->last_name,
            $registration->user->email,
            $result?->level_achieved ?? 'N/A',
            $result?->total_score ?? '-',
            $result?->status ?? 'pending',
            $result?->published_at?->format('Y-m-d H:i') ?? '',
        ];
    }
}
