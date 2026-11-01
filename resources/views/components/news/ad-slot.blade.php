@props([
    'ad' => null,
    'height' => 'h-[250px]',
])

@if($ad)
    <div {{ $attributes->class([$height, 'w-full relative flex items-center justify-center overflow-hidden border border-neutral-100 bg-white rounded-2xl']) }}>
        @if($ad->type === 'image')
            <a href="{{ $ad->link_url }}" target="_blank" rel="noopener noreferrer" class="block w-full h-full">
                <img src="{{ media_url($ad->image_url) }}" class="w-full h-full object-cover" alt="{{ $ad->name }}">
            </a>
        @else
            {!! $ad->script_code !!}
        @endif
    </div>
@endif
