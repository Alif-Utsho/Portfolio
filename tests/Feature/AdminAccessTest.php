<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pages_require_an_authenticated_owner(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));

        $regularUser = User::factory()->create();
        $this->actingAs($regularUser)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_owner_can_sign_in_and_sign_out(): void
    {
        $owner = User::factory()->create();
        $owner->forceFill(['is_admin' => true, 'admin_slot' => 'owner'])->save();

        $this->post(route('admin.login.store'), [
            'email' => $owner->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.content.index', 'project'))->assertOk();
        $this->get(route('admin.messages.index'))->assertOk();
        $this->get(route('admin.media.index'))->assertOk();
        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
    }
}
