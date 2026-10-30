<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach($posts as $post)
    @php
        $isGoogleNews = $post->published_at && $post->published_at->greaterThanOrEqualTo(now()->subDays(2));
        $newsKeywords = $post->categories->pluck('name')->implode(', ');
    @endphp
    <url>
        <loc>{{ route('news.show', $post->slug) }}</loc>
        <lastmod>{{ ($post->updated_at ?? $post->published_at)->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
        @if($isGoogleNews)
        <news:news>
            <news:publication>
                <news:name>Kabar Sahabat</news:name>
                <news:language>id</news:language>
            </news:publication>
            <news:publication_date>{{ $post->published_at->toAtomString() }}</news:publication_date>
            <news:title>{{ $post->title }}</news:title>
            @if($newsKeywords !== '')
            <news:keywords>{{ $newsKeywords }}</news:keywords>
            @endif
        </news:news>
        @endif
        @if($post->image_url)
        <image:image>
            <image:loc>{{ media_absolute_url($post->image_url) }}</image:loc>
            <image:title>{{ $post->title }}</image:title>
        </image:image>
        @endif
    </url>
    @endforeach
</urlset>
