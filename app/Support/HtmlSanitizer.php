<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Allow-list sanitizer for rich text written in the admin editor (blog posts) before it is rendered on the
 * public website. Unknown tags are unwrapped (their text is kept), dangerous ones are removed with their
 * content, attributes are filtered and only http(s)/mailto/tel links and http(s)/storage images survive.
 */
class HtmlSanitizer
{
    private const ALLOWED = [
        'p' => [], 'br' => [], 'hr' => [], 'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [],
        'sub' => [], 'sup' => [], 'blockquote' => [], 'pre' => [], 'code' => [], 'span' => [],
        'h1' => [], 'h2' => [], 'h3' => [], 'h4' => [], 'h5' => [], 'h6' => [],
        'ul' => [], 'ol' => [], 'li' => ['data-list'],
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
        'table' => [], 'thead' => [], 'tbody' => [], 'tr' => [], 'th' => ['colspan', 'rowspan'], 'td' => ['colspan', 'rowspan'],
        'figure' => [], 'figcaption' => [],
        'iframe' => ['src', 'width', 'height', 'allowfullscreen', 'frameborder'],
    ];

    private const DROP_WITH_CONTENT = ['script', 'style', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'link', 'meta', 'svg', 'math', 'noscript', 'template'];

    /** Quill's own formatting classes are the only classes kept */
    private const CLASS_PATTERN = '/^ql-(align|indent|size|font|direction)-[a-z0-9-]+$|^ql-syntax$|^ql-video$/';

    public static function clean(?string $html): string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }

        $doc = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="__root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $doc->getElementById('__root');
        if (! $root) {
            return '';
        }
        self::walk($root);

        $out = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $out .= $doc->saveHTML($child);
        }

        return $out;
    }

    private static function walk(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE || $child->nodeType === XML_PI_NODE) {
                $node->removeChild($child);

                continue;
            }
            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);
            if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                $node->removeChild($child);

                continue;
            }

            self::walk($child);

            if (! array_key_exists($tag, self::ALLOWED)) {
                // unwrap: keep the (already cleaned) children
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);

                continue;
            }

            self::filterAttributes($child, $tag);

            if (($tag === 'img' || $tag === 'iframe') && ! $child->hasAttribute('src')) {
                $node->removeChild($child);
            }
        }
    }

    private static function filterAttributes(DOMElement $el, string $tag): void
    {
        $allowed = self::ALLOWED[$tag];
        foreach (iterator_to_array($el->attributes) as $attr) {
            $name = strtolower($attr->name);
            $value = trim($attr->value);

            if ($name === 'class') {
                $classes = array_filter(preg_split('/\s+/', $value), fn ($c) => preg_match(self::CLASS_PATTERN, $c));
                $classes ? $el->setAttribute('class', implode(' ', $classes)) : $el->removeAttribute('class');

                continue;
            }
            if (! in_array($name, $allowed, true)) {
                $el->removeAttribute($attr->name);

                continue;
            }
            if ($name === 'href' && ! self::safeUrl($value, ['http', 'https', 'mailto', 'tel'], true)) {
                $el->removeAttribute('href');
            }
            if ($name === 'src') {
                $ok = $tag === 'iframe' ? self::safeEmbed($value) : self::safeUrl($value, ['http', 'https'], true);
                if (! $ok) {
                    $el->removeAttribute('src');
                }
            }
        }

        if ($tag === 'a' && $el->getAttribute('target') === '_blank') {
            $el->setAttribute('rel', 'noopener noreferrer nofollow');
        } elseif ($tag === 'a') {
            $el->removeAttribute('target');
        }
    }

    private static function safeUrl(string $url, array $schemes, bool $allowRelative): bool
    {
        $url = preg_replace('/[\x00-\x20]+/', '', $url);
        if ($url === '') {
            return false;
        }
        if (preg_match('/^([a-z][a-z0-9+.-]*):/i', $url, $m)) {
            return in_array(strtolower($m[1]), $schemes, true);
        }

        // relative URLs such as /storage/blog/x.jpg; "//host" is protocol-relative and allowed like https
        return $allowRelative;
    }

    /** Only video embeds from YouTube / Vimeo and Google Maps may be framed inside a post. */
    private static function safeEmbed(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return $scheme === 'https' && in_array($host, [
            'www.youtube.com', 'youtube.com', 'www.youtube-nocookie.com', 'player.vimeo.com', 'www.google.com', 'maps.google.com',
        ], true);
    }
}
