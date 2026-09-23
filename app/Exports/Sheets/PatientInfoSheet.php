<?php

namespace App\Exports\Sheets;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PatientInfoSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $patient;

    public function __construct(Patient $patient)
    {
        $this->patient = $patient;
    }

    public function title(): string
    {
        return 'Datos Personales';
    }

    public function array(): array
    {
        $r = $this->patient->medicalRecord;
        $pnp = $r ? ($r->personal_non_pathological ?? []) : [];

        $rows = [
            ['EXPEDIENTE CLINICO — REHAVITE'],
            ['Generado el:', now()->format('d/m/Y H:i')],
            [],
            ['DATOS PERSONALES'],
            ['Nombre completo', $this->patient->full_name],
            ['Expediente', $this->patient->expediente_id],
            ['Fecha de nacimiento', $this->patient->birth_date?->format('d/m/Y') ?? '—'],
            ['Edad', $this->patient->age ?? '—'],
            ['Genero', $this->patient->gender ?? '—'],
            ['Telefono', $this->patient->phone ?? '—'],
            ['Correo electronico', $this->patient->email ?? '—'],
            ['Diagnostico inicial', $this->patient->diagnosis ?? '—'],
            ['Fisioterapeuta asignado', $this->patient->user?->name ?? '—'],
            ['Fecha de registro', $this->patient->created_at->format('d/m/Y')],
            [],
            ['DATOS DEL EXPEDIENTE'],
            ['Estado civil', $r?->marital_status ?? '—'],
            ['Domicilio', $r?->address ?? '—'],
            ['Ocupacion', $r?->occupation ?? '—'],
            ['Lateralidad', $r?->laterality ?? '—'],
            ['Dependencia economica', $r?->economic_dependency ?? '—'],
            ['Cuidador primario', $r?->primary_caregiver ?? '—'],
            [],
            ['FUENTE DE REFERENCIA'],
            ['Como se entero', $pnp['referencia_como_entero'] ?? '—'],
            ['Medico remitente', $pnp['referencia_medico'] ?? '—'],
            [],
            ['CONTACTO DE EMERGENCIA'],
            ['Nombre', $pnp['emergencia_nombre'] ?? '—'],
            ['Telefono', $pnp['emergencia_telefono'] ?? '—'],
            ['Parentesco', $pnp['emergencia_parentesco'] ?? '—'],
            [],
            ['REPRESENTANTE LEGAL'],
            ['Nombre', $r?->guardian_name ?? '—'],
            ['Fecha de nacimiento', $r?->guardian_birth_date?->format('d/m/Y') ?? '—'],
            ['Edad', $r?->guardian_age ?? '—'],
            ['Genero', $r?->guardian_gender ?? '—'],
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1  => ['font' => ['bold' => true, 'size' => 14]],
            4  => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D0E4F7']]],
            16 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D0E4F7']]],
            23 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D0E4F7']]],
            27 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D0E4F7']]],
            31 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D0E4F7']]],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 50];
    }
}
