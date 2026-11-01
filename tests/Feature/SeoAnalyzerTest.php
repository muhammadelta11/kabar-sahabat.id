<?php

use App\Support\SeoAnalyzer;

it('memberi skor tinggi untuk artikel yang lengkap secara SEO', function () {
    $analysis = SeoAnalyzer::analyze(
        title: 'Pemkab Kendal Siap Bersinergi untuk Kongres IPNU 2026',
        slug: 'pemkab-kendal-siap-bersinergi-kongres-ipnu-2026',
        excerpt: 'Ringkasan berita yang menjelaskan inti peristiwa secara jelas agar cuplikan Google dan WhatsApp tampil rapi serta mengundang pembaca untuk membuka artikel lengkap.',
        content: '<h2>Latar belakang</h2><p>'.str_repeat('Konten berita lengkap dengan banyak kata untuk memenuhi target SEO artikel. ', 45).'</p>',
        image: 'posts/cover.jpg',
    );

    expect($analysis['score'])->toBeGreaterThanOrEqual(90)
        ->and($analysis['grade'])->toBe('A')
        ->and(collect($analysis['checks'])->every(fn ($c) => $c['ok']))->toBeTrue();
});

it('menandai kekurangan judul ringkasan isi dan gambar', function () {
    $analysis = SeoAnalyzer::analyze(
        title: 'Pendek',
        slug: 'Pendek Judul!!',
        excerpt: 'Singkat',
        content: '<p>Sedikit kata.</p>',
        image: null,
    );

    expect($analysis['score'])->toBeLessThan(60)
        ->and($analysis['grade'])->toBe('D')
        ->and(collect($analysis['checks'])->firstWhere('key', 'title_length')['ok'])->toBeFalse()
        ->and(collect($analysis['checks'])->firstWhere('key', 'image')['ok'])->toBeFalse()
        ->and(collect($analysis['checks'])->firstWhere('key', 'content_length')['ok'])->toBeFalse();
});
