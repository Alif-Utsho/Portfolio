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
        abort_if(PortfolioItem::query()->where('image_path', $media->path)->exists(), 409, 'This image is currently used by published or draft content.');

        Storage::disk('public')->delete($media->path);
        $media->delete();

        return back()->with('status', 'Image removed.');
    }
}
