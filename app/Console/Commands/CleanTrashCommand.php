<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Patient;
use Carbon\Carbon;

class CleanTrashCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patients:clean-trash {--days=15 : Número de días antes de eliminación permanente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina permanentemente los pacientes que llevan más de X días en la papelera';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Prefer DB setting, fall back to --days option, then default 15
        $days = \App\Models\SystemSetting::get('trash_days', $this->option('days') ?? 15);
        $thresholdDate = Carbon::now()->subDays((int)$days);

        $patientsToDelete = Patient::onlyTrashed()
                                   ->where('deleted_at', '<=', $thresholdDate)
                                   ->get();

        if ($patientsToDelete->isEmpty()) {
            $this->info("No hay pacientes con más de {$days} días en la papelera.");
            return;
        }

        $count = $patientsToDelete->count();
        foreach ($patientsToDelete as $patient) {
            // Force delete permanently removes it and any cascade constraints
            $patient->forceDelete();
        }

        $this->info("¡Limpieza completada! {$count} paciente(s) eliminados permanentemente.");
        \Illuminate\Support\Facades\Log::info("CleanTrashCommand: {$count} paciente(s) eliminados permanentemente (antigüedad > {$days} días).");
    }
}
