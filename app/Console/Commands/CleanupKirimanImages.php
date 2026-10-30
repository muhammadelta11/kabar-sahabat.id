<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('kiriman:bersihkan {--hari=1 : Usia file minimum dalam hari}')]
#[Description('Menghapus gambar kiriman publik yang tidak direferensikan artikel mana pun')]
class CleanupKirimanImages extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $disk = Storage::disk('public');

        if (! $disk->directoryExists('kiriman')) {
            $this->info('Tidak ada direktori kiriman.');

            return self::SUCCESS;
        }

        $referenced = [];

        Post::query()
            ->where('content', 'like', '%storage/kiriman/%')
            ->select('content')
            ->chunk(500, function ($posts) use (&$referenced): void {
                foreach ($posts as $post) {
                    preg_match_all('#storage/kiriman/([A-Za-z0-9]+\.(?:jpe?g|png|webp))#i', (string) $post->content, $matches);
                    $referenced = array_merge($referenced, $matches[1]);
                }
            });

        $referenced = array_unique($referenced);
        $cutoff = now()->subDays(max(1, (int) $this->option('hari')))->getTimestamp();
        $deleted = 0;

        foreach ($disk->files('kiriman') as $path) {
            $filename = basename($path);

            if (in_array($filename, $referenced)) {
                continue;
            }

            if ($disk->lastModified($path) > $cutoff) {
                continue;
            }

            $disk->delete($path);
            $deleted++;
        }

        $this->info("{$deleted} gambar yatim dihapus.");

        return self::SUCCESS;
    }
}
