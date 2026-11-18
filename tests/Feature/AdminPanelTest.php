<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\ContactChannel;
use App\Models\Document;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\Redirect;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Slide;
use App\Models\Slider;
use App\Models\Tariff;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/tariffs')->assertRedirect('/admin/login');
        $this->get('/admin/tariffs/create')->assertRedirect('/admin/login');
    }

    public function test_login_page_renders(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_admin_can_open_dashboard_and_resources(): void
    {
        $admin = $this->userWithRole('admin');

        foreach ([
            '/admin',
            '/admin/tariffs',
            '/admin/users',
            '/admin/roles',
            '/admin/posts',
            '/admin/promotions',
            '/admin/sliders',
            '/admin/slides',
            '/admin/pages',
            '/admin/documents',
            '/admin/faqs',
            '/admin/help-articles',
            '/admin/leads',
            '/admin/activities',
            '/admin/media',
            '/admin/site-settings',
            '/admin/redirects',
            '/admin/engineer-workspace',
        ] as $path) {
            $this->actingAs($admin)->get($path)->assertOk();
        }
    }

    public function test_full_page_create_and_edit_routes_are_available_to_admin(): void
    {
        $admin = $this->userWithRole('admin');

        $resources = [
            'tariffs' => Tariff::class,
            'users' => User::class,
            'posts' => Post::class,
            'promotions' => Promotion::class,
            'sliders' => Slider::class,
            'slides' => Slide::class,
            'pages' => Page::class,
            'documents' => Document::class,
            'faqs' => Faq::class,
            'help-articles' => HelpArticle::class,
            'technologies' => Technology::class,
            'services' => Service::class,
            'areas' => Area::class,
            'contact-channels' => ContactChannel::class,
            'faq-categories' => FaqCategory::class,
            'help-categories' => HelpCategory::class,
            'menus' => Menu::class,
            'menu-items' => MenuItem::class,
            'redirects' => Redirect::class,
            'site-settings' => SiteSetting::class,
        ];

        foreach ($resources as $uri => $model) {
            $this->actingAs($admin)->get("/admin/{$uri}/create")->assertOk();

            if ($record = $model::query()->first()) {
                $this->actingAs($admin)->get("/admin/{$uri}/{$record->getRouteKey()}/edit")->assertOk();
            }
        }

        // The role set is fixed: roles have list and edit pages only.
        $this->actingAs($admin)->get('/admin/roles')->assertOk();
        $this->actingAs($admin)->get('/admin/roles/'.Role::findByName('manager')->getKey().'/edit')->assertOk();
        $this->actingAs($admin)->get('/admin/roles/create')->assertNotFound();
    }

    public function test_topbar_has_link_to_public_site_in_new_tab(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('На сайт')
            ->assertSee('href="'.route('home').'"', false)
            ->assertSee('target="_blank"', false);
    }

    public function test_manager_can_manage_content_tariffs_and_leads(): void
    {
        $manager = $this->userWithRole('manager');

        $this->actingAs($manager)->get('/admin/posts')->assertOk();
        $this->actingAs($manager)->get('/admin/posts/create')->assertOk();
        $this->actingAs($manager)->get('/admin/tariffs')->assertOk();
        $this->actingAs($manager)->get('/admin/leads')->assertOk();
        $this->actingAs($manager)->get('/admin/users')->assertForbidden();
        $this->actingAs($manager)->get('/admin/roles')->assertForbidden();
        $this->actingAs($manager)->get('/admin/site-settings')->assertForbidden();
        $this->actingAs($manager)->get('/admin/econnections-map')->assertForbidden();
    }

    public function test_engineer_can_open_workspace_but_not_catalog(): void
    {
        $engineer = $this->userWithRole('engineer');

        $this->actingAs($engineer)->get('/admin/econnections-map')->assertOk();
        $this->actingAs($engineer)->get('/admin/tariffs')->assertForbidden();
        $this->actingAs($engineer)->get('/admin/posts')->assertForbidden();
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        return $user;
    }
}
