<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Exceptions\DriverException;

class ImageProcessor
{
    /**
     * Manager pode ser nulo se o driver de imagem não puder ser inicializado
     * (ex.: falta de extensão GD/Imagick). Nesse caso o processamento de
     * imagens é simplesmente ignorado.
     */
    private ?ImageManager $manager = null;

    public function __construct(?ImageManager $manager = null)
    {
        if ($manager) {
            $this->manager = $manager;
            return;
        }

        try {
            // Tenta usar Imagick se disponível, senão GD
            $this->manager = extension_loaded('imagick')
                ? ImageManager::imagick()
                : ImageManager::gd();
        } catch (DriverException $e) {
            // Se o driver falhar (ex.: GD não disponível), registra e continua
            \Log::error('ImageProcessor: falha ao inicializar driver de imagem', [
                'error' => $e->getMessage(),
            ]);

            $this->manager = null;
        } catch (\Throwable $e) {
            \Log::error('ImageProcessor: erro inesperado ao inicializar driver de imagem', [
                'error' => $e->getMessage(),
            ]);

            $this->manager = null;
        }
    }

    /**
     * Process an image (uploaded or existing) and store it on the public disk.
     *
     * @param UploadedFile|string $source Uploaded file instance or absolute path to an image file.
     * @param string $directory Target directory inside the public disk (e.g. "orders/items/covers").
     * @param array $options {
     *     @var int|null    $max_width   Max width for resize (maintains ratio).
     *     @var int|null    $max_height  Max height for resize (maintains ratio).
     *     @var int         $quality     Encode quality (0-100).
     *     @var string|null $format      Desired output format (jpg, png, webp, auto|null to keep).
     *     @var string|null $filename    Force filename without directory.
     *     @var bool        $optimize    Whether to resize if exceeds max dimensions.
     * }
     */
    public function processAndStore(UploadedFile|string $source, string $directory, array $options = []): ?string
    {
        $options = array_merge([
            'max_width' => 1600,
            'max_height' => 1600,
            'quality' => 85,
            'format' => 'auto',
            'filename' => null,
            'optimize' => true,
        ], $options);

        $directory = $this->normalizeDirectory($directory);

        // Fallback: se não houver driver de imagem disponível, copiar arquivo diretamente
        if (!$this->manager) {
            if ($source instanceof UploadedFile) {
                $format = $this->guessSourceFormat($source) ?? 'jpg';
                $filename = $options['filename'] ?? $this->generateFilename($format, $source);

                $publicImagesPath = public_path('images/' . $directory);

                if (!file_exists($publicImagesPath)) {
                    File::makeDirectory($publicImagesPath, 0755, true);
                }

                $fullPath = $publicImagesPath . '/' . $filename;

                try {
                    $source->move($publicImagesPath, $filename);
                } catch (\Throwable $e) {
                    \Log::error('ImageProcessor: falha ao copiar arquivo sem processamento', [
                        'error' => $e->getMessage(),
                        'full_path' => $fullPath,
                    ]);
                    return null;
                }

                if (!file_exists($fullPath)) {
                    \Log::error('ImageProcessor: arquivo não encontrado após copiar sem processamento', [
                        'full_path' => $fullPath,
                    ]);
                    return null;
                }

                \Log::info('ImageProcessor: imagem salva sem processamento em public/images', [
                    'relative_path' => $directory . '/' . $filename,
                    'full_path' => $fullPath,
                ]);

                return $directory . '/' . $filename;
            }

            \Log::warning('ImageProcessor: nenhum driver disponível e source não é UploadedFile, pulando processamento.');
            return null;
        }

        $image = $this->createImageInstance($source);
        if (!$image) {
            return null;
        }

        $image = $image->orient();

        if ($options['optimize']) {
            $image = $this->resizeImage($image, $options['max_width'], $options['max_height']);
        }

        $format = $this->determineFormat($image, $source, $options['format']);
        $quality = (int) $options['quality'];

        $filename = $options['filename'] ?? $this->generateFilename($format, $source);
        $relativePath = $directory . '/' . $filename;

        $encoded = $image->encodeByExtension($format, $quality);

        // Salvar diretamente em public/images em vez de storage/app/public
        // Isso elimina a necessidade de symlink
        $publicImagesPath = public_path('images/' . $directory);
        
        // Criar diretório se não existir
        if (!file_exists($publicImagesPath)) {
            File::makeDirectory($publicImagesPath, 0755, true);
        }
        
        $fullPath = $publicImagesPath . '/' . $filename;
        $saved = file_put_contents($fullPath, $encoded->toString());
        
        if ($saved === false) {
            \Log::error('ImageProcessor: Falha ao salvar imagem', [
                'path' => $relativePath,
                'directory' => $directory,
                'filename' => $filename,
                'full_path' => $fullPath
            ]);
            return null;
        }
        
        // Verificar se o arquivo foi realmente salvo
        if (!file_exists($fullPath)) {
            \Log::error('ImageProcessor: Arquivo não encontrado após salvar', [
                'relative_path' => $relativePath,
                'full_path' => $fullPath
            ]);
            return null;
        }
        
        // No servidor Hostinger, também salvar em public_html/images/ para acesso direto
        // Detectar se estamos no servidor Hostinger (caminho contém /home2/ ou /home/)
        $publicPath = public_path();
        if (str_contains($publicPath, '/home2/') || str_contains($publicPath, '/home/')) {
            // Extrair o caminho base (ex: /home2/dd173158)
            if (preg_match('#(/home2?/[^/]+)#', $publicPath, $matches)) {
                $homePath = $matches[1];
                $publicHtmlPath = $homePath . '/public_html/images/' . $directory;
                
                // Criar diretório se não existir
                if (!file_exists($publicHtmlPath)) {
                    File::makeDirectory($publicHtmlPath, 0755, true);
                }
                
                $publicHtmlFullPath = $publicHtmlPath . '/' . $filename;
                $copied = file_put_contents($publicHtmlFullPath, $encoded->toString());
                
                if ($copied !== false && file_exists($publicHtmlFullPath)) {
                    \Log::info('ImageProcessor: Imagem também salva em public_html/images', [
                        'path' => $publicHtmlFullPath
                    ]);
                } else {
                    \Log::warning('ImageProcessor: Falha ao copiar para public_html/images', [
                        'path' => $publicHtmlFullPath
                    ]);
                }
            }
        }
        
        \Log::info('ImageProcessor: Imagem salva com sucesso em public/images', [
            'relative_path' => $relativePath,
            'full_path' => $fullPath,
            'file_size' => filesize($fullPath),
            'format' => $format
        ]);

        // Retornar caminho relativo a partir de images/
        return $relativePath;
    }

