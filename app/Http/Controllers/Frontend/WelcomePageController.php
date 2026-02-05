<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WelcomePageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return Inertia::render('Frontend/Welcome', [
            'app_name' => config('app.name'),
            'general_address' => Setting::get('general_address'),
        ]);
    }
}
