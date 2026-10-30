<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\PostSlugRedirect;
use App\Support\PostSlug;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('posts:normalize-slugs {--dry-run : Hanya tampilkan perubahan tanpa menyimpan}')]
#[Description('Ubah slug judul mentah menjadi slug SEO + catat redirect 301')]
class NormalizePostSlugs extends Command
{
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $changed = 0;

        Post::query()->orderBy('id')->each(function (Post $post) use ($dryRun, &$changed): void {
            if (PostSlug::isClean($post->slug)) {
                return;
            }

            $oldSlug = $post->slug;
            $newSlug = PostSlug::unique($post->title ?: $oldSlug, $post->id);

            if ($oldSlug === $newSlug) {
                return;
            }

            $this->line("{$post->id}: {$oldSlug}");
            $this->line("   -> {$newSlug}");

            if ($dryRun) {
                $changed++;

                return;
            }

            PostSlugRedirect::query()->where('old_slug', $newSlug)->delete();

            PostSlugRedirect::query()->updateOrCreate(
                ['old_slug' => $oldSlug],
                ['post_id' => $post->id]
            );

            $post->forceFill(['slug' => $newSlug])->saveQuietly();
            $changed++;
        });

        $this->info(($dryRun ? '[dry-run] ' : '')."Selesai. diubah={$changed}");

        if (! $dryRun && $changed > 0) {
            $this->comment('URL lama otomatis 301 ke slug baru.');
        }

        return self::SUCCESS;
    }
}
