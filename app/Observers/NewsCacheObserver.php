<?php

namespace App\Observers;

use App\Support\NewsCache;

class NewsCacheObserver
{
    public function saved(mixed $model): void
    {
        NewsCache::flush();
    }

    public function deleted(mixed $model): void
    {
        NewsCache::flush();
    }
}
