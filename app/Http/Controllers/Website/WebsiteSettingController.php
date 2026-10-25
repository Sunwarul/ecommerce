<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class WebsiteSettingController extends Controller
{
    /** Branding images live in the settings table too, but they are used by the whole app, not only the website. */
    private const BRANDING = ['logo' => 'general_logo', 'favicon' => 'general_favicon'];

    public function index()
    {
        $branding = [];
        foreach (self::BRANDING as $field => $key) {
            $path = Setting::get($key);
            $branding[$field] = $path ? asset('storage/'.$path) : null;
        }

        $website = Website::raw();
        foreach (Website::IMAGES as $f) {
            $website[$f] = $website[$f] ? asset('storage/'.$website[$f]) : null;
        }

        return Inertia::render('Admin/Website/Settings', [
            'website' => $website,
            'branding' => $branding,
            'map_preview' => Website::mapEmbedUrl($website['map_location']),
        ]);
    }

    public function update(Request $request)
    {
        $text = fn ($max) => ['nullable', 'string', "max:{$max}"];
        $rules = [
            'hero_badge.*' => $text(60),
            'hero_title.*' => $text(160),
            'hero_subtitle.*' => $text(500),
            'hero_cta_label.*' => $text(40),
            'about_title.*' => $text(160),
            'about_body.*' => $text(3000),
            'address.*' => $text(255),
            'opening_hours.*' => $text(255),
            'footer_about.*' => $text(500),
            'meta_description.*' => $text(300),
            'hero_cta_url' => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/|\/|#)/i'],
            'contact_email' => ['nullable', 'email', 'max:191'],
            'notify_email' => ['nullable', 'email', 'max:191'],
            'contact_phone' => $text(40),
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9 \-]{6,20}$/'],
            'map_location' => ['nullable', 'string', 'max:3000', function ($attr, $value, $fail) {
                if (filled($value) && ! Website::mapEmbedUrl($value)) {
                    $fail(__('Use the Google Maps "Share > Embed a map" code, a google.com/maps link, an address or coordinates. Short maps.app.goo.gl links cannot be embedded.'));
                }
            }],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico,svg', 'max:512'],
            'hero_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'about_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'remove' => ['nullable', 'array'],
            'remove.*' => ['string', 'in:logo,favicon,hero_image,about_image'],
        ];
        foreach (['facebook', 'instagram', 'youtube', 'linkedin', 'tiktok'] as $s) {
            $rules["social_{$s}"] = ['nullable', 'url:https,http', 'max:255'];
        }
        foreach (Website::TOGGLES as $f) {
            $rules[$f] = ['nullable', 'boolean'];
        }
        foreach (Website::TRANSLATABLE as $f) {
            $rules[$f] = ['nullable', 'array'];
        }
        $data = $request->validate($rules);

        foreach (Website::TOGGLES as $f) {
            $data[$f] = $request->boolean($f);
        }
        Website::set($data);

        // images: replace on upload, delete on "remove"
        $images = self::BRANDING + array_combine(Website::IMAGES, array_map([Website::class, 'key'], Website::IMAGES));
        foreach ($images as $field => $key) {
            $remove = in_array($field, $data['remove'] ?? [], true);
            if ($request->hasFile($field) || $remove) {
                if ($old = Setting::get($key)) {
                    Storage::disk('public')->delete($old);
                }
                Setting::set($key, $request->hasFile($field) ? $request->file($field)->store('branding', 'public') : null);
            }
        }

        return back()->with('success', __('Website settings saved.'));
    }
}
