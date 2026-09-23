<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupService
{
    protected static $encryptionCipher = 'aes-256-cbc';

    public static function getBackupDirectory(): string
    {
        $path = storage_path('app/backups');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        return $path;
    }

    public static function listBackups(): array
    {
        $dir = self::getBackupDirectory();
        $files = glob($dir . '/*.enc');
        $backups = [];
        
        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => filesize($file),
                'created_at' => filemtime($file),
            ];
        }

        usort($backups, fn($a, $b) => $b['created_at'] - $a['created_at']);

        return $backups;
    }

    public static function createBackup(): string
    {
        $dir = self::getBackupDirectory();
        $driver = DB::connection()->getDriverName();
        
        $sqlDump = "";
        
        if ($driver === 'mysql') {
            $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
            $tables = array_map('current', DB::select('SHOW TABLES'));
            
            foreach ($tables as $table) {
                $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
                $createTableKey = 'Create Table';
                if (isset($createTable[0]->$createTableKey)) {
                    $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n";
                    $sqlDump .= $createTable[0]->$createTableKey . ";\n\n";
                }
                
                $rows = DB::table($table)->get();
                foreach ($rows as $row) {
                    $rowArr = (array)$row;
                    $columns = array_keys($rowArr);
                    $escapedColumns = array_map(fn($col) => "`{$col}`", $columns);
                    $escapedValues = array_map(function($val) {
                        if (is_null($val)) return 'NULL';
                        return DB::getPdo()->quote($val);
                    }, array_values($rowArr));

                    $sqlDump .= "INSERT INTO `{$table}` (" . implode(', ', $escapedColumns) . ") VALUES (" . implode(', ', $escapedValues) . ");\n";
                }
                $sqlDump .= "\n";
            }
            $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";
        } else {
            // Fallback for sqlite / generic drivers
            $tables = array_map('current', DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'"));
            foreach ($tables as $table) {
                $createTable = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='{$table}'");
                if (isset($createTable[0]->sql)) {
                    $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n";
                    $sqlDump .= $createTable[0]->sql . ";\n\n";
                }
                
                $rows = DB::table($table)->get();
                foreach ($rows as $row) {
                    $rowArr = (array)$row;
                    $columns = array_keys($rowArr);
                    $escapedColumns = array_map(fn($col) => "`{$col}`", $columns);
                    $escapedValues = array_map(function($val) {
                        if (is_null($val)) return 'NULL';
                        return DB::getPdo()->quote($val);
                    }, array_values($rowArr));

                    $sqlDump .= "INSERT INTO `{$table}` (" . implode(', ', $escapedColumns) . ") VALUES (" . implode(', ', $escapedValues) . ");\n";
                }
                $sqlDump .= "\n";
            }
        }

        // Compress
        $compressedData = gzencode($sqlDump, 9);

        // Encrypt
        $key = substr(hash('sha256', config('app.key')), 0, 32);
        $iv = substr(hash('sha256', 'rehavite_iv'), 0, 16);
        $encryptedData = openssl_encrypt($compressedData, self::$encryptionCipher, $key, OPENSSL_RAW_DATA, $iv);

        $fileName = 'backup_' . date('Y-m-d_H-i-s') . '.sql.gz.enc';
        $filePath = $dir . '/' . $fileName;
        
        file_put_contents($filePath, $encryptedData);

        return $fileName;
    }

    public static function restoreBackup(string $fileName): bool
    {
        $dir = self::getBackupDirectory();
        $filePath = $dir . '/' . $fileName;

        if (!file_exists($filePath)) {
            return false;
        }

        $encryptedData = file_get_contents($filePath);

        $key = substr(hash('sha256', config('app.key')), 0, 32);
        $iv = substr(hash('sha256', 'rehavite_iv'), 0, 16);
        $compressedData = openssl_decrypt($encryptedData, self::$encryptionCipher, $key, OPENSSL_RAW_DATA, $iv);

        if ($compressedData === false) {
            throw new \Exception("Fallo al desencriptar el respaldo. La clave del sistema (APP_KEY) puede haber cambiado.");
        }

        $sqlDump = @gzdecode($compressedData);
        if ($sqlDump === false) {
            throw new \Exception("Fallo al descomprimir el respaldo. Datos corruptos.");
        }

        DB::transaction(function() use ($sqlDump) {
            DB::unprepared($sqlDump);
        });

        return true;
    }

    public static function deleteBackup(string $fileName): bool
    {
        $dir = self::getBackupDirectory();
        $filePath = $dir . '/' . $fileName;

        if (file_exists($filePath)) {
            unlink($filePath);
            return true;
        }

        return false;
    }
}
