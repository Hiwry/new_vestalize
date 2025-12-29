<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class StorageLinkServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->ensureStorageLinkExists();
    }

    /**
     * Verifica e cria o symlink de storage se não existir
     */
    protected function ensureStorageLinkExists(): void
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        // Verificar se o symlink já existe
        if (file_exists($link)) {
            // Verificar se é um symlink válido
            if (is_link($link)) {
                $linkTarget = readlink($link);
                // Normalizar caminhos para comparação
                $normalizedLinkTarget = $this->normalizePath($linkTarget);
                $normalizedTarget = $this->normalizePath($target);
                
                if ($normalizedLinkTarget === $normalizedTarget) {
                    // Symlink já existe e está correto
                    return;
                } else {
                    // Symlink existe mas aponta para lugar errado
                    Log::warning('Symlink de storage existe mas aponta para local incorreto', [
                        'link' => $link,
                        'current_target' => $linkTarget,
                        'expected_target' => $target
                    ]);
                }
            } else {
                // Existe mas não é um symlink (pode ser um diretório)
                Log::warning('public/storage existe mas não é um symlink', [
                    'link' => $link
                ]);
                
                // Em ambiente local, remover e recriar como symlink
                if ($this->app->environment('local')) {
                    try {
                        if (is_dir($link)) {
                            File::deleteDirectory($link);
                            Log::info('Diretório public/storage removido para recriar como symlink');
                        } else {
                            @unlink($link);
                        }
                        // Continuar para criar o symlink abaixo
                    } catch (\Exception $e) {
                        Log::error('Erro ao remover public/storage para recriar symlink', [
                            'error' => $e->getMessage()
                        ]);
                        return;
                    }
                } else {
                    return;
                }
            }
        }

        // Verificar se o diretório target existe
        if (!File::exists($target)) {
            File::makeDirectory($target, 0755, true);
            Log::info('Diretório storage/app/public criado');
        }

        // Tentar criar o symlink
        try {
            // Remover link/diretório existente se necessário (apenas em desenvolvimento)
            if (file_exists($link) && !is_link($link) && $this->app->environment('local')) {
                // Em produção, não remover automaticamente
                Log::warning('public/storage existe como diretório, não será removido automaticamente em produção');
                return;
            }

            // Criar o symlink
            if (PHP_OS_FAMILY === 'Windows') {
                // Windows: usar junction ou symlink
                if (function_exists('symlink')) {
                    // Tentar criar symlink
                    if (file_exists($link)) {
                        @unlink($link);
                    }
                    symlink($target, $link);
                } else {
                    // Fallback: criar junction (requer privilégios de administrador)
                    $target = str_replace('/', '\\', $target);
                    $link = str_replace('/', '\\', $link);
                    exec("mklink /J \"$link\" \"$target\"", $output, $return);
                    if ($return !== 0) {
                        throw new \Exception('Falha ao criar junction no Windows. Execute como administrador ou use: php artisan storage:link');
                    }
                }
            } else {
                // Linux/Unix: criar symlink normal
                if (file_exists($link)) {
                    @unlink($link);
                }
                symlink($target, $link);
            }

            Log::info('Symlink de storage criado com sucesso', [
                'link' => $link,
                'target' => $target
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao criar symlink de storage', [
                'link' => $link,
                'target' => $target,
                'error' => $e->getMessage(),
                'suggestion' => 'Execute manualmente: php artisan storage:link'
            ]);
        }
    }

    /**
     * Normaliza caminhos para comparação (resolve caminhos relativos)
     */
    protected function normalizePath(string $path): string
    {
        $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
        $path = realpath($path) ?: $path;
        return str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
    }
}

