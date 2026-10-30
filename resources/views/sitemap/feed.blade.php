<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <channel>
        <title>Kabar Sahabat</title>
        <link>{{ url('/') }}</link>
        <description>Portal berita independen yang menyajikan berita pergerakan, nasional, kaderisasi, opini, dan tokoh terkini.</description>
        <language>id</language>
        <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
        <atom:link href="{{ url('/feed.xml') }}" rel="self" type="application/rss+xml"/>
        @foreach($posts as $post)
        <item>
            <title><![CDATA[{{ $post->title }}]]></title>
            <link>{{ route('news.show', $post->slug) }}</link>
            <guid isPermaLink="true">{{ route('news.show', $post->slug) }}</guid>
            <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
            <dc:creator><![CDATA[{{ $post->penulis ?? $post->user->name ?? 'Redaksi' }}]]></dc:creator>
            @foreach($post->categories as $cat)
            <category><![CDATA[{{ $cat->name }}]]></category>
            @endforeach
            <description><![CDATA[{{ Str::limit(strip_tags($post->excerpt ?? $post->content), 300) }}]]></description>
            @if($post->image_url)
            <enclosure url="{{ media_absolute_url($post->image_url) }}" type="image/jpeg" length="0"/>
            @endif
        </item>
        @endforeach
    </channel>
</rss>
