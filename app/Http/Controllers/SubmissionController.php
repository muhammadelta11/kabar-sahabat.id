<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class SubmissionController extends Controller
{
    /**
     * Tipe gambar yang diizinkan: [ekstensi, fungsi decode GD, fungsi encode GD]
     */
    private const IMAGE_TYPES = [
        IMAGETYPE_JPEG => ['jpg', 'imagecreatefromjpeg', 'imagejpeg'],
        IMAGETYPE_PNG => ['png', 'imagecreatefrompng', 'imagepng'],
        IMAGETYPE_WEBP => ['webp', 'imagecreatefromwebp', 'imagewebp'],
    ];

    public function create()
    {
        return view('submissions.create');
    }

    public function store(Request $request)
    {
        // Honeypot: hanya bot yang mengisi field tersembunyi ini. Buang diam-diam.
        if (filled($request->input('website'))) {
            return redirect()->route('submissions.create')->with('submitted', true);
        }

        $validated = $request->validate([
            'author_name' => ['required', 'string', 'max:100'],
            'author_wa' => ['required', 'string', 'max:20', 'regex:/^(?:\+62|62|0)8\d{7,12}$/'],
            'author_instansi' => ['nullable', 'string', 'max:150'],
            'title' => ['required', 'string', 'min:10', 'max:150'],
            'content' => ['required', 'string'],
            'agreement' => ['accepted'],
        ], [
            'author_name.required' => 'Nama wajib diisi.',
            'author_name.max' => 'Nama maksimal 100 karakter.',
            'author_wa.required' => 'Nomor WhatsApp wajib diisi.',
            'author_wa.regex' => 'Format nomor WhatsApp tidak valid. Gunakan format 08xxxxxxxxxx.',
            'title.required' => 'Judul wajib diisi.',
            'title.min' => 'Judul minimal 10 karakter.',
            'content.required' => 'Isi artikel wajib diisi.',
            'agreement.accepted' => 'Anda harus menyetujui penyuntingan redaksi.',
        ]);

        if (mb_strlen(trim(strip_tags($validated['content']))) < 200) {
            return back()->withErrors(['content' => 'Isi artikel minimal 200 karakter.'])->withInput();
        }

        Post::create([
            'user_id' => null,
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']).'-'.Str::lower(Str::random(8)),
            'content' => $this->sanitizeContent($validated['content']),
            'status' => 'Menunggu Review',
            'author_name' => $validated['author_name'],
            'author_wa' => preg_replace('/[\s\-]/', '', $validated['author_wa']),
            'author_instansi' => $validated['author_instansi'] ?? null,
        ]);

        return redirect()->route('submissions.create')->with('submitted', true);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:2048'],
        ]);

        $file = $request->file('file');

        $info = @getimagesize($file->getRealPath());
        $imageType = $info[2] ?? null;

        if (! isset(self::IMAGE_TYPES[$imageType])) {
            return response()->json(['message' => 'File bukan gambar yang didukung (jpg/png/webp).'], 422);
        }

        [$extension, $decoder, $encoder] = self::IMAGE_TYPES[$imageType];

        $source = @$decoder($file->getRealPath());

        if (! $source) {
            return response()->json(['message' => 'Gambar tidak dapat dibaca.'], 422);
        }

        // Re-encode: membuang EXIF/GPS dan payload apa pun yang diselundupkan dalam file
        ob_start();

        match ($encoder) {
            'imagepng' => imagepng($source, null, 6),
            default => $encoder($source, null, 85),
        };

        $binary = (string) ob_get_clean();
        imagedestroy($source);

        $filename = Str::random(40).'.'.$extension;
        Storage::disk('public')->put('kiriman/'.$filename, $binary);

        return response()->json([
            'url' => Storage::url('kiriman/'.$filename),
        ]);
    }

    /**
     * Bersihkan HTML kiriman publik, lalu pastikan:
     * - <img> hanya menunjuk ke /storage/kiriman milik situs sendiri
     * - semua <a> membawa rel noopener noreferrer
     */
    private function sanitizeContent(string $html): string
    {
        $clean = Purifier::clean($html, 'kiriman');

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$clean);
        libxml_clear_errors();

        foreach (iterator_to_array($dom->getElementsByTagName('img')) as $img) {
            $path = parse_url((string) $img->getAttribute('src'), PHP_URL_PATH) ?: '';

            if (str_starts_with($path, '/storage/kiriman/')) {
                $img->setAttribute('src', $path);
            } else {
                $img->parentNode?->removeChild($img);
            }
        }

        foreach ($dom->getElementsByTagName('a') as $anchor) {
            $anchor->setAttribute('rel', 'noopener noreferrer');
        }

        $output = '';

        foreach ($dom->getElementsByTagName('body')->item(0)?->childNodes ?? [] as $node) {
            $output .= $dom->saveHTML($node);
        }

        return trim($output);
    }
}
