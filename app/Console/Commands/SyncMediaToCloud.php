<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('media:sync-to-cloud {--dry-run : Hanya tampilkan file tanpa mengunggah} {--disk=s3 : Disk tujuan}')]
#[Description('Unggah file dari storage/app/public ke disk cloud (S3/R2/Spaces) untuk CDN')]
class SyncMediaToCloud extends Command
{
    public function handle(): int
    {
        $targetDisk = (string) $this->option('disk');
        $dryRun = (bool) $this->option('dry-run');

        if (! config("filesystems.disks.{$targetDisk}")) {
            $this->error("Disk [{$targetDisk}] tidak dikonfigurasi.");

            return self::FAILURE;
        }

        $source = Storage::disk('public');
        $target = Storage::disk($targetDisk);

        if (! $source->exists('/')) {
            $this->warn('Folder storage/app/public kosong atau belum ada.');
        }

        $files = $source->allFiles();
        $this->info('Menemukan '.count($files).' file di disk public.');

        $uploaded = 0;
        $skipped = 0;

        foreach ($files as $path) {
            if (str_ends_with($path, '.gitignore') || str_ends_with($path, '.gitkeep')) {
                continue;
            }

            if ($dryRun) {
                $this->line("upload {$path}");
                $uploaded++;

                continue;
            }

            try {
                if ($target->exists($path)) {
                    $skipped++;
                    $this->line("skip  {$path}");

                    continue;
                }
            } catch (\Throwable $e) {
                $this->error('Gagal cek disk tujuan (cek AWS_* / kredensial): '.$e->getMessage());

                return self::FAILURE;
            }

            $stream = $source->readStream($path);

            if ($stream === false) {
                $this->error("gagal baca {$path}");

                continue;
            }

            $target->put($path, $stream, [
                'visibility' => 'public',
                'CacheControl' => 'public, max-age=31536000, immutable',
            ]);

            if (is_resource($stream)) {
                fclose($stream);
            }

            $uploaded++;
            $this->line("ok    {$path}");
        }

        $this->newLine();
        $this->info(($dryRun ? '[dry-run] ' : '')."Selesai. upload={$uploaded}, skip={$skipped}");

        if (! $dryRun) {
            $this->comment('Set MEDIA_DISK=s3 dan AWS_URL (CDN) di .env, lalu: php artisan config:cache');
        }

        return self::SUCCESS;
    }
}
