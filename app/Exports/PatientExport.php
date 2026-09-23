<?php

namespace App\Exports;

use App\Models\Patient;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PatientExport
{
    protected Patient $patient;
    protected Spreadsheet $spreadsheet;

    protected array $statusMap = [
        'open'        => 'Abierta',
        'in_progress' => 'En Progreso',
        'closed'      => 'Cerrada',
    ];

    public function __construct(Patient $patient)
    {
        $this->patient = $patient->load([
            'medicalRecord',
            'user',
            'consultations.evolutionNotes',
        ]);
        $this->spreadsheet = new Spreadsheet();
    }

    public function download(string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->buildSheet1_DatosPersonales();
        $this->buildSheet2_Antecedentes();
        $this->buildSheet3_Consultas();
        $this->buildSheet4_Notas();

        return response()->streamDownload(function () {
            $writer = new Xlsx($this->spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ─── Hoja 1: Datos Personales + Expediente ───────────────────────────────

    protected function buildSheet1_DatosPersonales(): void
    {
        $sheet = $this->spreadsheet->getActiveSheet()->setTitle('Datos Personales');
        $p = $this->patient;
        $r = $p->medicalRecord;

        $rows = [
            ['EXPEDIENTE CLÍNICO — REHAVITÉ', ''],
            ['Generado el:', now()->format('d/m/Y H:i')],
            [],
            ['DATOS PERSONALES', ''],
            ['Nombre completo',         $p->full_name],
            ['Expediente',              $p->expediente_id],
            ['Fecha de nacimiento',     $p->birth_date?->format('d/m/Y') ?? '—'],
            ['Edad',                    ($p->age ?? '—') . ' años'],
            ['Género',                  ucfirst($p->gender ?? '—')],
            ['Teléfono',                $p->phone ?? '—'],
            ['Correo electrónico',      $p->email ?? '—'],
            ['Fisioterapeuta asignado', $p->user?->name ?? '—'],
            ['Estado',                  $p->status === 'active' ? 'Activo' : ($p->status === 'inactive' ? 'Inactivo' : 'Alta')],
            ['Diagnóstico inicial',     $p->diagnosis ?? '—'],
            ['Fecha de registro',       $p->created_at->format('d/m/Y')],
            [],
            ['EXPEDIENTE MÉDICO', ''],
            ['Ocupación',              $r?->occupation ?? '—'],
            ['Lateralidad',            $r?->laterality ?? '—'],
            ['Estado civil',           $r?->marital_status ?? '—'],
            ['Dirección',              $r?->address ?? '—'],
            ['Dependencia económica',  $r?->economic_dependency ?? '—'],
            ['Cuidador primario',      $r?->primary_caregiver ?? '—'],
            ['Tutor/Responsable',      $r?->guardian_name ?? '—'],
            ['Edad del tutor',         $r?->guardian_age ?? '—'],
            ['Género del tutor',       $r?->guardian_gender ?? '—'],
            ['Fecha nac. tutor',       $r?->guardian_birth_date?->format('d/m/Y') ?? '—'],
            ['Observaciones',          $r?->observations ?? '—'],
        ];

        $sheet->fromArray($rows, null, 'A1');
        $this->styleTitle($sheet, 'A1', 'B1', '0F3460');
        $this->styleSection($sheet, 'A4', 'B4');
        $this->styleSection($sheet, 'A17', 'B17');
        $sheet->getColumnDimension('A')->setWidth(28);
        $sheet->getColumnDimension('B')->setWidth(55);
    }

    // ─── Hoja 2: Antecedentes ────────────────────────────────────────────────

    protected function buildSheet2_Antecedentes(): void
    {
        $sheet = $this->spreadsheet->createSheet()->setTitle('Antecedentes');
        $r = $this->patient->medicalRecord;

        $row = 1;

        $groups = [
            'ANTECEDENTES NO PATOLÓGICOS'        => $r?->personal_non_pathological ?? [],
            'ANTECEDENTES FAMILIARES'            => $r?->family_history ?? [],
            'ANTECEDENTES PATOLÓGICOS'           => $r?->personal_pathological ?? [],
            'REVISIÓN POR SISTEMAS'              => $r?->systems_review ?? [],
        ];

        foreach ($groups as $title => $data) {
            $sheet->setCellValue("A{$row}", $title);
            $this->styleSection($sheet, "A{$row}", "B{$row}");
            $row++;

            if (empty($data)) {
                $sheet->setCellValue("A{$row}", 'Sin datos registrados');
                $row += 2;
                continue;
            }

            foreach ($data as $key => $val) {
                $sheet->setCellValue("A{$row}", ucfirst(str_replace('_', ' ', $key)));
                $sheet->setCellValue("B{$row}", $val ?: '—');
                $row++;
            }
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(32);
        $sheet->getColumnDimension('B')->setWidth(60);
        foreach (range(1, $row) as $r2) {
            $sheet->getStyle("B{$r2}")->getAlignment()->setWrapText(true);
        }
    }

    // ─── Hoja 3: Consultas ───────────────────────────────────────────────────

    protected function buildSheet3_Consultas(): void
    {
        $sheet = $this->spreadsheet->createSheet()->setTitle('Consultas');

        $headers = ['#', 'Fecha', 'Estado', 'Motivo', 'Estudios previos', 'Diagnóstico', 'Pronóstico',
                    'Objetivos de tratamiento',
                    'Recom. al alta', 'Motivo de alta'];
        $sheet->fromArray([$headers], null, 'A1');
        $this->styleTitle($sheet, 'A1', 'J1', '0F3460');

        $row = 2;
        foreach ($this->patient->consultations as $i => $c) {
            $sheet->fromArray([[
                $i + 1,
                $c->created_at->format('d/m/Y'),
                $this->statusMap[$c->status] ?? ucfirst($c->status),
                $c->reason ?? '—',
                $c->previous_studies_notes ?? '—',
                $c->diagnosis ?? '—',
                $c->prognosis ?? '—',
                $c->treatment_objectives ?? '—',
                $c->discharge_recommendations ?? '—',
                $c->discharge_reason ?? '—',
            ]], null, "A{$row}");

            foreach (['D','E','F','G','H','I','J'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getAlignment()->setWrapText(true);
            }
            $row++;
        }

        if ($row === 2) $sheet->setCellValue('A2', 'Sin consultas registradas');

        foreach (['A'=>5,'B'=>12,'C'=>13,'D'=>35,'E'=>30,'F'=>35,'G'=>25,'H'=>30,'I'=>25,'J'=>20] as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // Sub-tabla: Padecimiento Actual
        $row += 2;
        $sheet->setCellValue("A{$row}", 'PADECIMIENTO ACTUAL POR CONSULTA');
        $this->styleSection($sheet, "A{$row}", "J{$row}");
        $row++;

        $headers2 = ['Consulta #', 'Campo', 'Valor'];
        $sheet->fromArray([$headers2], null, "A{$row}");
        $this->styleTitle($sheet, "A{$row}", "C{$row}", '1ABC9C');
        $row++;

        foreach ($this->patient->consultations as $i => $c) {
            $cc = $c->current_condition ?? [];
            foreach ($cc as $key => $val) {
                if (empty($val)) continue;
                $sheet->setCellValue("A{$row}", 'Consulta ' . ($i + 1));
                $sheet->setCellValue("B{$row}", ucfirst(str_replace('_', ' ', $key)));
                $sheet->setCellValue("C{$row}", $val);
                $sheet->getStyle("C{$row}")->getAlignment()->setWrapText(true);
                $row++;
            }
        }

        // Sub-tabla: Exploración Física
        $row += 2;
        $sheet->setCellValue("A{$row}", 'EXPLORACIÓN FÍSICA POR CONSULTA');
        $this->styleSection($sheet, "A{$row}", "J{$row}");
        $row++;

        $sheet->fromArray([$headers2], null, "A{$row}");
        $this->styleTitle($sheet, "A{$row}", "C{$row}", '2980B9');
        $row++;

        foreach ($this->patient->consultations as $i => $c) {
            $pe = $c->physical_exam ?? [];
            foreach ($pe as $key => $val) {
                if (empty($val)) continue;
                $sheet->setCellValue("A{$row}", 'Consulta ' . ($i + 1));
                $sheet->setCellValue("B{$row}", ucfirst(str_replace('_', ' ', $key)));
                $sheet->setCellValue("C{$row}", $val);
                $row++;
            }
        }
    }

    // ─── Hoja 4: Notas de Evolución ──────────────────────────────────────────

    protected function buildSheet4_Notas(): void
    {
        $sheet = $this->spreadsheet->createSheet()->setTitle('Notas de Evolucion');

        $headers = ['Consulta #', 'Sesión #', 'Fecha', 'Descripción',
                    'Plan', 'Tratamiento', 'Notas adicionales', 'Comentarios',
                    'Temp inicio', 'Temp final', 'SatO2 inicio', 'SatO2 final',
                    'TA inicio', 'TA final', 'FC inicio', 'FC final'];
        $sheet->fromArray([$headers], null, 'A1');
        $this->styleTitle($sheet, 'A1', 'P1', '1ABC9C');

        $row = 2;
        foreach ($this->patient->consultations as $i => $c) {
            foreach ($c->evolutionNotes as $note) {
                $vs = $note->vital_signs ?? [];
                $sheet->fromArray([[
                    'Consulta ' . ($i + 1),
                    $note->session_number ?? '—',
                    $note->date ? $note->date->format('d/m/Y') : $note->created_at->format('d/m/Y'),
                    $note->description ?? '—',
                    $note->plan ?? '—',
                    $note->treatment ?? '—',
                    $note->additional_notes ?? '—',
                    $note->comments ?? '—',
                    $vs['temp_start'] ?? '—',
                    $vs['temp_end'] ?? '—',
                    $vs['saturation_start'] ?? '—',
                    $vs['saturation_end'] ?? '—',
                    $vs['bp_start'] ?? '—',
                    $vs['bp_end'] ?? '—',
                    $vs['hr_start'] ?? '—',
                    $vs['hr_end'] ?? '—',
                ]], null, "A{$row}");

                foreach (['D','E','F','G','H'] as $col) {
                    $sheet->getStyle("{$col}{$row}")->getAlignment()->setWrapText(true);
                }
                $row++;
            }
        }

        if ($row === 2) $sheet->setCellValue('A2', 'Sin notas de evolución registradas');

        foreach (['A'=>12,'B'=>10,'C'=>12,'D'=>40,'E'=>30,'F'=>30,'G'=>30,'H'=>35,
                  'I'=>12,'J'=>12,'K'=>12,'L'=>12,'M'=>12,'N'=>12,'O'=>12,'P'=>12] as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    protected function styleTitle($sheet, string $from, string $to, string $rgb): void
    {
        $sheet->getStyle("{$from}:{$to}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rgb]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }

    protected function styleSection($sheet, string $from, string $to): void
    {
        $sheet->getStyle("{$from}:{$to}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2471A3']],
        ]);
    }
}
