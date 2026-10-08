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

    public function test_login_page_shows_remember_me_unchecked_by_default(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Remember me on this device')
            ->assertSee('id="remember" name="remember" type="checkbox" value="1"', false)
            ->assertDontSee('id="remember" name="remember" type="checkbox" value="1" checked', false);
    }

    public function test_remember_me_sets_a_persistent_login_token(): void
    {
        $owner = User::factory()->create();
        $owner->forceFill(['is_admin' => true, 'admin_slot' => 'owner'])->save();

        $this->post(route('admin.login.store'), [
            'email' => $owner->email,
            'password' => 'password',
            'remember' => '1',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertNotNull($owner->fresh()->getRememberToken());
    }

    public function test_login_without_remember_me_does_not_set_a_persistent_token(): void
    {
        $owner = User::factory()->create();
        $owner->forceFill(['is_admin' => true, 'admin_slot' => 'owner'])->save();

        $this->post(route('admin.login.store'), [
            'email' => $owner->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertNull($owner->fresh()->getRememberToken());
    }
}
