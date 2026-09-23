<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\BackupService;

class BackupManager extends Component
{
    public $backups = [];

    public function mount()
    {
        $this->loadBackups();
    }

    public function loadBackups()
    {
        $this->backups = BackupService::listBackups();
    }

    public function runBackup()
    {
        try {
            $fileName = BackupService::createBackup();
            session()->flash('success', "Respaldo '$fileName' creado exitosamente.");
            $this->loadBackups();
        } catch (\Exception $e) {
            session()->flash('error', "Error al crear respaldo: " . $e->getMessage());
        }
    }

    public function restore($fileName)
    {
        try {
            BackupService::restoreBackup($fileName);
            session()->flash('success', "Respaldo '$fileName' restaurado exitosamente.");
            $this->loadBackups();
        } catch (\Exception $e) {
            session()->flash('error', "Error al restaurar respaldo: " . $e->getMessage());
        }
    }

    public function delete($fileName)
    {
        try {
            BackupService::deleteBackup($fileName);
            session()->flash('success', "Respaldo '$fileName' eliminado.");
            $this->loadBackups();
        } catch (\Exception $e) {
            session()->flash('error', "Error al eliminar respaldo: " . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.backup-manager');
    }
}
