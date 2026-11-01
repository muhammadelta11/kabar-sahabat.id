<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

function validPayload(array $overrides = []): array
{
    $paragraph = 'Ini adalah isi artikel yang cukup panjang untuk melewati validasi minimal dua ratus karakter. ';

    return array_merge([
        'author_name' => 'Budi Kontributor',
        'author_wa' => '081234567890',
        'title' => 'Judul Artikel Kiriman Publik',
        'content' => '<p>'.str_repeat($paragraph, 5).'</p>',
        'agreement' => '1',
    ], $overrides);
}

function actingAsKontributor(): User
{
    $user = User::factory()->kontributor()->create();
    actingAs($user);

    return $user;
}

it('menyimpan kiriman valid dengan status menunggu review', function () {
    $user = actingAsKontributor();

    post(route('submissions.store'), validPayload())
        ->assertRedirect(route('submissions.create'))
        ->assertSessionHas('submitted');

    $post = Post::query()->sole();

    expect($post->status)->toBe('Menunggu Review')
        ->and($post->author_name)->toBe('Budi Kontributor')
        ->and($post->author_wa)->toBe('081234567890')
        ->and($post->user_id)->toBe($user->id)
        ->and(mb_strlen(strip_tags($post->content)))->toBeGreaterThanOrEqual(200);
});

it('membersihkan xss dari konten kiriman', function () {
    actingAsKontributor();

    $malicious = '<p>Konten awal yang panjang dan aman. </p>'
        .'<script>alert("xss")</script>'
        .'<img src="javascript:alert(1)" onerror="alert(1)">'
        .'<p style="position:fixed" onclick="alert(1)">Paragraf berbahaya dengan event handler.</p>'
        .str_repeat('Teks tambahan agar lolos validasi panjang konten. ', 10);

    post(route('submissions.store'), validPayload(['content' => $malicious]));

    $content = Post::query()->sole()->content;

    expect($content)
        ->not->toContain('<script')
        ->not->toContain('onerror')
        ->not->toContain('onclick')
        ->not->toContain('javascript:')
        ->not->toContain('style=')
        ->not->toContain('<span');
});

it('membuang img eksternal dan hanya mengizinkan path kiriman sendiri', function () {
    actingAsKontributor();

    $local = '/storage/kiriman/abc123.jpg';
    $content = '<div>'
        .'<p>Berikut foto acara. '.str_repeat('Konten panjang. ', 12).'</p>'
        ."<img src=\"{$local}\" alt=\"foto\">"
        .'<img src="https://evil.example/track.png" alt="pelacak">'
        .'</div>';

    post(route('submissions.store'), validPayload(['content' => $content]));

    $stored = Post::query()->sole()->content;

    expect($stored)->toContain($local)
        ->not->toContain('evil.example');
});

it('menormalkan url absolut milik situs menjadi path relatif', function () {
    actingAsKontributor();

    $content = '<p>Paragraf pembuka artikel kontributor. '.str_repeat('Kalimat pengisi. ', 15).'</p>'
        .'<img src="https://kabarsahabat.test/storage/kiriman/namafile123.png" alt="dok">';

    post(route('submissions.store'), validPayload(['content' => $content]));

    $stored = Post::query()->sole()->content;

    expect($stored)->toContain('src="/storage/kiriman/namafile123.png"');
});

it('menambahkan rel noopener pada semua tautan', function () {
    actingAsKontributor();

    $content = '<p>Sumber bermanfaat untuk pembaca semua. '.str_repeat('kalimat pengisi. ', 20).'</p><a href="https://contoh.org">tautan</a>';

    post(route('submissions.store'), validPayload(['content' => $content]));

    expect(Post::query()->sole()->content)->toContain('rel="noopener noreferrer"');
});

it('membuang kiriman honeypot tanpa jejak', function () {
    actingAsKontributor();

    post(route('submissions.store'), validPayload(['website' => 'http://spam.bot']))
        ->assertRedirect(route('submissions.create'));

    expect(Post::count())->toBe(0);
});

it('membatasi frekuensi pengiriman per ip', function () {
    actingAsKontributor();

    foreach ([1, 2, 3] as $i) {
        post(route('submissions.store'), validPayload(['title' => "Judul Artikel Kiriman Nomor {$i}"]))
            ->assertRedirect();
    }

    post(route('submissions.store'), validPayload(['title' => 'Judul Artikel Kiriman Nomor Empat']))
        ->assertStatus(429);
});

it('menolak isi terlalu pendek', function () {
    actingAsKontributor();

    post(route('submissions.store'), validPayload(['content' => '<p>Terlalu pendek</p>']))
        ->assertSessionHasErrors('content');

    expect(Post::count())->toBe(0);
});

it('menolak nomor wa tidak valid', function (string $wa) {
    actingAsKontributor();

    post(route('submissions.store'), validPayload(['author_wa' => $wa]))
        ->assertSessionHasErrors('author_wa');
})->with([
    'huruf' => 'abcd12345',
    'terlalu pendek' => '08123',
    'tanpa nomor' => '+62',
]);

it('menerima upload gambar asli ke direktori kiriman', function () {
    actingAsKontributor();
    Storage::fake('public');

    $response = post(route('submissions.upload'), [
        'file' => UploadedFile::fake()->image('foto.jpg', 800, 600),
    ]);

    $response->assertSuccessful();
    $url = $response->json('url');

    expect($url)->toStartWith('/storage/kiriman/')
        ->and(Storage::disk('public')->exists('kiriman/'.basename($url)))->toBeTrue();
});

it('menolak file teks yang menyamar jadi jpg', function () {
    actingAsKontributor();
    Storage::fake('public');

    $fake = UploadedFile::fake()->createWithContent('shell.jpg', '<?php system($_GET["cmd"]);');

    post(route('submissions.upload'), ['file' => $fake])->assertStatus(422);

    expect(Storage::disk('public')->allFiles('kiriman'))->toBeEmpty();
});
