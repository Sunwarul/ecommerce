<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Selectable interface fonts. Web fonts are served by fonts.bunny.net (a privacy-friendly Google Fonts mirror);
 * "system" entries download nothing and use fonts already installed on the computer (common in Bangladesh).
 */
class Fonts
{
    public const BENGALI = [
        'noto-sans-bengali' => ['label' => 'Noto Sans Bengali', 'family' => "'Noto Sans Bengali'", 'weights' => '400,500,600,700'],
        'hind-siliguri' => ['label' => 'Hind Siliguri', 'family' => "'Hind Siliguri'", 'weights' => '400,500,600,700'],
        'baloo-da-2' => ['label' => 'Baloo Da 2', 'family' => "'Baloo Da 2'", 'weights' => '400,500,600,700'],
        'anek-bangla' => ['label' => 'Anek Bangla', 'family' => "'Anek Bangla'", 'weights' => '400,500,600,700'],
        'tiro-bangla' => ['label' => 'Tiro Bangla', 'family' => "'Tiro Bangla'", 'weights' => '400'],
        'noto-serif-bengali' => ['label' => 'Noto Serif Bengali', 'family' => "'Noto Serif Bengali'", 'weights' => '400,500,600,700'],
        'mina' => ['label' => 'Mina', 'family' => "'Mina'", 'weights' => '400,700'],
        'system-bn' => ['label' => 'SolaimanLipi / Kalpurush / Nikosh (installed on this PC)', 'family' => "'SolaimanLipi', 'Kalpurush', 'Nikosh', 'Siyam Rupali', 'Noto Sans Bengali', 'Vrinda'", 'weights' => null],
    ];

    public const LATIN = [
        'figtree' => ['label' => 'Figtree', 'family' => "'Figtree'", 'weights' => '400,500,600,700'],
        'inter' => ['label' => 'Inter', 'family' => "'Inter'", 'weights' => '400,500,600,700'],
        'poppins' => ['label' => 'Poppins', 'family' => "'Poppins'", 'weights' => '400,500,600,700'],
        'roboto' => ['label' => 'Roboto', 'family' => "'Roboto'", 'weights' => '400,500,700'],
        'open-sans' => ['label' => 'Open Sans', 'family' => "'Open Sans'", 'weights' => '400,500,600,700'],
        'nunito' => ['label' => 'Nunito', 'family' => "'Nunito'", 'weights' => '400,500,600,700'],
        'system' => ['label' => 'System default', 'family' => 'system-ui, -apple-system, \'Segoe UI\'', 'weights' => null],
    ];

    public const DEFAULT_BENGALI = 'noto-sans-bengali';

    public const DEFAULT_LATIN = 'figtree';

    public static function bengali(): string
    {
        $key = Setting::get('appearance_font_bn');

        return isset(self::BENGALI[$key]) ? $key : self::DEFAULT_BENGALI;
    }

    public static function latin(): string
    {
        $key = Setting::get('appearance_font_latin');

        return isset(self::LATIN[$key]) ? $key : self::DEFAULT_LATIN;
    }

    /** Bangla glyphs look small next to Latin ones; this scales the whole UI while Bangla is active. */
    public static function bengaliScale(): int
    {
        return max(90, min(130, (int) (Setting::get('appearance_bn_font_scale') ?: 100)));
    }

    /** One stylesheet URL for every web font in use (selected fonts + Arabic/Devanagari for those locales). */
    public static function stylesheetUrl(): string
    {
        $families = [];
        foreach ([self::LATIN[self::latin()], self::BENGALI[self::bengali()]] as $i => $font) {
            if ($font['weights']) {
                $slug = $i === 0 ? self::latin() : self::bengali();
                $families[] = "{$slug}:{$font['weights']}";
            }
        }
        // the system Bangla stack still needs Noto as a fallback for PCs without those fonts
        if (! self::BENGALI[self::bengali()]['weights']) {
            $families[] = 'noto-sans-bengali:400,500,600,700';
        }
        $families[] = 'noto-sans-arabic:400,500,600,700';
        $families[] = 'noto-sans-devanagari:400,500,600,700';

        return 'https://fonts.bunny.net/css?family='.implode('|', array_unique($families)).'&display=swap';
    }

    /** CSS custom properties consumed by app.scss / tailwind (font stacks and the Bangla scale). */
    public static function cssVariables(): string
    {
        $scale = self::bengaliScale();

        return ':root{--font-latin:'.self::LATIN[self::latin()]['family'].';--font-bengali:'.self::BENGALI[self::bengali()]['family'].';--bn-font-scale:'.($scale / 100).';}';
    }

    /** Options for the settings page: [{value, label, family}] */
    public static function options(array $catalog): array
    {
        return collect($catalog)->map(fn ($f, $k) => ['value' => $k, 'label' => $f['label'], 'family' => $f['family'], 'webfont' => (bool) $f['weights']])->values()->all();
    }

    /** Stylesheet loading every selectable web font, used only for the live preview on the settings page. */
    public static function previewStylesheetUrl(): string
    {
        $families = [];
        foreach ([self::BENGALI, self::LATIN] as $catalog) {
            foreach ($catalog as $slug => $f) {
                if ($f['weights']) {
                    $families[] = "{$slug}:400,600";
                }
            }
        }

        return 'https://fonts.bunny.net/css?family='.implode('|', $families).'&display=swap';
    }
}
