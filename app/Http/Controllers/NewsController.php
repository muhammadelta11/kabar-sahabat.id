<?php

namespace App\Http\Controllers;

use App\Jobs\RecordPostView;
use App\Models\Post;
use App\Models\PostSlugRedirect;
use App\Support\NewsCache;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query()
            ->with(['categories:id,name,slug', 'user:id,name'])
            ->published()
            ->latest('published_at');

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('excerpt', 'like', '%'.$search.'%');
            });
        }

        // Beranda: page 1 lebih padat (setara ~2 halaman lama), lalu lanjut page 2+.
        $perPage = ($request->filled('category') || $request->filled('q')) ? 15 : 30;
        $posts = $query->paginate($perPage)->withQueryString();

        return view('news.index', [
            'posts' => $posts,
            'latestPosts' => NewsCache::latestPosts(24),
            'trendingPosts' => NewsCache::trendingPosts(10),
            'popularPosts' => NewsCache::popularPosts(5),
            'homeSections' => NewsCache::homeSections(),
            'ads' => NewsCache::activeAds(),
        ]);
    }

    public function show(string $slug)
    {
        $post = Post::query()
            ->with(['categories:id,name,slug', 'user:id,name'])
            ->where('slug', $slug)
            ->where('status', 'Published')
            ->first();

        if (! $post) {
            $redirect = PostSlugRedirect::query()
                ->where('old_slug', $slug)
                ->with('post:id,slug,status')
                ->first();

            if ($redirect?->post && $redirect->post->status === 'Published') {
                return redirect()->route('news.show', $redirect->post->slug, 301);
            }

            abort(404);
        }

        RecordPostView::dispatch($post->id);

        $bacaJugaPosts = $post->relatedPosts()
            ->with(['categories:id,name,slug'])
            ->where('status', 'Published')
            ->limit(3)
            ->get();

        if ($bacaJugaPosts->count() < 3) {
            $needed = 3 - $bacaJugaPosts->count();
            $excludeIds = array_merge([$post->id], $bacaJugaPosts->pluck('id')->all());

            $randomPosts = Post::query()
                ->with(['categories:id,name,slug'])
                ->published()
                ->whereNotIn('id', $excludeIds)
                ->latest('published_at')
                ->limit($needed)
                ->get();

            $bacaJugaPosts = $bacaJugaPosts->concat($randomPosts);
        }

        $melewatkanPosts = Post::query()
            ->with(['categories:id,name,slug'])
            ->published()
            ->whereKeyNot($post->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $pilihanPosts = Post::query()
            ->with(['categories:id,name,slug'])
            ->published()
            ->whereKeyNot($post->id)
            ->inRandomOrder()
            ->limit(6)
            ->get();

        $terkiniPosts = Post::query()
            ->with(['categories:id,name,slug', 'user:id,name'])
            ->published()
            ->whereKeyNot($post->id)
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('news.show', [
            'post' => $post,
            'popularPosts' => NewsCache::popularPosts(5, 'news.popular_posts_sidebar'),
            'bacaJugaPosts' => $bacaJugaPosts,
            'melewatkanPosts' => $melewatkanPosts,
            'pilihanPosts' => $pilihanPosts,
            'terkiniPosts' => $terkiniPosts,
            'ads' => NewsCache::activeAds(),
        ]);
    }
}
