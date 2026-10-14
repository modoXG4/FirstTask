<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    /**
     * The locales supported by the application.
     *
     * @var array<int, string>
     */
    protected array $supported = ['ar', 'en'];

    /**
     * Update the authenticated user's preferred locale.
     */
    public function update(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, $this->supported, true), 404);

        $request->user()->update(['locale' => $locale]);

        return back();
    }
}
