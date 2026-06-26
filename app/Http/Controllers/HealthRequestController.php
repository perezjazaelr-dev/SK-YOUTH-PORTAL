<?php

namespace App\Http\Controllers;

use App\Http\Requests\HealthRequestFormRequest;
use App\Models\HealthRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HealthRequestController extends Controller
{
    /**
     * Show health consultation form.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('landing', ['form' => 'health']);
    }

    /**
     * Store health consultation submission.
     */
    public function store(HealthRequestFormRequest $request): RedirectResponse
    {
        $health = HealthRequest::create($request->validated());

        return redirect()->route('landing')->with([
            'submitted_success' => true,
            'type' => 'Health Consultation',
            'referenceNumber' => 'SK-HEA-' . str_pad($health->id, 5, '0', STR_PAD_LEFT),
            'name' => $health->first_name . ' ' . $health->last_name,
            'email' => $health->email,
            'detail' => 'Appointment: ' . $health->preferred_date->format('M d, Y') . ' @ ' . $health->preferred_time,
            'date' => $health->created_at->format('M d, Y h:i A'),
        ]);
    }

    /**
     * Show mental health form.
     */
    public function createMental(): RedirectResponse
    {
        return redirect()->route('landing', ['form' => 'mental-health']);
    }

    /**
     * Store mental health submission.
     */
    public function storeMental(HealthRequestFormRequest $request): RedirectResponse
    {
        $health = HealthRequest::create($request->validated());

        return redirect()->route('landing')->with([
            'submitted_success' => true,
            'type' => 'Mental Health Support',
            'referenceNumber' => 'SK-HEA-' . str_pad($health->id, 5, '0', STR_PAD_LEFT),
            'name' => $health->first_name . ' ' . $health->last_name,
            'email' => $health->email,
            'detail' => 'Appointment: ' . $health->preferred_date->format('M d, Y') . ' @ ' . $health->preferred_time,
            'date' => $health->created_at->format('M d, Y h:i A'),
        ]);
    }
}
