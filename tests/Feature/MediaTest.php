<?php

use App\Support\Media;
use Illuminate\Support\Facades\Storage;

it('membangun url media lokal dari path relatif', function () {
    config(['filesystems.media_disk' => 'public']);

    expect(media_url('posts/foto.jpg'))
        ->toBe('/storage/posts/foto.jpg')
        ->and(media_url('/storage/kiriman/a.jpg'))
        ->toBe('/storage/kiriman/a.jpg')
        ->and(media_url('https://cdn.example/x.jpg'))
        ->toBe('https://cdn.example/x.jpg');
});

it('menulis ulang src /storage di konten artikel', function () {
    config(['filesystems.media_disk' => 'public']);

    $html = '<p><img src="/storage/kiriman/abc.jpg" alt="x"></p>';

    expect(Media::rewriteContentUrls($html))
        ->toContain('/storage/kiriman/abc.jpg')
        ->and(Media::isTrustedKirimanUrl('/storage/kiriman/abc.jpg'))->toBeTrue()
        ->and(Media::isTrustedKirimanUrl('https://evil.test/hack.jpg'))->toBeFalse();
});

it('menyimpan dan menghapus file di disk media', function () {
    Storage::fake('public');
    config(['filesystems.media_disk' => 'public']);

    Media::put('kiriman/test.jpg', 'binary');

    expect(Media::exists('kiriman/test.jpg'))->toBeTrue();

    Media::delete('kiriman/test.jpg');

    expect(Media::exists('kiriman/test.jpg'))->toBeFalse();
});
