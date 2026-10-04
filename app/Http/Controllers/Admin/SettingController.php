<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => PortfolioSetting::query()->where('key', 'site')->first()?->value ?? [],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_title' => ['required', 'string', 'max:180'],
            'site_description' => ['required', 'string', 'max:320'],
            'hero_title' => ['required', 'string', 'max:180'],
            'hero_intro' => ['required', 'string', 'max:1000'],
            'about_lead' => ['required', 'string', 'max:500'],
            'about_body' => ['required', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:180'],
            'cv_url' => ['nullable', 'url', 'max:2048'],
            'github_url' => ['nullable', 'url', 'max:2048'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:180'],
            'seo_description' => ['nullable', 'string', 'max:320'],
        ]);

        PortfolioSetting::query()->updateOrCreate(['key' => 'site'], ['value' => $validated]);

        return back()->with('status', 'Site settings saved.');
    }
}
