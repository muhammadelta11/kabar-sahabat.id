<?php

namespace App\Support;

use App\Models\Post;
use App\Models\User;
use App\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class RedakturOperationalRecap
{
    public static function cacheKey(): string
    {
        return 'admin.redaktur_operational.'.now()->toDateString().'.'.now()->startOfWeek()->format('Y-m-d');
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::cacheKey());
        Cache::forget('admin.redaktur_performance.'.now()->toDateString());
    }

    public static function weekDaysCounted(): int
    {
        return max(1, (int) now()->startOfWeek()->diffInDays(now()->startOfDay()) + 1);
    }

    /**
     * @return Collection<int, array{
     *     user: User,
     *     uploaded_today: int,
     *     published_today: int,
     *     uploaded_week: int,
     *     published_week: int,
     *     active_days_week: int,
     *     consistency_week: float,
     *     low_today: bool,
     *     low_week: bool
     * }>
     */
    public static function rows(): Collection
    {
        $today = now()->toDateString();
        $weekStart = now()->startOfWeek()->toDateTimeString();
        $weekEnd = now()->toDateTimeString();
        $weekDays = self::weekDaysCounted();

        return Cache::remember(self::cacheKey(), 60, function () use ($today, $weekStart, $weekEnd, $weekDays) {
            $activeDaysSub = Post::query()
                ->selectRaw('count(distinct date(published_at))')
                ->whereColumn('posts.user_id', 'users.id')
                ->where('status', 'Published')
                ->whereBetween('published_at', [$weekStart, $weekEnd]);

            return User::query()
                ->select(['users.id', 'users.name', 'users.role'])
                ->whereIn('role', [UserRole::Redaktur, UserRole::PimpinanRedaktur])
                ->selectSub($activeDaysSub, 'active_days_week')
                ->withCount([
                    'posts as uploaded_today' => fn (Builder $query) => $query
                        ->whereDate('created_at', $today),
                    'posts as uploaded_week' => fn (Builder $query) => $query
                        ->where('created_at', '>=', $weekStart),
                    'posts as published_today' => fn (Builder $query) => $query
                        ->where('status', 'Published')
                        ->whereDate('published_at', $today),
                    'posts as published_week' => fn (Builder $query) => $query
                        ->where('status', 'Published')
                        ->where('published_at', '>=', $weekStart),
                ])
                ->orderByDesc('published_today')
                ->orderByDesc('uploaded_today')
                ->get()
                ->map(function (User $user) use ($weekDays): array {
                    $activeDays = (int) $user->active_days_week;
                    $publishedToday = (int) $user->published_today;
                    $consistency = round(min($activeDays / $weekDays, 1) * 100, 1);

                    return [
                        'user' => $user,
                        'uploaded_today' => (int) $user->uploaded_today,
                        'published_today' => $publishedToday,
                        'uploaded_week' => (int) $user->uploaded_week,
                        'published_week' => (int) $user->published_week,
                        'active_days_week' => $activeDays,
                        'consistency_week' => $consistency,
                        'low_today' => $publishedToday === 0,
                        'low_week' => $consistency < 40,
                    ];
                });
        });
    }
}
