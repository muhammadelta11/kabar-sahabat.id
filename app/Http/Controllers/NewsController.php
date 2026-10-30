<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostView;
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

        $posts = $query->paginate(15)->withQueryString();

        return view('news.index', [
            'posts' => $posts,
            'latestPosts' => NewsCache::latestPosts(12),
            'trendingPosts' => NewsCache::trendingPosts(10),
            'popularPosts' => NewsCache::popularPosts(7),
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
            ->firstOrFail();

        // Write ringan: 1 increment + 1 insert (diindeks). Untuk traffic sangat tinggi
        // bisa diganti Redis counter + agregasi berkala.
        Post::query()->whereKey($post->id)->increment('views_count');
        PostView::query()->create([
            'post_id' => $post->id,
            'viewed_at' => now(),
        ]);

        $bacaJugaPosts = $post->relatedPosts()
            ->with(['categories:id,name,slug'])
            ->where('status', 'Published')
            ->limit(2)
            ->get();

        if ($bacaJugaPosts->count() < 2) {
            $needed = 2 - $bacaJugaPosts->count();
            $excludeIds = array_merge([$post->id], $bacaJugaPosts->pluck('id')->all());

            $randomPosts = Post::query()
                ->with(['categories:id,name,slug'])
                ->published()
                ->whereNotIn('id', $excludeIds)
                ->inRandomOrder()
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
