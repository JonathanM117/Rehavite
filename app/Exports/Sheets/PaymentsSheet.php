<?php

namespace App\Exports\Sheets;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $patient;

    public function __construct(Patient $patient)
    {
        $this->patient = $patient;
    }

    public function title(): string
    {
        return 'Pagos';
    }

    public function array(): array
    {
        $rows = [
            ['Fecha', 'Concepto', 'Monto', 'Metodo', 'Notas'],
        ];

        $total = 0;
        foreach ($this->patient->payments as $p) {
            $rows[] = [
                $p->created_at->format('d/m/Y'),
                $p->concept ?? '—',
                '$' . number_format($p->amount ?? 0, 2),
                $p->method ?? '—',
                $p->notes ?? '—',
            ];
            $total += $p->amount ?? 0;
        }

        if (count($rows) === 1) {
            $rows[] = ['Sin pagos registrados'];
        } else {
            $rows[] = [];
            $rows[] = ['', 'TOTAL', '$' . number_format($total, 2), '', ''];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '27AE60'],
            ]],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 14, 'B' => 30, 'C' => 14, 'D' => 18, 'E' => 35];
    }
}
