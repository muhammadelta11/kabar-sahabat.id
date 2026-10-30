<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;

class PostComments extends Component
{
    public Post $post;

    public string $guestName = '';

    public string $content = '';

    protected $rules = [
        'guestName' => 'required|min:2|max:50',
        'content' => 'required|min:3|max:1000',
    ];

    protected $messages = [
        'guestName.required' => 'Nama harus diisi.',
        'guestName.min' => 'Nama minimal 2 karakter.',
        'guestName.max' => 'Nama maksimal 50 karakter.',
        'content.required' => 'Komentar harus diisi.',
        'content.min' => 'Komentar minimal 3 karakter.',
        'content.max' => 'Komentar maksimal 1000 karakter.',
    ];

    public function mount(Post $post)
    {
        $this->post = $post;
    }

    public function postComment()
    {
        $this->validate();

        $this->post->comments()->create([
            'guest_name' => $this->guestName,
            'content' => $this->content,
            'is_approved' => true, // default to approved for mockup simplicity, can be toggled in admin panel
        ]);

        $this->content = '';

        session()->flash('message', 'Komentar berhasil dikirim!');
    }

    public function render()
    {
        $comments = $this->post->comments()
            ->where('is_approved', true)
            ->get();

        return view('livewire.post-comments', [
            'comments' => $comments,
        ]);
    }
}
