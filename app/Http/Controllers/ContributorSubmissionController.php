<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContributorSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $submissions = Post::query()
            ->where('user_id', $request->user()->id)
            ->latest('updated_at')
            ->paginate(10);

        return view('submissions.index', [
            'submissions' => $submissions,
        ]);
    }
}
