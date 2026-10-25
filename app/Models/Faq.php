<?php

namespace App\Models;

class Faq extends BaseModel
{
    protected $fillable = ['question', 'answer', 'locale', 'sort_order', 'is_active'];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /** Active FAQs for a language, plus those written for every language (locale = null). */
    public function scopeVisibleFor($query, string $locale)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('locale')->orWhere('locale', $locale))
            ->orderBy('sort_order')->orderBy('id');
    }
}
