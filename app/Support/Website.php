<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Public website content stored in the settings table under "website_*" keys.
 * Text fields are translatable and saved as JSON ({"en": "...", "bn": "..."}); the visitor's language is
 * used first, then English, then the built-in translation on the page.
 */
class Website
{
    public const TRANSLATABLE = [
        'hero_badge',
        'hero_title',
        'hero_subtitle',
        'hero_cta_label',
        'about_title',
        'about_body',
        'address',
        'opening_hours',
        'footer_about',
        'meta_description',
    ];

    public const PLAIN = [
        'enabled',
        'hero_cta_url',
        'contact_email',
        'contact_phone',
        'whatsapp',
        'map_location',
        'notify_email',
        'social_facebook',
        'social_instagram',
        'social_youtube',
        'social_linkedin',
        'social_tiktok',
        'show_features',
        'show_about',
        'show_blog',
        'show_faq',
        'show_map',
        'show_contact',
    ];

    public const TOGGLES = ['enabled', 'show_features', 'show_about', 'show_blog', 'show_faq', 'show_map', 'show_contact'];

    public const IMAGES = ['hero_image', 'about_image'];

    public static function key(string $field): string
    {
        return "website_{$field}";
    }

    /** Raw values for the admin form: translatable fields as {locale: text}. */
    public static function raw(): array
    {
        $data = [];
        foreach (self::TRANSLATABLE as $f) {
            $decoded = json_decode((string) Setting::get(self::key($f)), true);
            $data[$f] = is_array($decoded) ? $decoded : [];
        }
        foreach (self::PLAIN as $f) {
            $data[$f] = Setting::get(self::key($f));
        }
        foreach (self::TOGGLES as $f) {
            // every section is on until switched off
            $data[$f] = $data[$f] === null ? true : filter_var($data[$f], FILTER_VALIDATE_BOOLEAN);
        }
        foreach (self::IMAGES as $f) {
            $data[$f] = Setting::get(self::key($f));
        }

        return $data;
    }

    /** Resolved values for one visitor language. Empty strings mean "use the page's default text". */
    public static function forLocale(string $locale): array
    {
        $raw = self::raw();
        foreach (self::TRANSLATABLE as $f) {
            $raw[$f] = trim((string) ($raw[$f][$locale] ?? '')) ?: trim((string) ($raw[$f]['en'] ?? ''));
        }
        foreach (self::IMAGES as $f) {
            $raw[$f] = $raw[$f] ? asset('storage/'.$raw[$f]) : null;
        }
        $raw['map_embed_url'] = self::mapEmbedUrl($raw['map_location']);
        unset($raw['notify_email']);

        $raw['socials'] = collect(['facebook', 'instagram', 'youtube', 'linkedin', 'tiktok'])
            ->mapWithKeys(fn ($s) => [$s => $raw["social_{$s}"]])
            ->filter()
            ->all();

        return $raw;
    }

    public static function set(array $values): void
    {
        foreach (self::TRANSLATABLE as $f) {
            if (array_key_exists($f, $values)) {
                $clean = array_filter(array_map(fn ($v) => is_string($v) ? trim($v) : null, (array) $values[$f]), fn ($v) => $v !== null && $v !== '');
                Setting::set(self::key($f), $clean ? json_encode($clean, JSON_UNESCAPED_UNICODE) : null);
            }
        }
        foreach (self::PLAIN as $f) {
            if (array_key_exists($f, $values)) {
                $v = $values[$f];
                Setting::set(self::key($f), is_bool($v) ? ($v ? '1' : '0') : $v);
            }
        }
    }

    /**
     * Turn whatever the admin pasted into a safe Google Maps embed URL:
     *  - the full <iframe ...> code from Google Maps "Share > Embed a map"
     *  - an https://www.google.com/maps/embed?... URL
     *  - an address, place name or "23.7808,90.2792" coordinates (embedded without an API key)
     * Returns null when nothing usable was given (e.g. a maps.app.goo.gl short link, which cannot be embedded).
     */
    public static function mapEmbedUrl(?string $input): ?string
    {
        $input = trim((string) $input);
        if ($input === '') {
            return null;
        }

        if (stripos($input, '<iframe') !== false) {
            if (! preg_match('/\ssrc\s*=\s*["\']([^"\']+)["\']/i', $input, $m)) {
                return null;
            }
            $input = html_entity_decode($m[1]);
        }

        if (preg_match('#^https?://#i', $input)) {
            $parts = parse_url($input);
            $host = strtolower($parts['host'] ?? '');
            $path = $parts['path'] ?? '';
            $isGoogle = in_array($host, ['www.google.com', 'google.com', 'maps.google.com'], true)
                || preg_match('/^(www\.)?google\.[a-z.]{2,6}$/', $host);
            if ($isGoogle && str_starts_with($path, '/maps/embed')) {
                return 'https://www.google.com'.$path.(isset($parts['query']) ? '?'.$parts['query'] : '');
            }
            if ($isGoogle && str_starts_with($path, '/maps')) {
                // a normal Google Maps link: use its "@lat,lng" or place name as a search query
                if (preg_match('#@(-?\d+\.\d+),(-?\d+\.\d+)#', $path, $m)) {
                    return self::queryEmbed("{$m[1]},{$m[2]}");
                }
                if (preg_match('#/place/([^/]+)#', $path, $m)) {
                    return self::queryEmbed(urldecode(str_replace('+', ' ', $m[1])));
                }
                parse_str($parts['query'] ?? '', $q);
                if (! empty($q['q'])) {
                    return self::queryEmbed($q['q']);
                }
            }

            return null;
        }

        return self::queryEmbed($input);
    }

    private static function queryEmbed(string $query): string
    {
        return 'https://maps.google.com/maps?q='.rawurlencode(mb_substr($query, 0, 300)).'&z=15&output=embed';
    }
}
