<?php

namespace App\Http\Controllers;

use App\Http\Requests\SilidKarununganFormRequest;
use App\Models\SilidKarununganRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SilidKarununganController extends Controller
{
    /**
     * Show library request form.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('landing', ['form' => 'silid']);
    }

    /**
     * Store library request submission.
     */
    public function store(SilidKarununganFormRequest $request): RedirectResponse
    {
        $silid = SilidKarununganRequest::create($request->validated());

        return redirect()->route('landing')->with([
            'submitted_success' => true,
            'type' => 'Silid Karunungan Booking',
            'referenceNumber' => 'SK-SIL-' . str_pad($silid->id, 5, '0', STR_PAD_LEFT),
            'name' => $silid->requestor_first_name . ' ' . $silid->requestor_last_name,
            'email' => $silid->email,
            'detail' => 'Schedule: ' . $silid->preferred_date->format('M d, Y') . ' @ ' . $silid->preferred_time,
            'date' => $silid->created_at->format('M d, Y h:i A'),
        ]);
    }
}
