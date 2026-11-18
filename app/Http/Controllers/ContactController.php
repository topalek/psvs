<?php

namespace App\Http\Controllers;

use App\Models\ContactChannel;
use App\Support\Contacts;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __invoke(): View
    {
        return view('contacts.index', [
            'phones' => ContactChannel::query()->active()->where('type', 'phone')->get(),
            'emails' => ContactChannel::query()->active()->where('type', 'email')->get(),
            'address' => Contacts::address(),
            'officeHours' => Contacts::officeHours(),
            'supportHours' => Contacts::supportHours(),
            'mapUrl' => Contacts::mapUrl(),
        ]);
    }
}
