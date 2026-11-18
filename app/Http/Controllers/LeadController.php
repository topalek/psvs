<?php

namespace App\Http\Controllers;

use App\Enums\LeadType;
use App\Http\Requests\LeadRequest;
use App\Models\SiteSetting;
use App\Services\LeadManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class LeadController extends Controller
{
    public function store(LeadRequest $request, LeadManager $manager): RedirectResponse|JsonResponse
    {
        $data = $request->safe()->except(['consent', 'website']);

        $data['type'] = $request->input('type', LeadType::Connection->value);
        $data['source_page'] = $request->input('source_page') ?: url()->previous();
        $data['utm'] = collect(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'])
            ->mapWithKeys(fn ($key) => [$key => $request->input($key)])
            ->filter()
            ->all();
        $data['consent_version'] = SiteSetting::get('consent_version');
        $data['consent_at'] = now();

        $lead = $manager->create($data);

        $message = 'Заявка получена. Специалист свяжется с вами. Это подтверждение получения обращения, а не гарантия доступности подключения по указанному адресу.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'lead_id' => $lead->id,
            ], 201);
        }

        return back()->with('lead_success', $message);
    }
}
