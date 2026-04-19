<?php

namespace App\Exports;

use App\Models\ExamRegistration;
use App\Models\ExamExaminerAssignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExamRegistrationsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    private int $examId;
    /** @var string[] */
    private array $writtenExaminers = [];
    /** @var string[] */
    private array $oralExaminers = [];

    public function __construct(int $examId)
    {
        $this->examId = $examId;
        $this->loadExaminerAssignments();
    }

    private function loadExaminerAssignments(): void
    {
        $assignments = ExamExaminerAssignment::where('exam_id', $this->examId)
            ->with('examiner')
            ->get();

        $written = [];
        $oral = [];

        foreach ($assignments as $assignment) {
            $modules = $assignment->modules ?? [];
            $examiner = $assignment->examiner;
            $name = $examiner
                ? trim($examiner->first_name . ' ' . $examiner->last_name)
                : '';

            if ($name === '') {
                continue;
            }

            if (array_intersect($modules, ['reading', 'listening', 'writing'])) {
                $written[] = $name;
            }

            if (in_array('speaking', $modules, true)) {
                $oral[] = $name;
            }
        }

        sort($written);
        sort($oral);

        $this->writtenExaminers = array_values(array_unique($written));
        $this->oralExaminers = array_values(array_unique($oral));
    }

    public function collection()
    {
        return ExamRegistration::where('exam_id', $this->examId)
            ->with(['user', 'exam', 'result'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Prüfungsstufe',
            'Titel Vor',
            'Vorname',
            'Familienname',
            'Titel Nach',
            'Geschlecht',
            'Geburts-Ort',
            'Geburts-Land',
            'Nationalität',
            'Geburts-Datum',
            'Prüfer/In Nr 1 schriftlich',
            'Prüfer/In Nr 2 schriftlich',
            'Prüfer/In Nr 1 mündlich',
            'Prüfer/In Nr 2 mündlich',
            'Modul Lesen',
            'Modul Hören',
            'Modul Schreiben',
            'Modul Sprechen',
        ];
    }

    public function map($registration): array
    {
        $selectedModules = $registration->selected_modules ?? [];

        return [
            $registration->exam?->name,
            $registration->user?->title,
            $registration->user?->first_name,
            $registration->user?->last_name,
            '',
            $registration->user?->sex,
            $registration->user?->birth_place,
            $registration->user?->birth_country,
            $registration->user?->nationality,
            $registration->user?->date_of_birth?->format('Y-m-d'),
            $this->writtenExaminers[0] ?? '',
            $this->writtenExaminers[1] ?? '',
            $this->oralExaminers[0] ?? '',
            $this->oralExaminers[1] ?? '',
            in_array('reading', $selectedModules, true) ? 'J' : '',
            in_array('listening', $selectedModules, true) ? 'J' : '',
            in_array('writing', $selectedModules, true) ? 'J' : '',
            in_array('speaking', $selectedModules, true) ? 'J' : '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:R1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFCC00'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'name' => 'Arial',
                'size' => 11,
                'bold' => false,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(21.75);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18.57,
            'B' => 13.14,
            'C' => 14.00,
            'D' => 18.71,
            'E' => 14.86,
            'F' => 16.14,
            'G' => 16.71,
            'H' => 18.29,
            'I' => 15.71,
            'J' => 20.00,
            'K' => 27.57,
            'L' => 11.00,
            'M' => 27.14,
            'N' => 11.00,
            'O' => 17.29,
            'P' => 17.14,
            'Q' => 21.14,
            'R' => 20.71,
        ];
    }
}
