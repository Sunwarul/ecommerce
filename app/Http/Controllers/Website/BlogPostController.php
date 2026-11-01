<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Support\HtmlSanitizer;
use App\Support\Search;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $posts = BlogPost::query()
            ->with('author:id,name')
            ->when($request->status, fn ($q, $s) => $s === 'trashed' ? $q->onlyTrashed() : $q->where('status', $s))
            ->when($request->search, fn ($q, $term) => Search::apply($q, $term, ['title', 'excerpt']))
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Website/Posts/Index', [
            'posts' => $posts,
            'filters' => $request->only('search', 'status'),
            'counts' => [
                'all' => BlogPost::count(),
                'published' => BlogPost::where('status', 'published')->count(),
                'draft' => BlogPost::where('status', 'draft')->count(),
                'trashed' => BlogPost::onlyTrashed()->count(),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Website/Posts/Form', ['post' => null]);
    }

    public function edit(BlogPost $post)
    {
        return Inertia::render('Admin/Website/Posts/Form', ['post' => $post]);
    }

    public function store(Request $request)
    {
        $post = new BlogPost(['author_id' => $request->user()->id]);
        $this->fill($post, $request);

        return redirect()->route('website.posts.edit', $post)->with('success', __('Post saved.'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $this->fill($post, $request);

        return back()->with('success', __('Post saved.'));
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();

        return redirect()->route('website.posts.index')->with('success', __('Post moved to trash.'));
    }

    public function restore(int $id)
    {
        BlogPost::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', __('Post restored.'));
    }

    public function forceDelete(int $id)
    {
        $post = BlogPost::onlyTrashed()->findOrFail($id);
        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }
        $post->forceDelete();

        return back()->with('success', __('Post deleted permanently.'));
    }

    protected function fill(BlogPost $post, Request $request): void
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:191'],
            'slug' => ['nullable', 'string', 'max:191', 'regex:/^[\p{L}\p{M}\p{N}-]+$/u'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:4000000'],
            'locale' => ['nullable', 'in:'.implode(',', config('app.supported_locales'))],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:191'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'cover' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'remove_cover' => ['nullable', 'boolean'],
        ]);

        $post->fill(collect($data)->except(['cover', 'remove_cover', 'slug'])->all());
        $post->body = HtmlSanitizer::clean($data['body'] ?? '');
        $post->slug = BlogPost::makeSlug(($data['slug'] ?? null) ?: $data['title'], $post->id);
        if ($post->status === 'published' && ! $post->published_at) {
            $post->published_at = now();
        }

        if ($request->hasFile('cover') || $request->boolean('remove_cover')) {
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
            $post->cover_image = $request->hasFile('cover') ? $request->file('cover')->store('blog', 'public') : null;
        }

        $post->save();
    }
}
