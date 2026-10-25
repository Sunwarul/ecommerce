<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Setting;
use App\Support\Website;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class WebsiteController extends Controller
{
    /** Seconds a real visitor needs at least to fill the form; faster submits are bots. */
    private const MIN_FILL_SECONDS = 3;

    public function home()
    {
        $site = $this->site();
        if (! $site['enabled']) {
            return redirect()->route(auth()->check() ? 'dashboard' : 'login');
        }
        $locale = app()->getLocale();

        return Inertia::render('Frontend/Welcome', [
            'site' => $site,
            'posts' => $site['show_blog'] ? $this->posts()->limit(3)->get() : [],
            'faqs' => $site['show_faq'] ? Faq::visibleFor($locale)->limit(6)->get(['id', 'question', 'answer']) : [],
            'form_token' => $this->formToken(),
        ]);
    }

    public function blog(Request $request)
    {
        $site = $this->enabledSite('show_blog');

        return Inertia::render('Frontend/Blog/Index', [
            'site' => $site,
            'posts' => $this->posts()
                ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w->where('title', 'like', '%'.addcslashes($s, '%_\\').'%')->orWhere('excerpt', 'like', '%'.addcslashes($s, '%_\\').'%')))
                ->paginate(9)->withQueryString(),
            'filters' => $request->only('search'),
        ]);
    }

    public function post(string $slug)
    {
        $site = $this->enabledSite('show_blog');
        $post = BlogPost::published()->with('author:id,name')->where('slug', $slug)->firstOrFail();
        // one view per visitor session
        if (! session()->has("viewed_post_{$post->id}")) {
            $post->increment('views');
            session()->put("viewed_post_{$post->id}", true);
        }

        return Inertia::render('Frontend/Blog/Show', [
            'site' => $site,
            'post' => $post,
            'related' => $this->posts()->where('id', '!=', $post->id)->limit(3)->get(),
        ]);
    }

    public function faq()
    {
        $site = $this->enabledSite('show_faq');

        return Inertia::render('Frontend/Faq', [
            'site' => $site,
            'faqs' => Faq::visibleFor(app()->getLocale())->get(['id', 'question', 'answer']),
        ]);
    }

    public function contact()
    {
        return Inertia::render('Frontend/Contact', [
            'site' => $this->enabledSite('show_contact'),
            'form_token' => $this->formToken(),
        ]);
    }

    public function sendContact(Request $request)
    {
        $this->enabledSite('show_contact');

        // Honeypot + timing trap: bots get a normal-looking success and nothing is stored.
        if (filled($request->input('website')) || ! $this->humanTiming($request->input('form_token'))) {
            Log::info('Contact form submission dropped as spam', ['ip' => $request->ip()]);

            return back()->with('success', __('Thank you! Your message has been sent. We will get back to you soon.'));
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['nullable', 'required_without:phone', 'email:rfc', 'max:191'],
            'phone' => ['nullable', 'required_without:email', 'string', 'max:40', 'regex:/^\+?[0-9 ()\-]{6,20}$/'],
            'subject' => ['nullable', 'string', 'max:191'],
            'message' => ['required', 'string', 'min:10', 'max:5000', function ($attr, $value, $fail) {
                if (preg_match_all('#https?://|www\.#i', $value) > 3) {
                    $fail(__('Please remove some links from your message.'));
                }
            }],
        ]);

        $message = ContactMessage::create($data + [
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
            'locale' => app()->getLocale(),
        ]);

        $this->notifyAdmin($message);

        return back()->with('success', __('Thank you! Your message has been sent. We will get back to you soon.'));
    }

    public function sitemap()
    {
        $site = $this->site();
        $urls = [];
        if ($site['enabled']) {
            $urls[] = ['loc' => route('welcome'), 'lastmod' => null];
            foreach (['show_blog' => 'website.blog', 'show_faq' => 'website.faq', 'show_contact' => 'website.contact'] as $section => $name) {
                if ($site[$section]) {
                    $urls[] = ['loc' => route($name), 'lastmod' => null];
                }
            }
            if ($site['show_blog']) {
                foreach (BlogPost::published()->latest('published_at')->limit(1000)->get(['slug', 'updated_at']) as $p) {
                    $urls[] = ['loc' => route('website.post', $p->slug), 'lastmod' => $p->updated_at->toAtomString()];
                }
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $u) {
            $xml .= '<url><loc>'.e($u['loc']).'</loc>'.($u['lastmod'] ? '<lastmod>'.$u['lastmod'].'</lastmod>' : '').'</url>';
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    protected function site(): array
    {
        return Website::forLocale(app()->getLocale());
    }

    /** The site (and the given section, e.g. "show_blog") must be switched on in Website settings. */
    protected function enabledSite(?string $section = null): array
    {
        $site = $this->site();
        abort_unless($site['enabled'] && (! $section || $site[$section]), 404);

        return $site;
    }

    protected function posts()
    {
        $locale = app()->getLocale();

        return BlogPost::published()
            ->where(fn ($q) => $q->whereNull('locale')->orWhere('locale', $locale))
            ->with('author:id,name')
            ->latest('published_at')
            ->select(['id', 'title', 'slug', 'excerpt', 'cover_image', 'published_at', 'author_id', 'views']);
    }

    protected function formToken(): string
    {
        return encrypt(now()->timestamp);
    }

    protected function humanTiming(?string $token): bool
    {
        try {
            $issued = (int) decrypt((string) $token);
        } catch (DecryptException) {
            return false;
        }

        return now()->timestamp - $issued >= self::MIN_FILL_SECONDS;
    }

    protected function notifyAdmin(ContactMessage $message): void
    {
        $to = Setting::get('website_notify_email') ?: Setting::get('website_contact_email');
        if (! $to || ! Setting::get('mail_host')) {
            return;
        }
        $shop = Setting::get('general_application_name') ?: config('app.name');

        dispatch(function () use ($message, $to, $shop) {
            try {
                Mail::raw(
                    "New message from the {$shop} website\n\n"
                    ."Name: {$message->name}\nEmail: {$message->email}\nPhone: {$message->phone}\nSubject: {$message->subject}\n\n"
                    ."{$message->message}\n\n".route('contact-messages.index'),
                    function ($m) use ($message, $to, $shop) {
                        $m->to($to)->subject("[{$shop}] ".($message->subject ?: 'New contact message'));
                        if ($message->email) {
                            $m->replyTo($message->email, $message->name);
                        }
                    }
                );
            } catch (\Throwable $e) {
                Log::warning('Contact notification email failed: '.$e->getMessage());
            }
        })->afterResponse();
    }
}
