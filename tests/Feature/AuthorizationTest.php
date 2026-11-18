<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Post;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_admin_can_do_everything(): void
    {
        $admin = $this->userWithRole('admin');

        $this->assertTrue(Gate::forUser($admin)->allows('viewAny', Tariff::class));
        $this->assertTrue(Gate::forUser($admin)->allows('create', Tariff::class));
        $this->assertTrue(Gate::forUser($admin)->allows('deleteAny', Tariff::class));
        $this->assertTrue(Gate::forUser($admin)->allows('create', User::class));
        $this->assertTrue(Gate::forUser($admin)->allows('engineer_workspace.access'));
    }

    public function test_manager_can_manage_content_tariffs_and_leads(): void
    {
        $manager = $this->userWithRole('manager');
        $post = Post::query()->firstOrFail();
        $tariff = Tariff::query()->firstOrFail();
        $lead = Lead::query()->create([
            'type' => 'connection',
            'name' => 'Клиент',
            'phone' => '+7 918 000-00-00',
        ]);

        $this->assertTrue(Gate::forUser($manager)->allows('create', Post::class));
        $this->assertTrue(Gate::forUser($manager)->allows('update', $post));
        $this->assertTrue(Gate::forUser($manager)->allows('create', Tariff::class));
        $this->assertTrue(Gate::forUser($manager)->allows('update', $tariff));
        $this->assertTrue(Gate::forUser($manager)->allows('view', $lead));
        $this->assertTrue(Gate::forUser($manager)->allows('update', $lead));
        $this->assertTrue(Gate::forUser($manager)->allows('assign', $lead));
        $this->assertFalse(Gate::forUser($manager)->allows('delete', $lead));
        $this->assertFalse(Gate::forUser($manager)->allows('viewAny', User::class));
        $this->assertFalse($manager->can('roles.manage'));
        $this->assertFalse($manager->can('engineer_workspace.access'));
    }

    public function test_engineer_only_has_workspace_access(): void
    {
        $engineer = $this->userWithRole('engineer');

        $this->assertTrue(Gate::forUser($engineer)->allows('engineer_workspace.access'));
        $this->assertFalse(Gate::forUser($engineer)->allows('viewAny', Tariff::class));
        $this->assertFalse(Gate::forUser($engineer)->allows('viewAny', Lead::class));
        $this->assertFalse($engineer->can('roles.manage'));
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $administrator = $this->userWithRole('admin');
        $other = $this->userWithRole('manager');

        $this->assertTrue(Gate::forUser($administrator)->allows('deactivate', $other));
        $this->assertFalse(Gate::forUser($administrator)->allows('deactivate', $administrator));
    }

    public function test_inactive_user_flag_is_exposed(): void
    {
        $user = $this->userWithRole('manager');

        $this->assertTrue($user->isActive());

        $user->update(['is_active' => false]);

        $this->assertFalse($user->fresh()->isActive());
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        return $user;
    }
}
