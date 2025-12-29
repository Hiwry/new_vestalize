<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageController extends Controller
{
    /**
     * Serve arquivos do storage quando o symlink não existe
     */
    public function serve(Request $request, string $path): BinaryFileResponse|\Illuminate\Http\Response
    {
        // Log imediato para verificar se o método está sendo chamado
        \Log::info('StorageController: Método serve chamado', [
            'request_path' => $request->path(),
            'original_path' => $path,
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip()
        ]);
        
        // Limpar o caminho de possíveis tentativas de path traversal
        $originalPath = $path;
        $path = str_replace('..', '', $path);
        $path = ltrim($path, '/');
        
        // Log sempre (não apenas debug) para identificar problemas
        \Log::info('StorageController: Tentando servir arquivo', [
            'request_path' => $request->path(),
            'original_path_param' => $originalPath,
            'cleaned_path' => $path,
            'full_url' => $request->fullUrl(),
            'request_method' => $request->method(),
            'ip' => $request->ip()
        ]);
        
        // Verificar se o arquivo existe no storage público
        // Servir diretamente do storage/app/public SEM depender de symlink
        $disk = Storage::disk('public');
        $storagePath = storage_path('app/public');
        $basename = basename($path);
        $foundPath = null;
        
        // Normalizar caminho para verificação
        $normalizedPath = ltrim(str_replace(['\\', '//'], '/', $path), '/');
        
        // Primeiro, verificar diretamente no sistema de arquivos (mais confiável que disk->exists)
        // Priorizar orders/applications/ pois é onde as imagens de aplicação são salvas
        $directPaths = [
            $storagePath . '/' . $normalizedPath, // Caminho normalizado (prioridade 1)
            $storagePath . '/' . $path, // Caminho original (prioridade 2)
            $storagePath . '/products/' . $basename, // Imagens de produtos
            $storagePath . '/orders/applications/' . $basename,
            $storagePath . '/orders/sublimations/' . $basename,
            $storagePath . '/orders/items/applications/' . $basename,
            $storagePath . '/orders/items/sublimations/' . $basename,
            $storagePath . '/applications/' . $basename,
            $storagePath . '/sublimations/' . $basename,
            $storagePath . '/' . $basename,
        ];
        
        // Remover duplicatas mantendo ordem
        $directPaths = array_unique($directPaths);
        
        foreach ($directPaths as $directPath) {
            if (file_exists($directPath) && is_readable($directPath)) {
                // Encontrar caminho relativo a partir do storage/app/public
                $relativePath = str_replace($storagePath . '/', '', $directPath);
                $foundPath = $relativePath;
                \Log::info('StorageController: Arquivo encontrado via verificação direta do sistema de arquivos', [
                    'original_request_path' => $request->path(),
                    'found_relative_path' => $foundPath,
                    'full_file_path' => $directPath,
                    'file_size' => filesize($directPath),
                    'is_readable' => is_readable($directPath)
                ]);
                break;
            }
        }
        
        // Se não encontrou, tentar via Storage facade também
        if (!$foundPath) {
            // Tentar buscar em diferentes locais possíveis usando Storage facade
            $possiblePaths = [
                $path, // Caminho original como veio na requisição
                $normalizedPath, // Caminho normalizado
                'products/' . $basename, // Imagens de produtos
                'orders/applications/' . $basename,
                'orders/sublimations/' . $basename,
                'orders/items/applications/' . $basename,
                'orders/items/sublimations/' . $basename,
                'orders/items/covers/' . $basename,
                'orders/covers/' . $basename,
                'orders/items/' . $basename,
                'orders/' . $basename,
                'applications/' . $basename, // Sem prefixo orders
                'sublimations/' . $basename,
                $basename, // Apenas o nome do arquivo
            ];
            
            foreach ($possiblePaths as $possiblePath) {
                // Normalizar caminho para evitar problemas com barras
                $normalizedPossiblePath = ltrim(str_replace(['\\', '//'], '/', $possiblePath), '/');
                
                // Verificar via Storage facade
                if ($disk->exists($normalizedPossiblePath)) {
                    $foundPath = $normalizedPossiblePath;
                    \Log::info('StorageController: Arquivo encontrado via Storage facade', [
                        'original_request_path' => $request->path(),
                        'found_relative_path' => $foundPath,
                        'full_path' => $disk->path($foundPath)
                    ]);
                    break;
                }
            }
        }
        
        // Se ainda não encontrou, retornar 404
        if (!$foundPath) {
            \Log::warning('StorageController: Arquivo não encontrado em nenhum local', [
                'original_path' => $path,
                'basename' => $basename,
                'storage_path' => $storagePath,
                'tried_direct_paths' => $directPaths
            ]);
            abort(404, 'Arquivo não encontrado: ' . $basename);
        }
        
        // Usar o caminho encontrado
        $path = $foundPath;
        
        // Obter o caminho completo do arquivo (sempre do storage/app/public)
        $filePath = $storagePath . '/' . $path;
        
        // Garantir que o caminho está dentro do storage/app/public (segurança)
        $realFilePath = realpath($filePath);
        $realStoragePath = realpath($storagePath);
        
        if (!$realFilePath || !$realStoragePath || !str_starts_with($realFilePath, $realStoragePath)) {
            \Log::error('StorageController: Tentativa de path traversal detectada', [
                'requested_path' => $path,
                'resolved_path' => $realFilePath,
                'storage_path' => $realStoragePath
            ]);
            abort(403, 'Acesso negado');
        }
        
        // Verificar se o arquivo realmente existe
        if (!file_exists($realFilePath)) {
            \Log::warning('StorageController: Arquivo não existe no sistema de arquivos', [
                'path' => $path,
                'filePath' => $realFilePath
            ]);
            abort(404, 'Arquivo não encontrado');
        }
        
        $filePath = $realFilePath;
        
        // Verificar permissões de leitura
        if (!is_readable($filePath)) {
            \Log::error('StorageController: Arquivo não tem permissão de leitura', [
                'path' => $path,
                'filePath' => $filePath,
                'permissions' => substr(sprintf('%o', fileperms($filePath)), -4)
            ]);
            abort(403, 'Acesso negado ao arquivo');
        }
        
        // Determinar o tipo MIME
        $mimeType = File::mimeType($filePath);
        if (!$mimeType) {
            // Fallback para tipos comuns
            $extension = strtolower(File::extension($filePath));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'pdf' => 'application/pdf',
                'zip' => 'application/zip',
            ];
            $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        }
        
        // Retornar o arquivo com headers apropriados
        try {
            $response = response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=31536000', // Cache por 1 ano
            ]);
            
            // Remover headers que podem causar problemas
            $response->headers->remove('X-Frame-Options');
            $response->headers->remove('Content-Security-Policy');
            $response->headers->remove('X-Content-Type-Options');
            
            \Log::info('StorageController: Arquivo servido com sucesso', [
                'request_path' => $request->path(),
                'found_path' => $path,
                'file_path' => $filePath,
                'file_size' => filesize($filePath),
                'mimeType' => $mimeType
            ]);
            
            return $response;
        } catch (\Exception $e) {
            \Log::error('StorageController: Erro ao servir arquivo', [
                'path' => $path,
                'filePath' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Erro ao servir arquivo: ' . $e->getMessage());
        }
    }

    /**
     * Serve especificamente imagens de aplicação usando apenas o filename
     * Esta rota é mais confiável pois não depende de .htaccess ou caminhos complexos
     */
    public function serveApplicationImage(Request $request, string $filename): BinaryFileResponse|\Illuminate\Http\Response
    {
        \Log::info('StorageController: serveApplicationImage chamado', [
            'filename' => $filename,
            'full_url' => $request->fullUrl(),
        ]);

        // Limpar o filename de possíveis tentativas de path traversal
        $filename = basename($filename); // Garantir que é apenas o nome do arquivo
        $filename = str_replace(['..', '/', '\\'], '', $filename); // Remover caracteres perigosos

        $storagePath = storage_path('app/public');
        
        // Lista de locais onde a imagem pode estar
        $possiblePaths = [
            $storagePath . '/orders/applications/' . $filename, // PRIORIDADE 1
            $storagePath . '/orders/sublimations/' . $filename,
            $storagePath . '/orders/sublimations/images/' . $filename, // NOVO: Imagens de sublimação
            $storagePath . '/orders/items/applications/' . $filename,
            $storagePath . '/orders/items/sublimations/' . $filename,
            $storagePath . '/applications/' . $filename,
            $storagePath . '/sublimations/' . $filename,
            $storagePath . '/' . $filename,
        ];

        $foundPath = null;
        foreach ($possiblePaths as $possiblePath) {
            if (file_exists($possiblePath) && is_readable($possiblePath)) {
                $foundPath = $possiblePath;
                \Log::info('StorageController: Imagem de aplicação encontrada', [
                    'filename' => $filename,
                    'found_path' => $foundPath,
                    'file_size' => filesize($foundPath)
                ]);
                break;
            }
        }

        if (!$foundPath) {
            \Log::warning('StorageController: Imagem de aplicação não encontrada', [
                'filename' => $filename,
                'tried_paths' => $possiblePaths
            ]);
            abort(404, 'Imagem de aplicação não encontrada: ' . $filename);
        }

        // Verificar segurança (path traversal)
        $realFilePath = realpath($foundPath);
        $realStoragePath = realpath($storagePath);
        
        if (!$realFilePath || !$realStoragePath || !str_starts_with($realFilePath, $realStoragePath)) {
            \Log::error('StorageController: Tentativa de path traversal detectada em serveApplicationImage');
            abort(403, 'Acesso negado');
        }

        // Determinar o tipo MIME
        $mimeType = File::mimeType($realFilePath);
        if (!$mimeType) {
            $extension = strtolower(File::extension($realFilePath));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            ];
            $mimeType = $mimeTypes[$extension] ?? 'image/png';
        }

        // Retornar o arquivo
        try {
            $response = response()->file($realFilePath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=31536000',
            ]);
            
            // Remover headers que podem causar problemas
            $response->headers->remove('X-Frame-Options');
            $response->headers->remove('Content-Security-Policy');
            $response->headers->remove('X-Content-Type-Options');
            
            return $response;
        } catch (\Exception $e) {
            \Log::error('StorageController: Erro ao servir imagem de aplicação', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Erro ao servir imagem: ' . $e->getMessage());
        }
    }
}

