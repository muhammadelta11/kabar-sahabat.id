<?php

namespace App\Support;

use Illuminate\Support\Str;

class SeoAnalyzer
{
    /**
     * @return array{
     *     score: int,
     *     grade: string,
     *     label: string,
     *     checks: list<array{key: string, label: string, ok: bool, weight: int, message: string}>
     * }
     */
    public static function analyze(
        ?string $title = null,
        ?string $slug = null,
        ?string $excerpt = null,
        ?string $content = null,
        mixed $image = null,
    ): array {
        $title = trim(html_entity_decode((string) $title));
        $slug = trim((string) $slug);
        $excerpt = Str::squish(html_entity_decode(strip_tags((string) $excerpt)));
        $plain = Str::squish(html_entity_decode(strip_tags((string) $content)));
        $wordCount = str_word_count($plain);
        $titleLen = mb_strlen($title);
        $excerptLen = mb_strlen($excerpt);
        $hasImage = filled($image) && (! is_array($image) || count(array_filter($image)) > 0);
        $hasHeading = (bool) preg_match('/<(h2|h3)(\s|>)/i', (string) $content);
        $slugClean = $slug !== '' && (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) && mb_strlen($slug) <= 75;
        $titleHasStopSpam = ! preg_match('/[!?]{2,}|\b(klik|viral|heboh)\b/iu', $title);

        $checks = [
            [
                'key' => 'title_length',
                'label' => 'Panjang judul',
                'ok' => $titleLen >= 35 && $titleLen <= 70,
                'weight' => 20,
                'message' => $titleLen === 0
                    ? 'Judul masih kosong.'
                    : ($titleLen < 35
                        ? "Judul terlalu pendek ({$titleLen} karakter). Ideal 35–70."
                        : ($titleLen > 70
                            ? "Judul terlalu panjang ({$titleLen} karakter). Ideal 35–70."
                            : "Judul pas ({$titleLen} karakter).")),
            ],
            [
                'key' => 'slug',
                'label' => 'Slug URL',
                'ok' => $slugClean,
                'weight' => 10,
                'message' => $slugClean
                    ? 'Slug bersih dan SEO-friendly.'
                    : 'Slug sebaiknya huruf kecil, strip, tanpa spasi/simbol (maks. 75).',
            ],
            [
                'key' => 'excerpt',
                'label' => 'Meta deskripsi / ringkasan',
                'ok' => $excerptLen >= 120 && $excerptLen <= 165,
                'weight' => 15,
                'message' => $excerptLen === 0
                    ? 'Isi ringkasan 120–165 karakter untuk cuplikan Google/WA.'
                    : ($excerptLen < 120
                        ? "Ringkasan kurang ({$excerptLen}). Tambah hingga 120–165 karakter."
                        : ($excerptLen > 165
                            ? "Ringkasan kepanjangan ({$excerptLen}). Potong ke 120–165 karakter."
                            : "Ringkasan pas ({$excerptLen} karakter).")),
            ],
            [
                'key' => 'content_length',
                'label' => 'Panjang isi artikel',
                'ok' => $wordCount >= 300,
                'weight' => 25,
                'message' => $wordCount === 0
                    ? 'Isi artikel masih kosong. Target minimal ±300 kata.'
                    : ($wordCount < 300
                        ? "Baru {$wordCount} kata. Tambah detail agar minimal 300 kata."
                        : "Isi cukup panjang ({$wordCount} kata)."),
            ],
            [
                'key' => 'headings',
                'label' => 'Subjudul (H2/H3)',
                'ok' => $hasHeading,
                'weight' => 10,
                'message' => $hasHeading
                    ? 'Ada subjudul — membantu struktur & SEO.'
                    : 'Tambahkan minimal 1 subjudul (Heading 2/3) di isi.',
            ],
            [
                'key' => 'image',
                'label' => 'Gambar utama',
                'ok' => $hasImage,
                'weight' => 15,
                'message' => $hasImage
                    ? 'Gambar utama sudah ada.'
                    : 'Lengkapi gambar utama (ideal rasio 16:9).',
            ],
            [
                'key' => 'title_tone',
                'label' => 'Nada judul',
                'ok' => $title !== '' && $titleHasStopSpam,
                'weight' => 5,
                'message' => $titleHasStopSpam
                    ? 'Judul terkesan natural.'
                    : 'Hindari clickbait berlebihan di judul.',
            ],
        ];

        $earned = 0;
        $total = 0;
        foreach ($checks as $check) {
            $total += $check['weight'];
            if ($check['ok']) {
                $earned += $check['weight'];
            }
        }

        $score = $total > 0 ? (int) round(($earned / $total) * 100) : 0;
        [$grade, $label] = match (true) {
            $score >= 90 => ['A', 'Sangat baik'],
            $score >= 75 => ['B', 'Baik'],
            $score >= 60 => ['C', 'Cukup — masih bisa ditingkatkan'],
            default => ['D', 'Perlu perbaikan'],
        };

        return [
            'score' => $score,
            'grade' => $grade,
            'label' => $label,
            'checks' => $checks,
            'word_count' => $wordCount,
            'title_length' => $titleLen,
            'excerpt_length' => $excerptLen,
        ];
    }
}
