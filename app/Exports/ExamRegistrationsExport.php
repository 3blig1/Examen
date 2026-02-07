<?php

namespace App\Exports;

use App\Models\ExamRegistration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExamRegistrationsExport implements FromCollection, WithHeadings, WithMapping
{
    private int $examId;

    public function __construct(int $examId)
    {
        $this->examId = $examId;
    }

    public function collection()
    {
        return ExamRegistration::where('exam_id', $this->examId)
            ->with(['user', 'exam'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Student',
            'Email',
            'Country',
            'Status',
            'Payment Status',
            'Registered At',
        ];
    }

    public function map($registration): array
    {
        return [
            $registration->user->first_name . ' ' . $registration->user->last_name,
            $registration->user->email,
            $registration->user->country,
            $registration->status,
            $registration->payment_status,
            $registration->created_at?->format('Y-m-d H:i'),
        ];
    }
}
