<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Экспорт CSV')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->visible(fn () => auth()->user()?->can('leads.export') ?? false)
                ->action(function (): StreamedResponse {
                    $leads = Lead::query()->latest()->get();

                    return response()->streamDownload(function () use ($leads): void {
                        $handle = fopen('php://output', 'w');

                        fputcsv($handle, [
                            'ID', 'Дата', 'Тип', 'Имя', 'Телефон', 'Email', 'Статус',
                            'Ответственный', 'Населённый пункт', 'Улица', 'Дом', 'Сообщение',
                        ]);

                        foreach ($leads as $lead) {
                            fputcsv($handle, [
                                $lead->id,
                                $lead->created_at?->format('Y-m-d H:i:s'),
                                $lead->type?->label(),
                                $lead->name,
                                $lead->phone,
                                $lead->email,
                                $lead->status?->label(),
                                $lead->assignee?->name,
                                $lead->locality,
                                $lead->street,
                                $lead->house,
                                $lead->message,
                            ]);
                        }

                        fclose($handle);
                    }, 'leads-'.now()->format('Y-m-d-His').'.csv');
                }),
        ];
    }
}
