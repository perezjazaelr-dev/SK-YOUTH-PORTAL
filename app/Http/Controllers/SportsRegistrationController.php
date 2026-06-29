<?php

namespace App\Http\Controllers;

use App\Http\Requests\SportsRegistrationFormRequest;
use App\Models\SportsRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SportsRegistrationController extends Controller
{
    /**
     * Show sports registration form.
     */
    public function create(): View
    {
        return view('forms.sports-registration');
    }

    /**
     * Store sports registration submission.
     */
    public function store(SportsRegistrationFormRequest $request): RedirectResponse
    {
        $sports = SportsRegistration::create($request->validated());

        return redirect()->route('landing')->with([
            'submitted_success' => true,
            'type' => 'Sports Registration',
            'referenceNumber' => 'SK-SPO-' . str_pad($sports->id, 5, '0', STR_PAD_LEFT),
            'name' => $sports->first_name . ' ' . $sports->last_name,
            'email' => $sports->email,
            'detail' => 'Sport: ' . $sports->sport . ' (Team: ' . ($sports->team_name ?? 'None') . ') on ' . $sports->event_date->format('M d, Y'),
            'date' => $sports->created_at->format('M d, Y h:i A'),
        ]);
    }
}
