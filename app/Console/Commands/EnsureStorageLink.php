<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class EnsureStorageLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:ensure-link 
                            {--force : Força a recriação do symlink mesmo se já existir}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica e cria o symlink de storage se não existir';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        $this->info('Verificando symlink de storage...');
        $this->line("Link: {$link}");
        $this->line("Target: {$target}");

        // Verificar se o symlink já existe
        if (file_exists($link) && !$this->option('force')) {
            if (is_link($link)) {
                $linkTarget = readlink($link);
                $normalizedLinkTarget = $this->normalizePath($linkTarget);
                $normalizedTarget = $this->normalizePath($target);
                
                if ($normalizedLinkTarget === $normalizedTarget) {
                    $this->info(' Symlink já existe e está correto.');
                    return Command::SUCCESS;
                } else {
                    $this->warn(' Symlink existe mas aponta para local incorreto.');
                    $this->line("  Atual: {$linkTarget}");
                    $this->line("  Esperado: {$target}");
                    
                    if (!$this->confirm('Deseja recriar o symlink?', true)) {
                        return Command::FAILURE;
                    }
                }
            } else {
                $this->warn(' public/storage existe mas não é um symlink (pode ser um diretório).');
                
                if (!$this->confirm('Deseja remover e criar um symlink?', true)) {
                    return Command::FAILURE;
                }
            }
        }

        // Verificar se o diretório target existe
        if (!File::exists($target)) {
            $this->info('Criando diretório storage/app/public...');
            File::makeDirectory($target, 0755, true);
            $this->info(' Diretório criado.');
        }

        // Remover link/diretório existente se necessário
        if (file_exists($link)) {
            if (is_link($link)) {
                $this->info('Removendo symlink existente...');
                @unlink($link);
            } elseif (is_dir($link)) {
                $this->warn(' public/storage é um diretório. Removendo...');
                try {
                    File::deleteDirectory($link);
                } catch (\Exception $e) {
                    $this->error(" Erro ao remover diretório: {$e->getMessage()}");
                    $this->warn('  Você pode precisar remover manualmente o diretório public/storage');
                    return Command::FAILURE;
                }
            } else {
                $this->info('Removendo arquivo existente...');
                @unlink($link);
            }
        }

        // Criar o symlink
        try {
            $this->info('Criando symlink...');
            
            if (PHP_OS_FAMILY === 'Windows') {
                // Windows: usar symlink ou junction
                if (function_exists('symlink')) {
                    $result = symlink($target, $link);
                    if ($result) {
                        $this->info(' Symlink criado com sucesso!');
                        return Command::SUCCESS;
                    } else {
                        $this->error(' Falha ao criar symlink. Verifique as permissões.');
                        $this->warn('  No Windows, você pode precisar executar como administrador.');
                        $this->warn('  Ou usar: mklink /J "' . str_replace('/', '\\', $link) . '" "' . str_replace('/', '\\', $target) . '"');
                        return Command::FAILURE;
                    }
                } else {
                    // Fallback: criar junction (requer privilégios de administrador)
                    $targetWin = str_replace('/', '\\', $target);
                    $linkWin = str_replace('/', '\\', $link);
                    
                    $this->warn('  Função symlink() não disponível. Tentando criar junction...');
                    $this->warn('  Isso requer privilégios de administrador no Windows.');
                    
                    exec("mklink /J \"$linkWin\" \"$targetWin\"", $output, $return);
                    
                    if ($return === 0) {
                        $this->info(' Junction criada com sucesso!');
                        return Command::SUCCESS;
                    } else {
                        $this->error(' Falha ao criar junction.');
                        $this->warn('  Execute o PowerShell como administrador e execute:');
                        $this->line("  mklink /J \"$linkWin\" \"$targetWin\"");
                        return Command::FAILURE;
                    }
                }
            } else {
                // Linux/Unix: criar symlink normal
                $result = symlink($target, $link);
                if ($result) {
                    $this->info(' Symlink criado com sucesso!');
                    return Command::SUCCESS;
                } else {
                    $this->error(' Falha ao criar symlink. Verifique as permissões.');
                    return Command::FAILURE;
                }
            }
        } catch (\Exception $e) {
            $this->error(" Erro ao criar symlink: {$e->getMessage()}");
            Log::error('Erro ao criar symlink de storage', [
                'link' => $link,
                'target' => $target,
                'error' => $e->getMessage()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Normaliza caminhos para comparação
     */
    protected function normalizePath(string $path): string
    {
        $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
        $realPath = realpath($path);
        return $realPath ? str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $realPath) : $path;
    }
}

