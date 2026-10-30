<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach($posts as $post)
    <url>
        <loc>{{ route('news.show', $post->slug) }}</loc>
        <lastmod>{{ ($post->updated_at ?? $post->published_at)->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
        @if($post->image_url)
        <image:image>
            <image:loc>{{ media_url($post->image_url) }}</image:loc>
        </image:image>
        @endif
    </url>
    @endforeach
</urlset>
