<?php

namespace App\Http\Controllers;

use App\Enums\PostType;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Post::query()->published()->orderByDesc('published_at');

        if ($request->filled('type') && PostType::tryFrom((string) $request->input('type'))) {
            $query->where('type', $request->input('type'));
        }

        $posts = $query->paginate(9)->withQueryString();

        return view('news.index', [
            'posts' => $posts,
            'types' => PostType::options(),
        ]);
    }

    public function show(Post $post): View
    {
        $isPublic = $post->status->value === 'published'
            && $post->published_at?->isPast()
            && ($post->unpublish_at === null || $post->unpublish_at->isFuture());

        abort_unless($isPublic, 404);

        $post->load(['areas', 'services', 'author']);

        $related = Post::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('news.show', [
            'post' => $post,
            'related' => $related,
        ]);
    }
}
