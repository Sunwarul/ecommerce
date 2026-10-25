<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;

/**
 * In-app user manual. Chapters are Markdown files in resources/docs/{locale}/NN-slug.md; a chapter missing in
 * the user's language falls back to English. The whole manual is sent at once so search works offline-fast.
 */
class DocsController extends Controller
{
    public function __invoke(?string $section = null)
    {
        $locale = app()->getLocale();
        $chapters = cache()->remember("docs_{$locale}_".$this->version(), now()->addDay(), fn () => $this->chapters($locale));

        if ($section && ! collect($chapters)->contains('slug', $section)) {
            abort(404);
        }

        return Inertia::render('Docs/Index', [
            'chapters' => $chapters,
            'current' => $section ?? ($chapters[0]['slug'] ?? null),
        ]);
    }

    /** Changes whenever a docs file changes, so edits show without clearing the cache. */
    protected function version(): string
    {
        $stamp = 0;
        foreach (File::allFiles(resource_path('docs')) as $f) {
            $stamp = max($stamp, $f->getMTime());
        }

        return (string) $stamp;
    }

    protected function chapters(string $locale): array
    {
        $files = [];
        foreach (['en', $locale] as $lang) {
            $dir = resource_path("docs/{$lang}");
            if (! File::isDirectory($dir)) {
                continue;
            }
            foreach (File::files($dir) as $file) {
                if ($file->getExtension() === 'md') {
                    $files[$file->getFilename()] = $file->getPathname(); // the user's language overrides English
                }
            }
        }
        ksort($files, SORT_NATURAL);

        $chapters = [];
        foreach ($files as $filename => $path) {
            $markdown = File::get($path);
            $slug = preg_replace('/^\d+-/', '', pathinfo($filename, PATHINFO_FILENAME));
            $title = preg_match('/^#\s+(.+)$/m', $markdown, $m) ? trim($m[1]) : Str::headline($slug);
            $markdown = preg_replace('/^#\s+.+$/m', '', $markdown, 1); // the title is rendered by the page

            $html = Str::markdown($markdown, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $toc = [];
            $html = preg_replace_callback('#<h([23])>(.*?)</h\1>#s', function ($m) use (&$toc, $slug) {
                $text = trim(strip_tags($m[2]));
                $id = $slug.'-'.(Str::slug($text) ?: substr(md5($text), 0, 8));
                $toc[] = ['id' => $id, 'text' => $text, 'level' => (int) $m[1]];

                return "<h{$m[1]} id=\"{$id}\">{$m[2]}</h{$m[1]}>";
            }, $html);

            $chapters[] = [
                'slug' => $slug,
                'title' => $title,
                'html' => $html,
                'toc' => $toc,
                'text' => mb_strtolower(trim(preg_replace('/\s+/u', ' ', strip_tags($html)))),
            ];
        }

        return $chapters;
    }
}