    /**
     * Remove a stored image from the public disk.
     */
    public function delete(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        
        // Tentar deletar de public/images primeiro (novo local)
        $publicPath = public_path('images/' . $relativePath);
        if (file_exists($publicPath)) {
            @unlink($publicPath);
            \Log::info('ImageProcessor: Imagem deletada de public/images', ['path' => $publicPath]);
        }
        
        // No servidor Hostinger, também deletar de public_html/images/
        $publicPathBase = public_path();
        if (str_contains($publicPathBase, '/home2/') || str_contains($publicPathBase, '/home/')) {
            if (preg_match('#(/home2?/[^/]+)#', $publicPathBase, $matches)) {
                $homePath = $matches[1];
                $publicHtmlPath = $homePath . '/public_html/images/' . $relativePath;
                if (file_exists($publicHtmlPath)) {
                    @unlink($publicHtmlPath);
                    \Log::info('ImageProcessor: Imagem deletada de public_html/images', ['path' => $publicHtmlPath]);
                }
            }
        }
        
        // Também tentar deletar de storage/app/public (compatibilidade com imagens antigas)
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
            \Log::info('ImageProcessor: Imagem deletada de storage/app/public', ['path' => $relativePath]);
        }
    }

    private function createImageInstance(UploadedFile|string $source): ?ImageInterface
    {
        // Se o manager não estiver disponível, não há o que fazer
        if (!$this->manager) {
            return null;
        }

        try {
            if ($source instanceof UploadedFile) {
                return $this->manager->read($source->getRealPath());
            }

            if (is_string($source) && file_exists($source)) {
                return $this->manager->read($source);
            }
        } catch (\Throwable $exception) {
            \Log::warning('ImageProcessor failed to read image', [
                'error' => $exception->getMessage(),
            ]);
        }

        return null;
    }

    private function resizeImage(ImageInterface $image, ?int $maxWidth, ?int $maxHeight): ImageInterface
    {
        $width = $image->width();
        $height = $image->height();

        if (($maxWidth && $width > $maxWidth) || ($maxHeight && $height > $maxHeight)) {
            return $image->scaleDown($maxWidth ?? $width, $maxHeight ?? $height);
        }

        return $image;
    }

    private function determineFormat(ImageInterface $image, UploadedFile|string $source, ?string $format): string
    {
        $format = $format ? strtolower($format) : null;

        if ($format === 'auto' || !$format) {
            // Keep original extension when possible
            $format = $this->guessSourceFormat($source) ?? 'jpg';

            // If image has transparency avoid jpeg
            if ($format === 'jpg' && $this->hasTransparency($image)) {
                $format = 'png';
            }
        }

        if ($format === 'jpeg') {
            $format = 'jpg';
        }

        return in_array($format, ['jpg', 'png', 'webp']) ? $format : 'jpg';
    }

    private function guessSourceFormat(UploadedFile|string $source): ?string
    {
        if ($source instanceof UploadedFile) {
            return strtolower($source->getClientOriginalExtension()) ?: null;
        }

        if (is_string($source)) {
            $extension = pathinfo($source, PATHINFO_EXTENSION);
            return $extension ? strtolower($extension) : null;
        }

        return null;
    }

    private function hasTransparency(ImageInterface $image): bool
    {
        try {
            return $image->pickColor(0, 0, 'rgba')[3] < 1;
        } catch (\Throwable $exception) {
            return false;
        }
    }

    private function generateFilename(string $format, UploadedFile|string $source): string
    {
        $base = Str::uuid()->toString();

        if ($source instanceof UploadedFile) {
            $original = pathinfo($source->getClientOriginalName(), PATHINFO_FILENAME);
            $base = $this->slugify($original) . '_' . $base;
        } elseif (is_string($source)) {
            $original = pathinfo($source, PATHINFO_FILENAME);
            if ($original) {
                $base = $this->slugify($original) . '_' . $base;
            }
        }

        return $base . '.' . $format;
    }

    private function slugify(string $value): string
    {
        $value = preg_replace('/[^A-Za-z0-9\-]+/', '-', $value);
        $value = trim($value ?? '', '-');

        return $value ?: Str::uuid()->toString();
    }

    private function normalizeDirectory(string $directory): string
    {
        return trim(str_replace('\\', '/', $directory), '/');
    }
}

