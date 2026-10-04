<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use App\Models\PortfolioSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_show_only_published_projects_and_unknown_slugs_404(): void
    {
        PortfolioItem::query()->create([
            'type' => 'project', 'title' => 'Published Project', 'slug' => 'published-project',
            'is_published' => true, 'sort_order' => 1,
        ]);
        PortfolioItem::query()->create([
            'type' => 'project', 'title' => 'Draft Project', 'slug' => 'draft-project',
            'is_published' => false, 'sort_order' => 2,
        ]);

        $this->get(route('home'))->assertOk();
        $this->get(route('projects.index'))->assertOk()->assertSee('Published Project')->assertDontSee('Draft Project');
        $this->get(route('projects.show', 'published-project'))->assertOk()->assertSee('Published Project');
        $this->get(route('projects.show', 'draft-project'))->assertNotFound();
        $this->get(route('projects.show', 'missing-project'))->assertNotFound();
    }

    public function test_owner_can_create_publish_edit_and_remove_a_project(): void
    {
        $owner = User::factory()->create();
        $owner->forceFill(['is_admin' => true, 'admin_slot' => 'owner'])->save();
        $this->actingAs($owner);

        $this->post(route('admin.content.store', 'project'), [
            'type' => 'project', 'title' => 'CMS Project', 'slug' => '',
            'summary' => 'A project managed from the CMS.', 'sort_order' => 5,
            'is_published' => '1', 'is_featured' => '1', 'technologies' => 'PHP, Laravel',
        ])->assertRedirect(route('admin.content.index', 'project'));

        $project = PortfolioItem::query()->where('title', 'CMS Project')->sole();
        $this->assertSame('cms-project', $project->slug);
        $this->get(route('admin.content.edit', $project))->assertOk()->assertSee('Edit content');
        $this->get(route('projects.show', $project->slug))->assertOk()->assertSee('CMS Project');

        $this->put(route('admin.content.update', $project), [
            'type' => 'project', 'title' => 'Updated CMS Project', 'slug' => 'updated-cms-project',
            'summary' => 'Updated summary.', 'sort_order' => 3,
        ])->assertRedirect(route('admin.content.index', 'project'));
        $this->assertDatabaseHas('portfolio_items', ['id' => $project->id, 'title' => 'Updated CMS Project']);

        $this->delete(route('admin.content.destroy', $project))->assertRedirect(route('admin.content.index', 'project'));
        $this->assertDatabaseMissing('portfolio_items', ['id' => $project->id]);
    }

    public function test_owner_can_open_each_content_editor_variant(): void
    {
        $owner = User::factory()->create();
        $owner->forceFill(['is_admin' => true, 'admin_slot' => 'owner'])->save();
        $this->actingAs($owner);

        foreach (['experience', 'skill', 'personal', 'social', 'navigation'] as $type) {
            $item = PortfolioItem::query()->create([
                'type' => $type,
                'title' => ucfirst($type).' entry',
                'url' => in_array($type, ['social', 'navigation'], true) ? 'https://example.com' : null,
                'is_published' => false,
                'sort_order' => 1,
            ]);

            $this->get(route('admin.content.edit', $item))->assertOk()->assertSee('Edit content');
        }
    }

    public function test_owner_can_update_profile_and_search_metadata(): void
    {
        $owner = User::factory()->create();
        $owner->forceFill(['is_admin' => true, 'admin_slot' => 'owner'])->save();

        $this->actingAs($owner)->put(route('admin.settings.update'), [
            'site_title' => 'Portfolio Owner — Engineer',
            'site_description' => 'A refreshed portfolio description.',
            'hero_title' => 'Useful software, thoughtfully made.',
            'hero_intro' => 'A short profile introduction.',
            'about_lead' => 'An updated about lead.',
            'about_body' => 'A longer profile summary.',
            'location' => 'Dhaka, Bangladesh',
            'github_url' => 'https://github.com/Alif-Utsho',
            'seo_title' => 'Portfolio SEO title',
            'seo_description' => 'Search result description.',
        ])->assertRedirect();

        $settings = PortfolioSetting::query()->where('key', 'site')->sole()->value;
        $this->assertSame('Portfolio Owner — Engineer', $settings['site_title']);
        $this->assertSame('Search result description.', $settings['seo_description']);
    }
}
