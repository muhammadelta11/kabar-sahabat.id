<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;

class SearchNavbar extends Component
{
    public string $q = '';

    public function mount()
    {
        $this->q = request('q', '');
    }

    public function render()
    {
        $suggestions = collect();

        if (strlen(trim($this->q)) >= 2) {
            $suggestions = Post::with('categories')
                ->where('status', 'Published')
                ->where('title', 'like', '%'.$this->q.'%')
                ->orderBy('published_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('livewire.search-navbar', [
            'suggestions' => $suggestions,
        ]);
    }
}
