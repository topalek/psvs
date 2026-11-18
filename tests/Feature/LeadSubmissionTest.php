<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\Lead;
use App\Models\LeadStatusHistory;
use App\Models\Tariff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_valid_lead_is_created_with_status_history_and_consent(): void
    {
        $response = $this->post('/leads', [
            'type' => LeadType::Connection->value,
            'name' => 'Иван Петров',
            'phone' => '+7 (918) 000-00-00',
            'email' => 'ivan@example.com',
            'message' => 'Хочу подключиться.',
            'consent' => '1',
        ]);

        $response->assertRedirect();

        $lead = Lead::query()->firstOrFail();

        $this->assertSame('Иван Петров', $lead->name);
        $this->assertSame('+7 (918) 000-00-00', $lead->phone);
        $this->assertSame(LeadStatus::New, $lead->status);
        $this->assertSame('2026-11-01', $lead->consent_version);
        $this->assertNotNull($lead->consent_at);

        $this->assertSame(1, LeadStatusHistory::query()->where('lead_id', $lead->id)->count());
    }

    public function test_lead_requires_consent(): void
    {
        $this->post('/leads', [
            'type' => LeadType::Connection->value,
            'name' => 'Иван Петров',
            'phone' => '+7 918 000-00-00',
        ])->assertSessionHasErrors('consent');

        $this->assertSame(0, Lead::query()->count());
    }

    public function test_lead_requires_phone_or_email(): void
    {
        $this->post('/leads', [
            'type' => LeadType::Connection->value,
            'name' => 'Иван Петров',
            'consent' => '1',
        ])->assertSessionHasErrors('phone');

        $this->assertSame(0, Lead::query()->count());
    }

    public function test_business_lead_requires_company_name(): void
    {
        $this->post('/leads', [
            'type' => LeadType::Business->value,
            'name' => 'Иван Петров',
            'phone' => '+7 918 000-00-00',
            'consent' => '1',
        ])->assertSessionHasErrors('company_name');
    }

    public function test_honeypot_field_blocks_spam_submission(): void
    {
        $this->post('/leads', [
            'type' => LeadType::Connection->value,
            'name' => 'Бот',
            'phone' => '+7 918 000-00-00',
            'website' => 'https://spam.example',
            'consent' => '1',
        ])->assertSessionHasErrors('website');

        $this->assertSame(0, Lead::query()->count());
    }

    public function test_lead_cannot_reference_an_unpublished_tariff(): void
    {
        $tariff = Tariff::query()->create([
            'name' => 'Черновой тариф',
            'slug' => 'draft-tariff',
            'regular_price' => 100000,
            'is_published' => false,
        ]);

        $this->post('/leads', [
            'type' => LeadType::Connection->value,
            'name' => 'Иван Петров',
            'phone' => '+7 918 000-00-00',
            'tariff_id' => $tariff->id,
            'consent' => '1',
        ])->assertSessionHasErrors('tariff_id');
    }

    public function test_lead_can_be_submitted_as_json(): void
    {
        $response = $this->postJson('/leads', [
            'type' => LeadType::Callback->value,
            'name' => 'Мария',
            'phone' => '+7 918 111-11-11',
            'consent' => '1',
        ]);

        $response->assertCreated()->assertJsonStructure(['message', 'lead_id']);

        $this->assertDatabaseHas('leads', [
            'name' => 'Мария',
            'type' => LeadType::Callback->value,
        ]);
    }

    public function test_phone_is_sanitized_and_utm_parameters_are_stored(): void
    {
        $this->post('/leads', [
            'type' => LeadType::Connection->value,
            'name' => '  Пётр  ',
            'phone' => '+7 (918) 222-33-44<script>',
            'utm_source' => 'yandex',
            'utm_campaign' => 'autumn',
            'consent' => '1',
        ]);

        $lead = Lead::query()->firstOrFail();

        $this->assertSame('Пётр', $lead->name);
        $this->assertSame('+7 (918) 222-33-44', $lead->phone);
        $this->assertSame(['utm_source' => 'yandex', 'utm_campaign' => 'autumn'], $lead->utm);
    }
}
