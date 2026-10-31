<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExamResultsRowsImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings
{
    public Collection $rows;

    public function __construct(private readonly string $delimiter = ',')
    {
    }

    public function collection(Collection $rows): void
    {
        $this->rows = $rows;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => $this->delimiter,
            'input_encoding' => 'UTF-8',
        ];
    }
}
