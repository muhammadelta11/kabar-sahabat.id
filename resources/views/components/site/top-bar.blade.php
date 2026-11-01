@props([
    'latestPosts' => null,
])

@php
    $latestPosts = collect($latestPosts ?? []);
    $siteSettings = $siteSettings ?? \App\Support\NewsCache::siteSettings();
    $sorotan = $latestPosts->take(3);
@endphp

<div {{ $attributes->class(['bg-brand-deep text-white text-[11px]']) }}>
    <div class="site-container h-9 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 sm:gap-3 overflow-hidden min-w-0 flex-1">
            <span class="text-white/60 font-medium whitespace-nowrap hidden sm:inline">{{ now()->translatedFormat('l, d F Y') }}</span>
            <span class="text-white/60 font-medium whitespace-nowrap sm:hidden">{{ now()->translatedFormat('D, d M') }}</span>

            @if($sorotan->isNotEmpty())
                <span class="w-px h-3 bg-white/20 flex-shrink-0"></span>
                <div class="flex items-center gap-2 text-white/80 overflow-hidden flex-1 min-w-0">
                    <span class="bg-accent text-white px-1.5 py-0.5 font-bold uppercase tracking-wider text-[9px] rounded-sm flex-shrink-0">Sorotan</span>
                    @if($sorotan->count() === 1)
                        <a href="{{ route('news.show', $sorotan->first()->slug) }}" class="hover:text-accent transition-colors truncate font-medium">
                            {{ $sorotan->first()->title }}
                        </a>
                    @else
                        <div class="sorotan-ticker" aria-live="off">
                            <ul class="sorotan-track">
                                @foreach($sorotan->concat($sorotan) as $sorotanPost)
                                    <li>
                                        <a href="{{ route('news.show', $sorotanPost->slug) }}">{{ $sorotanPost->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
            @auth
                @php
                    $authUser = auth()->user();
                    $isPanelUser = $authUser?->role instanceof \App\UserRole && $authUser->role->canAccessPanel();
                    $isKontributor = $authUser?->isKontributor() ?? false;
                @endphp
                <div class="flex items-center gap-1.5 sm:gap-2">
                    @if($isPanelUser)
                        <a href="{{ url('/admin') }}" class="inline-flex items-center gap-1 rounded-full bg-white/10 hover:bg-accent px-2 sm:px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-white transition-colors" title="Panel admin">
                            <span class="hidden sm:inline">Admin</span>
                            <span class="sm:hidden">Panel</span>
                        </a>
                    @endif
                    @if($isKontributor)
                        <a href="{{ route('account.edit') }}" class="inline-flex items-center gap-1 rounded-full bg-accent/90 hover:bg-accent px-2 sm:px-2.5 py-1 text-[10px] font-bold text-white transition-colors max-w-[9rem] truncate" title="Akun saya">
                            {{ \Illuminate\Support\Str::of($authUser->name)->before(' ')->limit(12) }}
                        </a>
                    @elseif(! $isPanelUser)
                        <a href="{{ route('account.edit') }}" class="inline-flex items-center rounded-full bg-white/10 hover:bg-accent px-2 py-1 text-[10px] font-bold text-white transition-colors">Akun</a>
                    @endif
                    <form method="POST" action="{{ route('auth.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-full border border-white/25 hover:bg-white hover:text-brand-deep px-2 py-1 text-[10px] font-bold text-white/90 transition-colors" title="Keluar">
                            Keluar
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('auth.login') }}" class="hidden sm:inline-flex items-center rounded-full bg-white/10 hover:bg-accent px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-white transition-colors">
                    Masuk
                </a>
            @endauth

            <div class="hidden sm:flex items-center gap-1.5 [&_a]:w-7 [&_a]:h-7 [&_a]:bg-white/10 [&_a]:hover:bg-accent [&_a]:hover:text-white [&_div]:mt-0 [&_div]:gap-1.5">
                @include('partials.social-links')
            </div>
        </div>
    </div>
</div>
