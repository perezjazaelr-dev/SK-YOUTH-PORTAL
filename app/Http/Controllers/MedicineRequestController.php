<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicineRequestFormRequest;
use App\Models\MedicineRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MedicineRequestController extends Controller
{
    /**
     * Show medicine request form.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('landing', ['form' => 'medicine']);
    }

    /**
     * Store medicine request submission.
     */
    public function store(MedicineRequestFormRequest $request): RedirectResponse
    {
        $med = MedicineRequest::create($request->validated());

        return redirect()->route('landing')->with([
            'submitted_success' => true,
            'type' => 'Pabili Medicine Services',
            'referenceNumber' => 'SK-MED-' . str_pad($med->id, 5, '0', STR_PAD_LEFT),
            'name' => $med->requestor_first_name . ' ' . $med->requestor_last_name,
            'email' => $med->email,
            'detail' => 'Address: ' . $med->complete_address,
            'date' => $med->created_at->format('M d, Y h:i A'),
        ]);
    }
}
