<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;
use Throwable;

final class MediaImageProcessor
{
    /**
     * Validasi aman → resize (jika perlu) → kompres → watermark → simpan ke media disk.
     *
     * @return string Path relatif di disk media (contoh: posts/abc.webp)
     */
    public static function store(UploadedFile $file, string $directory, ?array $options = null): string
    {
        $config = array_replace_recursive(config('media.image', []), $options ?? []);

        $binary = self::readBinary($file);
        self::assertSafeImage($binary, $file, $config);

        $processed = self::processBinary($binary, $config);

        $extension = ($processed['format'] === 'webp') ? 'webp' : 'jpg';
        $filename = Str::lower(Str::ulid()).'.'.$extension;
        $path = trim($directory, '/').'/'.$filename;

        Media::put($path, $processed['contents'], [
            'visibility' => 'public',
            'ContentType' => $processed['format'] === 'webp' ? 'image/webp' : 'image/jpeg',
        ]);

        return $path;
    }

    /**
     * Callback Filament FileUpload::saveUploadedFileUsing.
     *
     * @return \Closure(mixed, TemporaryUploadedFile): (?string)
     */
    public static function filamentSaver(string $directory, ?array $options = null): \Closure
    {
        return function (mixed $component, TemporaryUploadedFile $file) use ($directory, $options): ?string {
            try {
                if (! $file->exists()) {
                    return null;
                }
            } catch (Throwable) {
                return null;
            }

            try {
                return self::store($file, $directory, $options);
            } catch (ValidationException $e) {
                throw $e;
            } catch (Throwable $e) {
                Log::warning('media.image.process_failed', [
                    'message' => $e->getMessage(),
                    'directory' => $directory,
                ]);

                throw ValidationException::withMessages([
                    'image_url' => 'Gagal memproses gambar. Gunakan JPG/PNG/WebP yang valid (maks. 5MB).',
                ]);
            }
        };
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array{contents: string, format: string, width: int, height: int}
     */
    public static function processBinary(string $binary, array $config): array
    {
        $info = @getimagesizefromstring($binary);
        if ($info === false || empty($info[0]) || empty($info[1])) {
            throw ValidationException::withMessages([
                'image' => 'File bukan gambar yang valid.',
            ]);
        }

        [$width, $height] = [(int) $info[0], (int) $info[1]];
        $megapixels = ($width * $height) / 1_000_000;
        if ($megapixels > (float) $config['max_megapixels']) {
            throw ValidationException::withMessages([
                'image' => 'Resolusi gambar terlalu besar (maks. '.(int) $config['max_megapixels'].' megapiksel).',
            ]);
        }

        $source = @imagecreatefromstring($binary);
        if ($source === false) {
            throw ValidationException::withMessages([
                'image' => 'Gambar tidak dapat dibaca. Coba export ulang ke JPG/PNG.',
            ]);
        }

        imagepalettetotruecolor($source);
        imagealphablending($source, true);
        imagesavealpha($source, true);

        $maxW = (int) $config['max_width'];
        $maxH = (int) $config['max_height'];
        $scale = min(1.0, $maxW / max(1, $width), $maxH / max(1, $height));
        $targetW = max(1, (int) round($width * $scale));
        $targetH = max(1, (int) round($height * $scale));

        if ($scale < 1.0) {
            $canvas = imagecreatetruecolor($targetW, $targetH);
            if ($canvas === false) {
                imagedestroy($source);
                throw new RuntimeException('Gagal membuat canvas gambar.');
            }
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefilledrectangle($canvas, 0, 0, $targetW, $targetH, $transparent);
            imagealphablending($canvas, true);
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetW, $targetH, $width, $height);
            imagedestroy($source);
            $source = $canvas;
            $width = $targetW;
            $height = $targetH;
        }

        if (! empty($config['watermark']['enabled'])) {
            self::applyWatermark($source, $width, $height, $config['watermark']);
        }

        $format = self::resolveFormat($config['format'] ?? 'webp');
        $quality = max(60, min(95, (int) ($config['quality'] ?? 88)));
        $contents = self::encode($source, $format, $quality);
        imagedestroy($source);

        return [
            'contents' => $contents,
            'format' => $format,
            'width' => $width,
            'height' => $height,
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private static function assertSafeImage(string $binary, UploadedFile $file, array $config): void
    {
        $maxBytes = (int) ($config['max_upload_bytes'] ?? (5 * 1024 * 1024));
        if (strlen($binary) > $maxBytes) {
            throw ValidationException::withMessages([
                'image' => 'Ukuran file terlalu besar (maks. '.intdiv($maxBytes, 1024 * 1024).'MB).',
            ]);
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($binary) ?: $file->getMimeType();
        $allowed = $config['allowed_mimes'] ?? ['image/jpeg', 'image/png', 'image/webp'];

        if (! in_array($mime, $allowed, true)) {
            throw ValidationException::withMessages([
                'image' => 'Format tidak diizinkan. Gunakan JPG, PNG, atau WebP.',
            ]);
        }

        // Tolak SVG / polyglot teks
        $head = strtolower(substr($binary, 0, 256));
        if (str_contains($head, '<svg') || str_contains($head, '<?php') || str_contains($head, '<script')) {
            throw ValidationException::withMessages([
                'image' => 'File gambar ditolak karena konten tidak aman.',
            ]);
        }
    }

    private static function readBinary(UploadedFile $file): string
    {
        $path = $file->getRealPath() ?: $file->getPathname();
        if (! is_string($path) || $path === '' || ! is_readable($path)) {
            throw ValidationException::withMessages([
                'image' => 'File upload tidak ditemukan. Unggah ulang gambar.',
            ]);
        }

        $binary = @file_get_contents($path);
        if ($binary === false || $binary === '') {
            throw ValidationException::withMessages([
                'image' => 'File upload kosong atau rusak.',
            ]);
        }

        return $binary;
    }

    /**
     * @param  array<string, mixed>  $watermark
     */
    private static function applyWatermark(\GdImage $image, int $width, int $height, array $watermark): void
    {
        $path = (string) ($watermark['path'] ?? '');
        if ($path === '' || ! is_file($path)) {
            return;
        }

        $wmBinary = @file_get_contents($path);
        if ($wmBinary === false) {
            return;
        }

        $wm = @imagecreatefromstring($wmBinary);
        if ($wm === false) {
            return;
        }

        $wmW = imagesx($wm);
        $wmH = imagesy($wm);
        if ($wmW < 1 || $wmH < 1) {
            imagedestroy($wm);

            return;
        }

        $targetW = (int) round($width * (float) ($watermark['width_ratio'] ?? 0.18));
        $targetW = max((int) ($watermark['min_width'] ?? 96), min((int) ($watermark['max_width'] ?? 280), $targetW));
        $targetH = (int) max(1, round($wmH * ($targetW / $wmW)));

        $resized = imagecreatetruecolor($targetW, $targetH);
        if ($resized === false) {
            imagedestroy($wm);

            return;
        }

        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $clear = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefilledrectangle($resized, 0, 0, $targetW, $targetH, $clear);
        imagealphablending($resized, true);
        imagecopyresampled($resized, $wm, 0, 0, 0, 0, $targetW, $targetH, $wmW, $wmH);
        imagedestroy($wm);

        $pad = (int) round($width * (float) ($watermark['padding_ratio'] ?? 0.025));
        $x = max(0, $width - $targetW - $pad);
        $y = max(0, $height - $targetH - $pad);
        $opacity = max(0, min(100, (int) ($watermark['opacity'] ?? 42)));

        self::mergePngWithOpacity($image, $resized, $x, $y, $opacity);
        imagedestroy($resized);
    }

    private static function mergePngWithOpacity(\GdImage $dest, \GdImage $src, int $dstX, int $dstY, int $opacityPercent): void
    {
        $srcW = imagesx($src);
        $srcH = imagesy($src);
        $opacity = max(0, min(100, $opacityPercent)) / 100;

        imagealphablending($dest, true);
        imagesavealpha($dest, true);

        for ($y = 0; $y < $srcH; $y++) {
            for ($x = 0; $x < $srcW; $x++) {
                $rgba = imagecolorat($src, $x, $y);
                $a = ($rgba & 0x7F000000) >> 24;
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                // GD alpha: 0 opaque .. 127 transparent
                $srcAlpha = 1 - ($a / 127);
                $outAlpha = $srcAlpha * $opacity;
                if ($outAlpha <= 0.01) {
                    continue;
                }

                $dx = $dstX + $x;
                $dy = $dstY + $y;
                if ($dx < 0 || $dy < 0 || $dx >= imagesx($dest) || $dy >= imagesy($dest)) {
                    continue;
                }

                $bg = imagecolorat($dest, $dx, $dy);
                $br = ($bg >> 16) & 0xFF;
                $bgG = ($bg >> 8) & 0xFF;
                $bb = $bg & 0xFF;

                $nr = (int) round($r * $outAlpha + $br * (1 - $outAlpha));
                $ng = (int) round($g * $outAlpha + $bgG * (1 - $outAlpha));
                $nb = (int) round($b * $outAlpha + $bb * (1 - $outAlpha));
                imagesetpixel($dest, $dx, $dy, imagecolorallocate($dest, $nr, $ng, $nb));
            }
        }
    }

    private static function resolveFormat(string $preferred): string
    {
        if ($preferred === 'webp' && function_exists('imagewebp')) {
            return 'webp';
        }

        return 'jpg';
    }

    private static function encode(\GdImage $image, string $format, int $quality): string
    {
        ob_start();
        if ($format === 'webp') {
            imagewebp($image, null, $quality);
        } else {
            imagejpeg($image, null, $quality);
        }
        $contents = ob_get_clean();

        if (! is_string($contents) || $contents === '') {
            throw new RuntimeException('Gagal meng-encode gambar.');
        }

        return $contents;
    }
}
