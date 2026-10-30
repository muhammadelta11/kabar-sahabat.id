<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Sitemap index: links to post sitemap and static sitemap.
     */
    public function index(): Response
    {
        $lastPostModified = Post::where('status', 'Published')
            ->orderByDesc('updated_at')
            ->value('updated_at');

        $content = view('sitemap.index', compact('lastPostModified'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    /**
     * Sitemap for all published posts.
     */
    public function posts(): Response
    {
        $posts = Post::where('status', 'Published')
            ->where('published_at', '<=', now())
            ->select(['slug', 'published_at', 'updated_at', 'image_url'])
            ->orderByDesc('published_at')
            ->get();

        $content = view('sitemap.posts', compact('posts'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    /**
     * Sitemap for static pages and categories.
     */
    public function static(): Response
    {
        $categories = Category::orderBy('name')->get(['slug', 'updated_at']);

        $content = view('sitemap.static', compact('categories'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    /**
     * RSS Feed (latest 20 published posts).
     */
    public function feed(): Response
    {
        $posts = Post::with('categories', 'user')
            ->where('status', 'Published')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->limit(20)
            ->get();

        $content = view('sitemap.feed', compact('posts'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/rss+xml; charset=utf-8',
        ]);
    }
}
