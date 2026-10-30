<?php

use App\Support\Media;

if (! function_exists('media_url')) {
    /**
     * URL publik media (lokal /storage atau CDN/S3).
     */
    function media_url(?string $path): ?string
    {
        return Media::url($path);
    }
}

if (! function_exists('media_disk')) {
    /**
     * Nama disk media aktif.
     */
    function media_disk(): string
    {
        return Media::diskName();
    }
}

if (! function_exists('media_absolute_url')) {
    /**
     * URL absolut media untuk OG, JSON-LD, dan sitemap.
     */
    function media_absolute_url(?string $path): ?string
    {
        return Media::absoluteUrl($path);
    }
}
