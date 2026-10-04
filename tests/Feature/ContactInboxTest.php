<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactInboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_validates_and_saves_a_submission(): void
    {
        $this->from(route('home'))->post(route('contact.store'), [
            'name' => 'A Visitor', 'email' => 'visitor@example.com',
            'subject' => 'Project question', 'message' => 'I would like to discuss a project.',
            'website' => '',
        ])->assertRedirect(route('home'))->assertSessionHas('contact_submitted', true);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'visitor@example.com', 'status' => 'unread', 'subject' => 'Project question',
        ]);
        $this->post(route('contact.store'), [])->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    public function test_owner_can_review_and_archive_inbox_messages(): void
    {
        $owner = User::factory()->create();
        $owner->forceFill(['is_admin' => true, 'admin_slot' => 'owner'])->save();
        $message = ContactMessage::query()->create([
            'name' => 'A Visitor', 'email' => 'visitor@example.com', 'subject' => 'Hello',
            'message' => 'Message body.', 'status' => 'unread',
        ]);

        $this->actingAs($owner)->get(route('admin.messages.show', $message))
            ->assertOk()->assertSee('Message body.');
        $this->assertDatabaseHas('contact_messages', ['id' => $message->id, 'status' => 'read']);

        $this->from(route('admin.messages.index'))->patch(route('admin.messages.status', $message), ['status' => 'archived'])
            ->assertRedirect(route('admin.messages.index'));
        $this->assertDatabaseHas('contact_messages', ['id' => $message->id, 'status' => 'archived']);
    }
}
