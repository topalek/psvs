<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadStatusHistory;
use App\Models\User;
use App\Services\LeadManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_direct_status_update_records_history_with_authenticated_user(): void
    {
        $user = User::factory()->create();
        $lead = $this->makeLead();

        $this->actingAs($user);

        $lead->update(['status' => LeadStatus::InProgress]);

        $history = LeadStatusHistory::query()->where('lead_id', $lead->id)->latest('id')->firstOrFail();

        $this->assertSame('new', $history->from_status);
        $this->assertSame('in_progress', $history->to_status);
        $this->assertSame($user->id, $history->user_id);
    }

    public function test_manager_status_change_records_explicit_user_and_comment_once(): void
    {
        $user = User::factory()->create();
        $lead = $this->makeLead();

        app(LeadManager::class)->changeStatus($lead, LeadStatus::Completed, $user->id, 'Клиент подключён');

        $history = LeadStatusHistory::query()->where('lead_id', $lead->id)->get();

        $this->assertCount(1, $history);
        $this->assertSame('completed', $history->first()->to_status);
        $this->assertSame($user->id, $history->first()->user_id);
        $this->assertSame('Клиент подключён', $history->first()->comment);
    }

    public function test_setting_the_same_status_does_not_create_history(): void
    {
        $lead = $this->makeLead();

        app(LeadManager::class)->changeStatus($lead, LeadStatus::New);

        $this->assertSame(0, LeadStatusHistory::query()->where('lead_id', $lead->id)->count());
    }

    private function makeLead(): Lead
    {
        return Lead::query()->create([
            'type' => 'connection',
            'name' => 'Тестовый клиент',
            'phone' => '+7 918 000-00-00',
            'status' => LeadStatus::New,
        ]);
    }
}
