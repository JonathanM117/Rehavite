<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Perform an encrypted, compressed database backup';

    public function handle()
    {
        $this->info('Starting database backup...');
        try {
            $fileName = BackupService::createBackup();
            $this->info("Backup successfully created: {$fileName}");
        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
        }
    }
}
