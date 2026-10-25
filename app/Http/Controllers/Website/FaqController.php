<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaqController extends Controller
{
    protected function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'locale' => ['nullable', 'in:'.implode(',', config('app.supported_locales'))],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['boolean'],
        ];
    }

    public function index()
    {
        return Inertia::render('Admin/Website/Faqs', [
            'faqs' => Faq::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        Faq::create($data + ['sort_order' => (int) Faq::max('sort_order') + 1]);

        return back()->with('success', __('FAQ added.'));
    }

    public function update(Request $request, Faq $faq)
    {
        $faq->update($request->validate($this->rules()));

        return back()->with('success', __('FAQ updated.'));
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return back()->with('success', __('FAQ deleted.'));
    }

    /** Persist a new order after drag & drop: ids in their new order. */
    public function reorder(Request $request)
    {
        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']])['ids'];
        foreach (array_values($ids) as $i => $id) {
            Faq::whereKey($id)->update(['sort_order' => $i]);
        }

        return back();
    }
}
