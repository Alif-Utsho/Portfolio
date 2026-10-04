<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePortfolioItemRequest;
use App\Models\MediaAsset;
use App\Models\PortfolioItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    public function index(string $type): View
    {
        $label = $this->typeLabel($type);

        return view('admin.content.index', [
            'type' => $type,
            'label' => $label,
            'items' => PortfolioItem::query()->where('type', $type)->orderBy('sort_order')->orderByDesc('updated_at')->paginate(15),
        ]);
    }

    public function create(string $type): View
    {
        return view('admin.content.form', [
            'type' => $type,
            'label' => $this->typeLabel($type),
            'item' => new PortfolioItem(['type' => $type, 'sort_order' => 0]),
            'media' => MediaAsset::query()->latest()->get(),
        ]);
    }

    public function store(StorePortfolioItemRequest $request, string $type): RedirectResponse
    {
        $data = $this->itemData($request, $type);
        PortfolioItem::query()->create($data);

        return to_route('admin.content.index', $type)->with('status', 'Content created.');
    }

    public function edit(PortfolioItem $item): View
    {
        return view('admin.content.form', [
            'type' => $item->type,
            'label' => $this->typeLabel($item->type),
            'item' => $item,
            'media' => MediaAsset::query()->latest()->get(),
        ]);
    }

    public function update(StorePortfolioItemRequest $request, PortfolioItem $item): RedirectResponse
    {
        $type = $item->type;
        $item->update($this->itemData($request, $type));

        return to_route('admin.content.index', $type)->with('status', 'Content updated.');
    }

    public function destroy(PortfolioItem $item): RedirectResponse
    {
        $type = $item->type;
        $item->delete();

        return to_route('admin.content.index', $type)->with('status', 'Content removed.');
    }

    /** @return array<string, mixed> */
    private function itemData(StorePortfolioItemRequest $request, string $type): array
    {
        $data = $request->safe()->except([
            'organization', 'period', 'group', 'challenge', 'features', 'category', 'technologies',
        ]);
        $data['type'] = $type;
        $data['technologies'] = collect(explode(',', (string) $request->validated('technologies')))
            ->map(fn (string $technology): string => trim($technology))
            ->filter()
            ->values()
            ->all();
        $data['metadata'] = array_filter([
            'organization' => $request->validated('organization'),
            'period' => $request->validated('period'),
            'group' => $request->validated('group'),
            'challenge' => $request->validated('challenge'),
            'features' => collect(preg_split('/\R/', (string) $request->validated('features')) ?: [])
                ->map(fn (string $feature): string => trim($feature))
                ->filter()
                ->values()
                ->all(),
            'category' => $request->validated('category'),
        ], fn (mixed $value): bool => $value !== null && $value !== [] && $value !== '');
        $data['is_published'] = $request->boolean('is_published');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($type === 'project' && blank($data['slug'] ?? null)) {
            $data['slug'] = $this->uniqueProjectSlug((string) $data['title']);
        }

        return Arr::only($data, [
            'type', 'title', 'slug', 'eyebrow', 'subtitle', 'summary', 'body', 'technologies',
            'metadata', 'url', 'secondary_url', 'image_path', 'is_published', 'is_featured', 'sort_order',
        ]);
    }

    private function uniqueProjectSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $suffix = 2;

        while (PortfolioItem::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function typeLabel(string $type): string
    {
        abort_unless(array_key_exists($type, config('portfolio.content_types')), 404);

        return config('portfolio.content_types.'.$type);
    }
}
