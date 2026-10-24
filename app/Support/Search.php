<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

/**
 * Forgiving search helpers.
 *
 *  - Every word must match (in any of the columns): "rahim 017" finds Rahim with a 017.. number.
 *  - Partial match by default; "*" = any characters, "?" = one character: "01*678", "ra?im".
 *  - Phone numbers are compared digits-only, so "01712-345678", "+880 1712 345678" and "1712345" all match.
 */
class Search
{
    public static function tokens(?string $term): array
    {
        $term = trim((string) $term);
        // A phone typed with spaces ("+880 1712 345678") is one token, not three words
        if (preg_match('/^[\d\s+\-()*?]+$/', $term) && preg_match('/\d/', $term)) {
            return [preg_replace('/\s+/', '', $term)];
        }

        return array_values(array_filter(preg_split('/\s+/', trim((string) $term)) ?: [], fn ($t) => $t !== ''));
    }

    public static function like(string $token): string
    {
        $escaped = addcslashes($token, '%_\\');

        return '%'.str_replace(['*', '?'], ['%', '_'], $escaped).'%';
    }

    /** Digits of a token that looks like (part of) a phone number, else null. */
    public static function phoneDigits(string $token): ?string
    {
        if (! preg_match('/^[\d\s+\-()*?]+$/', $token)) {
            return null;
        }
        $digits = preg_replace('/[^\d*?]/', '', $token);
        if (strlen(preg_replace('/\D/', '', $digits)) < 3) {
            return null;
        }
        // +8801712.. / 8801712.. => 01712..
        if (str_starts_with($digits, '880')) {
            $digits = substr($digits, 2);
        }

        return $digits;
    }

    public static function cleanPhoneSql(string $column): string
    {
        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE({$column}, ' ', ''), '-', ''), '+', ''), '(', ''), ')', '')";
    }

    /**
     * @param  array  $columns  text columns to match
     * @param  array  $phoneColumns  columns compared digits-only
     */
    public static function apply(Builder $query, ?string $term, array $columns, array $phoneColumns = []): Builder
    {
        foreach (self::tokens($term) as $token) {
            $query->where(function (Builder $q) use ($token, $columns, $phoneColumns) {
                $like = self::like($token);
                foreach ($columns as $col) {
                    $q->orWhere($col, 'like', $like);
                }
                if ($digits = self::phoneDigits($token)) {
                    foreach ($phoneColumns as $col) {
                        $q->orWhereRaw(self::cleanPhoneSql($q->getModel()->qualifyColumn($col)).' like ?', [self::like($digits)]);
                    }
                }
            });
        }

        return $query;
    }

    /** Customer name / phone / email / whatsapp search. */
    public static function customers(Builder $query, ?string $term): Builder
    {
        return self::apply($query, $term, ['name', 'email', 'phone', 'whatsapp_number'], ['phone', 'whatsapp_number']);
    }
}
