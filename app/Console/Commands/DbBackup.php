<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DbBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera um backup do banco de dados e salva no storage local';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = "backup-" . Carbon::now()->format('Y-m-d_H-i-s') . ".sql";
        $path = storage_path('app/backups/' . $filename);

        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $dbHost = config('database.connections.mysql.host');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $this->info("Iniciando backup do banco de dados: {$dbName}...");

        // Comando mysqldump
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbHost),
            escapeshellarg($dbName),
            escapeshellarg($path)
        );

        $returnVar = null;
        $output = null;
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("Backup concluído com sucesso: {$filename}");
            \Log::info("Backup do banco de dados realizado com sucesso: {$filename}");
            
            // Aqui poderíamos enviar para S3 ou Google Drive no futuro
            // Storage::disk('s3')->put('backups/' . $filename, file_get_contents($path));
            
            // Limpar backups antigos (manter últimos 7 dias)
            $this->cleanOldBackups();
        } else {
            $this->error("Erro ao realizar o backup. Código: {$returnVar}");
            \Log::error("Erro ao realizar o backup do banco de dados. Código: {$returnVar}");
        }
    }

    protected function cleanOldBackups()
    {
        $files = glob(storage_path('app/backups/backup-*.sql'));
        $now = time();
        $max_age = 7 * 24 * 60 * 60; // 7 dias

        foreach ($files as $file) {
            if ($now - filemtime($file) >= $max_age) {
                unlink($file);
                $this->info("Backup antigo removido: " . basename($file));
            }
        }
    }
}
