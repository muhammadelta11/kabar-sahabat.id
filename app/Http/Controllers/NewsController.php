<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostView;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('categories')
            ->where('status', 'Published')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc');

        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->q.'%')
                    ->orWhere('content', 'like', '%'.$request->q.'%');
            });
        }

        $posts = $query->paginate(15)->withQueryString();

        $latestPosts = Post::with('categories')
            ->where('status', 'Published')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(12)
            ->get();

        $trendingPosts = Post::with('categories')
            ->where('status', 'Published')
            ->where('published_at', '<=', now())
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        $popularPosts = Post::with('categories')
            ->where('status', 'Published')
            ->where('published_at', '<=', now())
            ->orderBy('views_count', 'desc')
            ->limit(7)
            ->get();

        // Seksi beranda: kategori yang dicentang "Tampilkan di Beranda" oleh admin
        $homeSections = Category::query()
            ->where('show_on_homepage', true)
            ->orderBy('name')
            ->get()
            ->map(function (Category $category) {
                $category->homepagePosts = $category->posts()
                    ->where('status', 'Published')
                    ->where('published_at', '<=', now())
                    ->orderByDesc('published_at')
                    ->limit(4)
                    ->get();

                return $category;
            })
            ->filter(fn (Category $category) => $category->homepagePosts->isNotEmpty())
            ->values();

        $ads = Advertisement::where('is_active', true)->get()->keyBy('slot_key');

        return view('news.index', compact(
            'posts',
            'latestPosts',
            'trendingPosts',
            'popularPosts',
            'homeSections',
            'ads'
        ));
    }

    public function show($slug)
    {
        $post = Post::with('categories', 'user')
            ->where('slug', $slug)
            ->where('status', 'Published')
            ->firstOrFail();

        $post->increment('views_count');
        PostView::create(['post_id' => $post->id, 'viewed_at' => now()]);

        // 1. Terpopuler (Sidebar)
        $popularPosts = Post::with('categories')
            ->where('status', 'Published')
            ->where('published_at', '<=', now())
            ->orderBy('views_count', 'desc')
            ->limit(5)
            ->get();

        // 2. Baca juga items (Prioritize admin-selected related posts)
        $bacaJugaPosts = $post->relatedPosts()->where('status', 'Published')->limit(2)->get();

        if ($bacaJugaPosts->count() < 2) {
            $needed = 2 - $bacaJugaPosts->count();
            $excludeIds = array_merge([$post->id], $bacaJugaPosts->pluck('id')->toArray());

            $randomPosts = Post::with('categories')
                ->whereNotIn('id', $excludeIds)
                ->where('status', 'Published')
                ->inRandomOrder()
                ->limit($needed)
                ->get();

            $bacaJugaPosts = $bacaJugaPosts->concat($randomPosts);
        }

        // 3. Mungkin Anda Melewatkan Ini (4 posts)
        $melewatkanPosts = Post::with('categories')
            ->where('id', '!=', $post->id)
            ->where('status', 'Published')
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // 4. Berita Pilihan Untukmu (6 posts)
        $pilihanPosts = Post::with('categories')
            ->where('id', '!=', $post->id)
            ->where('status', 'Published')
            ->inRandomOrder()
            ->limit(6)
            ->get();

        // 5. Terkini Lainnya (Paginated, 10 per page)
        $terkiniPosts = Post::with('categories')
            ->where('id', '!=', $post->id)
            ->where('status', 'Published')
            ->orderBy('published_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $ads = Advertisement::where('is_active', true)->get()->keyBy('slot_key');

        return view('news.show', compact(
            'post',
            'popularPosts',
            'bacaJugaPosts',
            'melewatkanPosts',
            'pilihanPosts',
            'terkiniPosts',
            'ads'
        ));
    }
}
