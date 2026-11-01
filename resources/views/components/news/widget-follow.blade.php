<section {{ $attributes->class(['bg-white rounded-2xl border border-neutral-100 shadow-sm p-4']) }}>
    <h2 class="text-sm font-black text-brand font-display flex items-center gap-2">
        <span class="w-1 h-4 rounded-full bg-accent"></span>
        Follow Kami
    </h2>
    <div class="mt-1 [&>div]:mt-3 [&>div]:justify-start [&_a]:bg-brand [&_a]:hover:bg-accent">
        @include('partials.social-links')
    </div>
</section>
