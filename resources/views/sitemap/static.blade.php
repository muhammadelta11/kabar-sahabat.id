<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>hourly</changefreq>
        <priority>1.0</priority>
    </url>
    @foreach(['pages.redaksi', 'pages.tentang-kami', 'pages.kontak', 'pages.pedoman-media-siber'] as $pageRoute)
    <url>
        <loc>{{ route($pageRoute) }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    @endforeach
    <url>
        <loc>{{ route('submissions.create') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
    </url>
    @foreach($categories as $category)
    <url>
        <loc>{{ url('/?category=' . $category->slug) }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
</urlset>
