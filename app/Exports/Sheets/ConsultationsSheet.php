<?php

namespace App\Exports\Sheets;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConsultationsSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $patient;

    public function __construct(Patient $patient)
    {
        $this->patient = $patient;
    }

    public function title(): string
    {
        return 'Consultas';
    }

    public function array(): array
    {
        $rows = [
            ['#', 'Fecha', 'Motivo', 'Diagnostico', 'Pronostico', 'Estado', 'Fisioterapeuta'],
        ];

        foreach ($this->patient->consultations as $i => $c) {
            $rows[] = [
                $i + 1,
                $c->created_at->format('d/m/Y'),
                $c->reason ?? '—',
                $c->diagnosis ?? '—',
                $c->prognosis ?? '—',
                $c->status === 'active' ? 'Activa' : 'Inactiva',
                $c->user?->name ?? '—',
            ];
        }

        if (count($rows) === 1) {
            $rows[] = ['Sin consultas registradas'];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1ABC9C'],
            ], 'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 5, 'B' => 14, 'C' => 35, 'D' => 35, 'E' => 25, 'F' => 12, 'G' => 25];
    }
}
