<?php

use App\Support\Media;
use App\Support\MediaImageProcessor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    Storage::fake('public');
    config(['filesystems.media_disk' => 'public']);
});

function makeTestImageUpload(int $width = 2000, int $height = 1200, string $name = 'foto.jpg'): UploadedFile
{
    $img = imagecreatetruecolor($width, $height);
    $bg = imagecolorallocate($img, 30, 80, 160);
    imagefilledrectangle($img, 0, 0, $width, $height, $bg);
    $tmp = tempnam(sys_get_temp_dir(), 'img');
    imagejpeg($img, $tmp, 95);
    imagedestroy($img);

    return new UploadedFile($tmp, $name, 'image/jpeg', null, true);
}

it('mengompres resize dan menyimpan webp dengan watermark', function () {
    $upload = makeTestImageUpload();
    $originalSize = filesize($upload->getRealPath());

    $path = MediaImageProcessor::store($upload, 'posts');

    expect($path)->toStartWith('posts/')
        ->and($path)->toEndWith('.webp')
        ->and(Media::exists($path))->toBeTrue();

    $stored = Storage::disk('public')->get($path);
    expect(strlen($stored))->toBeLessThan($originalSize);

    $info = getimagesizefromstring($stored);
    expect($info[0])->toBeLessThanOrEqual(1600)
        ->and($info[1])->toBeLessThanOrEqual(1600);
});

it('menolak mime berbahaya / bukan gambar', function () {
    $tmp = tempnam(sys_get_temp_dir(), 'bad');
    file_put_contents($tmp, '<?php echo "hack";');
    $upload = new UploadedFile($tmp, 'evil.jpg', 'image/jpeg', null, true);

    MediaImageProcessor::store($upload, 'posts');
})->throws(ValidationException::class);

it('Media::storeUploaded memakai processor untuk image', function () {
    $path = Media::storeUploaded(makeTestImageUpload(800, 600), 'posts');

    expect($path)->toMatch('/^posts\/.+\.webp$/')
        ->and(Media::exists($path))->toBeTrue();
});

it('bisa menonaktifkan watermark untuk logo settings', function () {
    $path = Media::storeUploaded(makeTestImageUpload(900, 900), 'settings', [
        'watermark' => ['enabled' => false],
        'max_width' => 800,
        'max_height' => 800,
    ]);

    expect($path)->toStartWith('settings/')
        ->and(Media::exists($path))->toBeTrue();

    $info = getimagesizefromstring(Storage::disk('public')->get($path));
    expect($info[0])->toBeLessThanOrEqual(800);
});
