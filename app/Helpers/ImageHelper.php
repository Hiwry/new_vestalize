<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Resolve a public URL for a cover image stored on the public disk.
     * Agora verifica primeiro em public/images (novo local) e depois em storage/app/public (compatibilidade).
     */
    public static function resolveCoverImageUrl(?string $path): ?string
    {
        if (!$path) {
            \Log::debug('ImageHelper: Path vazio');
            return null;
        }
        
        $normalizedPath = self::normalizePath($path);

        // Remover prefixo "images/" caso venha no caminho salvo
        if ($normalizedPath && Str::startsWith($normalizedPath, 'images/')) {
            $normalizedPath = Str::after($normalizedPath, 'images/');
        }

        $basename = $normalizedPath ? basename($normalizedPath) : basename($path);

        // Imagens de aplicação podem estar bloqueadas em /images; prefira servir via storage/rota
        if ($normalizedPath && (Str::startsWith($normalizedPath, [
            'orders/applications',
            'orders/sublimations',
            'orders/items/applications',
            'orders/items/sublimations',
        ]) || Str::contains($normalizedPath, [
            'orders/applications',
            'orders/sublimations',
            'applications/',
            'sublimations/',
        ]))) {
            $relativePath = self::resolveRelativePath($normalizedPath, [
                'orders/applications',
                'orders/sublimations',
                'orders/items/applications',
                'orders/items/sublimations',
                'applications',
                'sublimations',
            ]);

            if ($relativePath) {
                return url('/storage/' . $relativePath);
            }

            if ($basename) {
                return url('/imagens-aplicacao/' . rawurlencode($basename));
            }
        }
        
        // Primeiro, verificar se a imagem está em public/images (novo local, sem symlink)
        $publicImagesPath = public_path('images/' . $normalizedPath);
        if (file_exists($publicImagesPath)) {
            $url = asset('images/' . $normalizedPath);
            \Log::debug('ImageHelper: Imagem encontrada em public/images', [
                'path' => $normalizedPath,
                'url' => $url
            ]);
            return $url;
        }
        
        // Se não encontrou em public/images, tentar em storage/app/public (compatibilidade com imagens antigas)
        $relativePath = self::resolveRelativePath($path, [
            'orders/covers',
            'orders/items/covers',
            'orders/items',
            'orders',
        ]);

        if (!$relativePath) {
            \Log::warning('ImageHelper: Não foi possível resolver o caminho da imagem', [
                'original_path' => $path
            ]);
            return null;
        }
        
        // Verificar se o symlink existe e está funcionando
        $linkPath = public_path('storage');
        $useSymlink = false;
        
        if (file_exists($linkPath)) {
            if (is_link($linkPath)) {
                // É um symlink, verificar se aponta para o lugar correto
                try {
                    $linkTarget = readlink($linkPath);
                    if ($linkTarget) {
                        // Resolver caminhos absolutos
                        $linkTargetPath = realpath(dirname($linkPath) . DIRECTORY_SEPARATOR . $linkTarget);
                        $expectedPath = realpath(storage_path('app/public'));
                        
                        if ($linkTargetPath && $expectedPath && $linkTargetPath === $expectedPath) {
                            $useSymlink = true;
                        }
                    }
                } catch (\Exception $e) {
                    // Erro ao ler symlink, usar rota
                    \Log::warning('ImageHelper: Erro ao verificar symlink', ['error' => $e->getMessage()]);
                }
            } elseif (is_dir($linkPath)) {
                // É um diretório, verificar se é o diretório correto (pode ser uma cópia)
                $expectedPath = realpath(storage_path('app/public'));
                $actualPath = realpath($linkPath);
                
                if ($actualPath && $expectedPath && $actualPath === $expectedPath) {
                    $useSymlink = true;
                }
            }
        }
        
        if ($useSymlink) {
            // Symlink válido, usar asset()
            $url = asset('storage/' . $relativePath);
        } else {
            // Sem symlink válido, usar URL direta que será capturada pela rota
            // Usar url() diretamente para evitar problemas com route() não disponível
            $url = url('/storage/' . $relativePath);
            
            \Log::info('ImageHelper: Usando URL de storage (symlink não encontrado ou inválido)', [
                'relative_path' => $relativePath,
                'url' => $url
            ]);
        }
        
        \Log::debug('ImageHelper: URL gerada', [
            'original_path' => $path,
            'resolved_path' => $relativePath,
            'url' => $url,
            'using_symlink' => $useSymlink
        ]);

        return $url;
    }

    /**
     * Verifica se o symlink de storage existe e tenta criá-lo se não existir
     */
    public static function ensureStorageLink(): bool
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        // Verificar se o symlink já existe e está correto
        if (file_exists($link)) {
            if (is_link($link)) {
                $linkTarget = readlink($link);
                $normalizedLinkTarget = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, realpath($linkTarget) ?: $linkTarget);
                $normalizedTarget = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, realpath($target) ?: $target);
                
                if ($normalizedLinkTarget === $normalizedTarget) {
                    return true;
                }
            } else {
                // Existe mas não é um symlink
                return false;
            }
        }

        // Tentar criar o symlink
        try {
            if (!File::exists($target)) {
                File::makeDirectory($target, 0755, true);
            }

            if (PHP_OS_FAMILY === 'Windows') {
                if (function_exists('symlink')) {
                    if (file_exists($link)) {
                        @unlink($link);
                    }
                    return symlink($target, $link);
                } else {
                    // Fallback para junction no Windows
                    $target = str_replace('/', '\\', $target);
                    $link = str_replace('/', '\\', $link);
                    exec("mklink /J \"$link\" \"$target\"", $output, $return);
                    return $return === 0;
                }
            } else {
                if (file_exists($link)) {
                    @unlink($link);
                }
                return symlink($target, $link);
            }
        } catch (\Exception $e) {
            \Log::error('ImageHelper: Erro ao criar symlink', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Normalize storage path and try common fallback locations.
     */
    public static function resolveRelativePath(?string $path, array $fallbackDirectories = []): ?string
    {
        if (!$path) {
            return null;
        }

        $normalized = self::normalizePath($path);
        $disk = Storage::disk('public');

        if ($normalized && $disk->exists($normalized)) {
            return $normalized;
        }

        $basename = $normalized ? basename($normalized) : basename($path);
        $directories = array_unique(array_filter($fallbackDirectories));

        foreach ($directories as $directory) {
            $directory = trim($directory, '/');

            if ($normalized) {
                $candidate = $directory . '/' . ltrim($normalized, '/');
                if ($disk->exists($candidate)) {
                    return $candidate;
                }
            }

            if ($basename) {
                $candidate = $directory . '/' . $basename;
                if ($disk->exists($candidate)) {
                    return $candidate;
                }

                $similar = self::findByPrefix($directory, $basename);
                if ($similar) {
                    return $similar;
                }
            }
        }

        if ($basename && $disk->exists($basename)) {
            return $basename;
        }

        return null;
    }

    /**
     * Normalize path separators and trim leading slashes.
     */
    public static function normalizePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#/+#', '/', $path);

        return ltrim($path, '/');
    }

    /**
     * Attempt to find a file in the given directory with matching prefix.
     */
    private static function findByPrefix(string $directory, string $basename): ?string
    {
        $disk = Storage::disk('public');
        $prefix = Str::beforeLast($basename, '.');
        $directoryPath = storage_path('app/public/' . trim($directory, '/'));

        if (!is_dir($directoryPath)) {
            return null;
        }

        $files = $disk->files($directory);

        foreach ($files as $file) {
            if (Str::startsWith(basename($file), $prefix)) {
                return $file;
            }
        }

        return null;
    }
}

