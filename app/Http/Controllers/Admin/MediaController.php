<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Models\PortfolioItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(): View
    {
        return view('admin.media.index', ['media' => MediaAsset::query()->latest()->paginate(24)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);
        $path = $request->file('image')->store('portfolio', 'public');

        MediaAsset::query()->create([
            'path' => $path,
            'original_name' => basename($request->file('image')->getClientOriginalName()),
            'alt_text' => $validated['alt_text'] ?? null,
        ]);

        return back()->with('status', 'Image uploaded.');
    }

    public function destroy(MediaAsset $media): RedirectResponse
    {
        $usedBy = PortfolioItem::query()->where('image_path', $media->path);
        $affectedItems = (clone $usedBy)->count();

        // Detach the asset first so projects and personal sections fall back to their
        // built-in artwork instead of leaving broken image URLs behind.
        $usedBy->update(['image_path' => null]);

        Storage::disk('public')->delete($media->path);
        $media->delete();

        $message = $affectedItems > 0
            ? "Image removed. {$affectedItems} content item(s) now use the default artwork."
            : 'Image removed.';

        return back()->with('status', $message);
    }
}
