<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SetupOwnerRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SetupController extends Controller
{
    public function create(): View
    {
        abort_if(User::query()->where('is_admin', true)->exists(), 404);

        return view('admin.auth.setup', [
            'setupAvailable' => filled(config('portfolio.admin_setup_key')),
        ]);
    }

    public function store(SetupOwnerRequest $request): RedirectResponse
    {
        abort_if(User::query()->where('is_admin', true)->exists(), 404);

        $configuredKey = (string) config('portfolio.admin_setup_key');
        abort_if($configuredKey === '', 503, 'Admin setup is not configured.');
        abort_unless(hash_equals($configuredKey, $request->string('setup_key')->toString()), 403);

        $user = DB::transaction(function () use ($request): User {
            abort_if(User::query()->where('is_admin', true)->exists(), 404);

            $user = new User([
                'name' => $request->validated('name'),
                'email' => Str::lower($request->validated('email')),
                'password' => Hash::make($request->validated('password')),
            ]);
            $user->forceFill(['is_admin' => true, 'admin_slot' => 'owner']);
            $user->save();

            return $user;
        });

        return to_route('admin.login')->with('status', 'Owner account created. Sign in to continue.');
    }
}
