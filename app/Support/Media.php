<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media
{
    public static function diskName(): string
    {
        return (string) config('filesystems.media_disk', 'public');
    }

    public static function disk(): Filesystem
    {
        return Storage::disk(self::diskName());
    }

    /**
     * URL publik untuk file media (gambar berita, logo, iklan, kiriman).
     */
    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        // Legacy path dari konten HTML: /storage/kiriman/xxx.jpg
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        // Disk lokal: URL relatif agar tidak tergantung APP_URL (sering salah di hosting).
        if (self::diskName() === 'public') {
            return '/storage/'.$path;
        }

        return self::disk()->url($path);
    }

    public static function exists(?string $path): bool
    {
        if (blank($path)) {
            return false;
        }

        return self::disk()->exists(ltrim($path, '/'));
    }

    public static function delete(?string $path): bool
    {
        if (blank($path) || ! self::exists($path)) {
            return false;
        }

        return self::disk()->delete(ltrim($path, '/'));
    }

    public static function put(string $path, mixed $contents, array $options = []): string
    {
        $options = array_merge(['visibility' => 'public'], $options);
        self::disk()->put($path, $contents, $options);

        return $path;
    }

    public static function storeUploaded(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, [
            'disk' => self::diskName(),
            'visibility' => 'public',
        ]);
    }

    /**
     * Ubah src /storage/... di HTML artikel menjadi URL media (CDN/S3/local).
     */
    public static function rewriteContentUrls(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        return (string) preg_replace_callback(
            '#(src|href)=([\'"])(/?storage/[^\'"]+)\2#i',
            function (array $matches): string {
                $attr = $matches[1];
                $quote = $matches[2];
                $url = self::url($matches[3]);

                return $attr.'='.$quote.$url.$quote;
            },
            $html
        );
    }

    /**
     * Apakah URL/path menunjuk ke folder kiriman milik situs ini?
     */
    public static function isTrustedKirimanUrl(string $src): bool
    {
        $path = parse_url($src, PHP_URL_PATH) ?: $src;
        $path = str_replace('\\', '/', $path);

        if (str_contains($path, '/storage/kiriman/') || str_starts_with(ltrim($path, '/'), 'kiriman/')) {
            return true;
        }

        $mediaBase = rtrim((string) config('filesystems.disks.'.self::diskName().'.url'), '/');

        if ($mediaBase !== '' && str_starts_with($src, $mediaBase.'/kiriman/')) {
            return true;
        }

        return Str::contains($src, '/kiriman/') && str_starts_with($src, rtrim((string) config('app.url'), '/'));
    }
}
