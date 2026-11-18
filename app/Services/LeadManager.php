<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LeadManager
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Lead
    {
        return DB::transaction(function () use ($data) {
            $lead = Lead::create([
                ...$data,
                'status' => $data['status'] ?? LeadStatus::New->value,
            ]);

            LeadStatusHistory::create([
                'lead_id' => $lead->id,
                'user_id' => null,
                'from_status' => null,
                'to_status' => $lead->status->value,
                'comment' => 'Заявка создана через сайт',
            ]);

            $this->notify($lead);

            return $lead;
        });
    }

    public function changeStatus(Lead $lead, LeadStatus $status, ?int $userId = null, ?string $comment = null): void
    {
        if ($lead->status === $status) {
            return;
        }

        DB::transaction(function () use ($lead, $status, $userId, $comment) {
            $lead->statusChangedBy = $userId ?? auth()->id();
            $lead->statusChangeComment = $comment;

            $lead->update(['status' => $status]);
        });
    }

    protected function notify(Lead $lead): void
    {
        $recipient = config('mail.lead_notification_email') ?: env('LEAD_NOTIFICATION_EMAIL');

        if (! $recipient) {
            Log::info('Lead created, notification recipient is not configured.', ['lead_id' => $lead->id]);

            return;
        }

        try {
            Mail::raw(
                "Новая заявка №{$lead->id}\n".
                "Тип: {$lead->type->label()}\n".
                "Имя: {$lead->name}\n".
                "Телефон: {$lead->phone}\n".
                "Email: {$lead->email}\n".
                "Адрес: {$lead->addressLine()}\n".
                "Сообщение: {$lead->message}",
                fn ($message) => $message->to($recipient)->subject('Новая заявка с сайта №'.$lead->id)
            );
        } catch (\Throwable $e) {
            Log::error('Lead notification failed.', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
