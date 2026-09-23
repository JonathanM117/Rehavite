<?php

namespace App\Exports\Sheets;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EvolutionNotesSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $patient;

    public function __construct(Patient $patient)
    {
        $this->patient = $patient;
    }

    public function title(): string
    {
        return 'Notas Evolucion';
    }

    public function array(): array
    {
        $rows = [
            ['Consulta #', 'Fecha Nota', 'Nota de Evolucion'],
        ];

        foreach ($this->patient->consultations as $i => $c) {
            foreach ($c->evolutionNotes as $note) {
                $rows[] = [
                    'Consulta ' . ($i + 1),
                    $note->date ? \Carbon\Carbon::parse($note->date)->format('d/m/Y') : $note->created_at->format('d/m/Y'),
                    $note->note ?? '—',
                ];
            }
        }

        if (count($rows) === 1) {
            $rows[] = ['Sin notas registradas'];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2C3E50'],
            ]],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 15, 'B' => 14, 'C' => 80];
    }
}
