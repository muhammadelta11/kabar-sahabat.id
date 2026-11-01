<?php

namespace App\Support;

use App\Models\Post;
use App\Models\User;
use App\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class RedakturMonthlyRecap
{
    /** Target terbit per bulan untuk indeks acuan gaji. */
    public const TARGET_PUBLISHED = 20;

    /** Target total views artikel terbit bulan itu. */
    public const TARGET_VIEWS = 3000;

    /**
     * @return array<string, string>
     */
    public static function monthOptions(int $months = 6): array
    {
        return collect(range(0, $months - 1))
            ->mapWithKeys(function (int $i): array {
                $month = now()->startOfMonth()->subMonths($i);

                return [$month->format('Y-m') => $month->translatedFormat('F Y')];
            })
            ->all();
    }

    public static function parseMonth(string $yearMonth): Carbon
    {
        return Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
    }

    /**
     * Hari yang dihitung untuk konsistensi: bulan berjalan = sampai hari ini; bulan lalu = penuh.
     */
    public static function consistencyDenominator(Carbon $monthStart): int
    {
        $monthEnd = $monthStart->copy()->endOfMonth();

        if ($monthStart->isSameMonth(now())) {
            return max(1, (int) $monthStart->diffInDays(now()->startOfDay()) + 1);
        }

        return (int) $monthStart->daysInMonth;
    }

    /**
     * Indeks 0–100: jumlah 40% + konsistensi 35% + viewers 25%.
     * Ini acuan diskusi gaji, bukan nominal otomatis.
     */
    public static function score(int $published, int $activeDays, int $views, Carbon $monthStart): float
    {
        $days = self::consistencyDenominator($monthStart);

        $publishedScore = min($published / self::TARGET_PUBLISHED, 1) * 40;
        $consistencyScore = min($activeDays / $days, 1) * 35;
        $viewsScore = min($views / self::TARGET_VIEWS, 1) * 25;

        return round($publishedScore + $consistencyScore + $viewsScore, 1);
    }

    /**
     * @return Builder<User>
     */
    public static function query(string $yearMonth): Builder
    {
        $monthStart = self::parseMonth($yearMonth);
        $rangeStart = $monthStart->toDateTimeString();
        $rangeEnd = $monthStart->copy()->endOfMonth()->toDateTimeString();

        $activeDaysSub = Post::query()
            ->selectRaw('count(distinct date(published_at))')
            ->whereColumn('posts.user_id', 'users.id')
            ->where('status', 'Published')
            ->whereBetween('published_at', [$rangeStart, $rangeEnd]);

        return User::query()
            ->whereIn('role', [UserRole::Redaktur, UserRole::PimpinanRedaktur])
            ->select('users.*')
            ->selectSub($activeDaysSub, 'active_days')
            ->withCount([
                'posts as published_month' => fn (Builder $query) => $query
                    ->where('status', 'Published')
                    ->whereBetween('published_at', [$rangeStart, $rangeEnd]),
            ])
            ->withSum([
                'posts as views_month' => fn (Builder $query) => $query
                    ->where('status', 'Published')
                    ->whereBetween('published_at', [$rangeStart, $rangeEnd]),
            ], 'views_count')
            ->orderByDesc('published_month');
    }

    /**
     * @return Collection<int, array{user: User, published: int, active_days: int, views: int, consistency: float, score: float}>
     */
    public static function rows(string $yearMonth): Collection
    {
        $monthStart = self::parseMonth($yearMonth);
        $days = self::consistencyDenominator($monthStart);

        return self::query($yearMonth)
            ->get()
            ->map(function (User $user) use ($monthStart, $days): array {
                $published = (int) $user->published_month;
                $activeDays = (int) $user->active_days;
                $views = (int) ($user->views_month ?? 0);

                return [
                    'user' => $user,
                    'published' => $published,
                    'active_days' => $activeDays,
                    'views' => $views,
                    'consistency' => round(min($activeDays / $days, 1) * 100, 1),
                    'score' => self::score($published, $activeDays, $views, $monthStart),
                ];
            });
    }
}
