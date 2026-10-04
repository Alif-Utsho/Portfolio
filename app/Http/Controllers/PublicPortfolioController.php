<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Models\PortfolioItem;
use App\Models\PortfolioSetting;
use App\Services\PortfolioAnalytics;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;

class PublicPortfolioController extends Controller
{
    public function home(): View
    {
        $items = PortfolioItem::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('type');
        $projects = $items->get('project') ?? collect();
        $featuredProjects = $projects->where('is_featured', true)->take(3);

        return view('portfolio.home', [
            'settings' => $this->settings(),
            'items' => $items,
            'featuredProjects' => $featuredProjects->isNotEmpty() ? $featuredProjects : $projects->take(3),
            'navigationLinks' => $items->get('navigation') ?? collect(),
            'socialLinks' => $items->get('social') ?? collect(),
            'experiences' => $items->get('experience') ?? collect(),
            'skills' => ($items->get('skill') ?? collect())->groupBy(fn (PortfolioItem $item): string => $item->metadata['group'] ?? $item->subtitle ?? 'Skills')->map(function ($group, $name) {
                return (object) [
                    'title' => $name,
                    'summary' => $group->pluck('title')->join(' · '),
                    'technologies' => $group->pluck('title')->all(),
                ];
            })->values(),
            'personalItems' => $items->get('personal') ?? collect(),
        ]);
    }

    public function projects(): View
    {
        $projects = PortfolioItem::query()
            ->where('type', 'project')
            ->where('is_published', true)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(9);

        return view('portfolio.projects.index', [
            'projects' => $projects,
            'settings' => $this->settings(),
            'navigationLinks' => $this->navigationLinks(),
            'socialLinks' => $this->socialLinks(),
        ]);
    }

    public function project(string $slug): View
    {
        $project = PortfolioItem::query()
            ->where('type', 'project')
            ->where('is_published', true)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('portfolio.projects.show', [
            'project' => $project,
            'settings' => $this->settings(),
            'relatedProjects' => PortfolioItem::query()
                ->where('type', 'project')
                ->where('is_published', true)
                ->whereKeyNot($project->id)
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->limit(3)
                ->get(),
            'navigationLinks' => $this->navigationLinks(),
            'socialLinks' => $this->socialLinks(),
        ]);
    }

    public function contact(StoreContactMessageRequest $request, PortfolioAnalytics $analytics): RedirectResponse
    {
        ContactMessage::query()->create($request->safe()->only(['name', 'email', 'subject', 'message']));
        $analytics->recordEvent($request, ['event' => 'contact_submit', 'path' => '/#contact']);

        return back()->with('contact_submitted', true);
    }

    /** @return array<string, mixed> */
    private function settings(): array
    {
        return PortfolioSetting::query()->where('key', 'site')->first()?->value ?? [];
    }

    private function navigationLinks(): Collection
    {
        return PortfolioItem::query()->where('type', 'navigation')->where('is_published', true)->orderBy('sort_order')->get();
    }

    private function socialLinks(): Collection
    {
        return PortfolioItem::query()->where('type', 'social')->where('is_published', true)->orderBy('sort_order')->get();
    }
}
