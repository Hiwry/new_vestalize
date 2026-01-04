<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageController extends Controller
{
    /**
     * Diretórios permitidos para servir arquivos (whitelist de segurança)
     */
    private const ALLOWED_DIRECTORIES = [
        'products',
        'orders/applications',
        'orders/sublimations',
        'orders/sublimations/images',
        'orders/items/applications',
        'orders/items/sublimations',
        'orders/items/covers',
        'orders/covers',
        'orders/items',
        'orders',
        'applications',
        'sublimations',
        'logos',
    ];

    /**
     * Extensões de arquivo permitidas (whitelist de segurança)
     */
    private const ALLOWED_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'ico'
    ];

    /**
     * Serve arquivos do storage com segurança reforçada
     */
    public function serve(Request $request, string $path): BinaryFileResponse|\Illuminate\Http\Response
    {
        // Limpar o caminho de tentativas de path traversal
        $originalPath = $path;
        $path = str_replace('..', '', $path);
        $path = ltrim($path, '/');
        
        // Log apenas em DEBUG (não expor PII em produção)
        \Log::debug('StorageController: serve request', ['path' => $path]);
        
        // Verificar extensão do arquivo (whitelist)
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            \Log::warning('StorageController: Extensão não permitida', ['extension' => $extension]);
            abort(403, 'Tipo de arquivo não permitido');
        }
        
        $storagePath = storage_path('app/public');
        $basename = basename($path);
        $foundPath = null;
        
        // Normalizar caminho
        $normalizedPath = ltrim(str_replace(['\\', '//'], '/', $path), '/');
        
        // Construir lista de caminhos permitidos baseada na whitelist
        $directPaths = [
            $storagePath . '/' . $normalizedPath,
            $storagePath . '/' . $path,
        ];
        
        // Adicionar apenas diretórios permitidos
        foreach (self::ALLOWED_DIRECTORIES as $dir) {
            $directPaths[] = $storagePath . '/' . $dir . '/' . $basename;
        }
        
        // Adicionar raiz como último fallback
        $directPaths[] = $storagePath . '/' . $basename;
        
        // Remover duplicatas
        $directPaths = array_unique($directPaths);
        
        foreach ($directPaths as $directPath) {
            if (file_exists($directPath) && is_readable($directPath)) {
                $relativePath = str_replace($storagePath . '/', '', $directPath);
                $foundPath = $relativePath;
                \Log::debug('StorageController: Arquivo encontrado', ['relative_path' => $foundPath]);
                break;
            }
        }
        
        if (!$foundPath) {
            \Log::debug('StorageController: Arquivo não encontrado', ['basename' => $basename]);
            abort(404, 'Arquivo não encontrado');
        }
        
        $path = $foundPath;
        $filePath = $storagePath . '/' . $path;
        
        // Verificação de path traversal
        $realFilePath = realpath($filePath);
        $realStoragePath = realpath($storagePath);
        
        if (!$realFilePath || !$realStoragePath || !str_starts_with($realFilePath, $realStoragePath)) {
            \Log::error('StorageController: Path traversal detectado');
            abort(403, 'Acesso negado');
        }
        
        if (!file_exists($realFilePath)) {
            abort(404, 'Arquivo não encontrado');
        }
        
        $filePath = $realFilePath;
        
        if (!is_readable($filePath)) {
            \Log::error('StorageController: Arquivo sem permissão de leitura', ['path' => $path]);
            abort(403, 'Acesso negado ao arquivo');
        }
        
        // Determinar tipo MIME
        $mimeType = File::mimeType($filePath);
        if (!$mimeType) {
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                'pdf' => 'application/pdf',
                'ico' => 'image/x-icon',
            ];
            $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        }
        
        // Retornar arquivo COM cabeçalhos de segurança
        try {
            $response = response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=2592000', // 30 dias
                'X-Content-Type-Options' => 'nosniff',
            ]);
            
            // MANTER cabeçalhos de segurança (não remover)
            // X-Frame-Options e CSP são mantidos pelo Laravel
            
            return $response;
        } catch (\Exception $e) {
            \Log::error('StorageController: Erro ao servir arquivo', ['error' => $e->getMessage()]);
            abort(500, 'Erro ao servir arquivo');
        }
    }

    /**
     * Serve imagens de aplicação com segurança reforçada
     */
    public function serveApplicationImage(Request $request, string $filename): BinaryFileResponse|\Illuminate\Http\Response
    {
        // Limpar filename de path traversal
        $filename = basename($filename);
        $filename = str_replace(['..', '/', '\\'], '', $filename);
        
        // Verificar extensão (whitelist)
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            \Log::warning('StorageController: Extensão não permitida em serveApplicationImage', ['extension' => $extension]);
            abort(403, 'Tipo de arquivo não permitido');
        }

        \Log::debug('StorageController: serveApplicationImage', ['filename' => $filename]);

        $storagePath = storage_path('app/public');
        
        // Lista de diretórios permitidos para imagens de aplicação
        $possiblePaths = [];
        foreach (self::ALLOWED_DIRECTORIES as $dir) {
            $possiblePaths[] = $storagePath . '/' . $dir . '/' . $filename;
        }
        $possiblePaths[] = $storagePath . '/' . $filename;

        $foundPath = null;
        foreach ($possiblePaths as $possiblePath) {
            if (file_exists($possiblePath) && is_readable($possiblePath)) {
                $foundPath = $possiblePath;
                \Log::debug('StorageController: Imagem encontrada', ['path' => $foundPath]);
                break;
            }
        }

        if (!$foundPath) {
            \Log::debug('StorageController: Imagem não encontrada', ['filename' => $filename]);
            abort(404, 'Imagem não encontrada');
        }

        // Verificar path traversal
        $realFilePath = realpath($foundPath);
        $realStoragePath = realpath($storagePath);
        
        if (!$realFilePath || !$realStoragePath || !str_starts_with($realFilePath, $realStoragePath)) {
            \Log::error('StorageController: Path traversal detectado em serveApplicationImage');
            abort(403, 'Acesso negado');
        }

        // Determinar MIME type
        $mimeType = File::mimeType($realFilePath);
        if (!$mimeType) {
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
            ];
            $mimeType = $mimeTypes[$extension] ?? 'image/png';
        }

        // Retornar arquivo COM cabeçalhos de segurança
        try {
            $response = response()->file($realFilePath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=2592000', // 30 dias
                'X-Content-Type-Options' => 'nosniff',
            ]);
            
            // MANTER cabeçalhos de segurança
            return $response;
        } catch (\Exception $e) {
            \Log::error('StorageController: Erro ao servir imagem', ['error' => $e->getMessage()]);
            abort(500, 'Erro ao servir imagem');
        }
    }
}

