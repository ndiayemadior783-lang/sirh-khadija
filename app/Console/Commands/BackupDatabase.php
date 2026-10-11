<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Exporter une copie de sauvegarde de la base de donnees';

    public function handle()
    {
        $backupDir = storage_path('app/backups');
        File::makeDirectory($backupDir, 0755, true, true);

        $filename = 'backup_' . now()->format('Y-m-d_His') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        $db = config('database.default');
        $config = config("database.connections.{$db}");

        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $database = $config['database'];
        $username = $config['username'] ?? 'root';
        $password = $config['password'] ?? '';

        // Auto-detect mysqldump path in Laragon
        $mysqldumpPath = 'mysqldump';
        $mysqlDir = 'C:/laragon/bin/mysql/';
        if (is_dir($mysqlDir)) {
            foreach (scandir($mysqlDir) as $dir) {
                $candidate = $mysqlDir . $dir . '/bin/mysqldump.exe';
                if (is_file($candidate)) {
                    $mysqldumpPath = $candidate;
                    break;
                }
            }
        }

        $cmdParts = [
            '"' . $mysqldumpPath . '"',
            '--host=' . $host,
            '--port=' . $port,
            '--user=' . $username,
        ];

        if (!empty($password)) {
            $cmdParts[] = '--password=' . $password;
        }

        $cmdParts[] = '--no-tablespaces';
        $cmdParts[] = '"' . $database . '"';
        $cmdParts[] = '> "' . $filepath . '"';

        $command = implode(' ', $cmdParts);

        $output = [];
        $returnCode = 0;

        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            $this->error('Erreur lors de la sauvegarde : ' . implode("\n", $output));
            return 1;
        }

        $size = File::exists($filepath) ? round(File::size($filepath) / 1024, 2) : 0;

        $this->info("Sauvegarde creee avec succes !");
        $this->info("Fichier : {$filepath}");
        $this->info("Taille : {$size} Ko");

        return 0;
    }
}