<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_setup_requires_the_configured_key_and_creates_only_the_first_owner(): void
    {
        config(['portfolio.admin_setup_key' => 'setup-secret']);
        $details = [
            'name' => 'Portfolio Owner', 'email' => 'owner@example.com',
            'password' => 'StrongPassword2026', 'password_confirmation' => 'StrongPassword2026',
        ];

        $this->post(route('admin.setup.store'), $details + ['setup_key' => 'wrong-key'])->assertForbidden();
        $this->assertDatabaseCount('users', 0);

        $this->post(route('admin.setup.store'), $details + ['setup_key' => 'setup-secret'])
            ->assertRedirect(route('admin.login'));
        $owner = User::query()->sole();
        $this->assertTrue($owner->is_admin);
        $this->assertSame('owner', $owner->admin_slot);

        $this->get(route('admin.setup'))->assertNotFound();
        $this->post(route('admin.setup.store'), $details + ['setup_key' => 'setup-secret'])->assertNotFound();
    }

    public function test_setup_is_unavailable_without_an_admin_setup_key(): void
    {
        config(['portfolio.admin_setup_key' => '']);

        $this->get(route('admin.setup'))->assertOk()->assertSee('ADMIN_SETUP_KEY');
        $this->post(route('admin.setup.store'), [
            'setup_key' => 'anything', 'name' => 'Portfolio Owner', 'email' => 'owner@example.com',
            'password' => 'StrongPassword2026', 'password_confirmation' => 'StrongPassword2026',
        ])->assertStatus(503);
    }
}
