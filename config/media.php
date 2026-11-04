<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image processing (kompresi + watermark)
    |--------------------------------------------------------------------------
    |
    | Semua upload gambar lewat Media::storeUploaded / MediaImageProcessor
    | divalidasi ketat, di-re-encode (buang EXIF), dikompres, lalu di-watermark.
    | Kualitas tinggi agar visual tetap tajam; penghematan utama dari resize.
    |
    */

    'image' => [
        // Batas keamanan (anti decompression bomb)
        'max_megapixels' => 40,
        'max_upload_bytes' => 5 * 1024 * 1024, // 5MB input mentah

        // Resize: hanya jika lebih besar (tidak di-upscale)
        'max_width' => 1600,
        'max_height' => 1600,

        // Output: webp (lebih hemat) atau jpg
        'format' => 'webp', // webp|jpg
        'quality' => 88, // tinggi = sedikit artifact, hemat lewat resize

        // Watermark
        'watermark' => [
            'enabled' => true,
            'path' => public_path('images/brand/logo-dark.png'),
            // Lebar watermark relatif terhadap gambar (maks 280px)
            'width_ratio' => 0.18,
            'max_width' => 280,
            'min_width' => 96,
            'opacity' => 42, // 0–100
            'padding_ratio' => 0.025,
        ],

        'allowed_mimes' => [
            'image/jpeg',
            'image/png',
            'image/webp',
        ],
    ],

];
