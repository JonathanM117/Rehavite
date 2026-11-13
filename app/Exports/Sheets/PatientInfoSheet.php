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
        $p = $this->patient;

        $rows = [
            ['EXPEDIENTE CLINICO — REHAVITE'],
            ['Generado el:', now()->format('d/m/Y H:i')],
            [],
            ['DATOS PERSONALES'],
            ['Nombre completo', $p->full_name],
            ['Expediente', $p->expediente_id],
            ['Fecha de nacimiento', $p->birth_date?->format('d/m/Y') ?? '—'],
            ['Edad', $p->manual_age ?? $p->age ?? '—'],
            ['Genero', $p->gender_label ?? '—'],
            ['Telefono', $p->phone ?? '—'],
            ['Correo electronico', $p->email ?? '—'],
            ['Diagnostico inicial', $p->diagnosis ?? '—'],
            ['Fisioterapeuta asignado', $p->user?->name ?? '—'],
            ['Fecha de registro', $p->created_at->format('d/m/Y')],
            [],
            ['DATOS DEL EXPEDIENTE'],
            ['Estado civil', $p->marital_status ?? '—'],
            ['Domicilio', trim(($p->street_address ?? '') . ' ' . ($p->neighborhood ?? '')) ?: '—'],
            ['Ocupacion', $p->occupation ?? '—'],
            ['Lateralidad', $p->laterality ?? '—'],
            ['Dependencia economica', $p->economic_dependency ?? '—'],
            ['Cuidador primario', $p->primary_caregiver ?? '—'],
            [],
            ['FUENTE DE REFERENCIA'],
            ['Como se entero', ($p->referral_source === 'Otro' ? $p->referral_source_other : $p->referral_source) ?? '—'],
            ['Medico remitente', $p->referring_doctor ?? '—'],
            [],
            ['CONTACTO DE EMERGENCIA'],
            ['Nombre', $p->emergency_contact_name ?? '—'],
            ['Telefono', $p->emergency_contact_phone ?? '—'],
            ['Parentesco', $p->emergency_contact_relationship ?? '—'],
            [],
            ['REPRESENTANTE LEGAL'],
            ['Nombre', $p->guardian_name ?? '—'],
            ['Fecha de nacimiento', $p->guardian_birth_date?->format('d/m/Y') ?? '—'],
            ['Genero', $p->guardian_gender ?? '—'],
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
