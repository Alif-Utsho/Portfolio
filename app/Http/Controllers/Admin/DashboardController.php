<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\PortfolioItem;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'contentCount' => PortfolioItem::query()->count(),
            'publishedCount' => PortfolioItem::query()->where('is_published', true)->count(),
            'unreadCount' => ContactMessage::query()->where('status', 'unread')->count(),
            'recentMessages' => ContactMessage::query()->latest()->limit(5)->get(),
        ]);
    }
}
