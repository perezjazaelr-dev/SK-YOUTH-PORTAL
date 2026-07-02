<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ConsultationController extends Controller
{
    /**
     * Display the admin consultations inbox.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->input('status');
        $search = $request->input('search');

        $query = ConsultationRequest::latest();

        if ($statusFilter && in_array($statusFilter, ['Pending', 'In Review', 'Resolved'], true)) {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $consultations = $query->paginate(15)->withQueryString();

        return view('admin.consultations.index', compact('consultations', 'statusFilter', 'search'));
    }

    /**
     * Update the status of a consultation request.
     */
    public function updateStatus(Request $request, ConsultationRequest $consultation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Pending,In Review,Resolved'],
        ]);

        $consultation->update([
            'status' => $validated['status']
        ]);

        return redirect()->route('admin.consultations.index')
            ->with('success', 'Consultation status updated to ' . $validated['status']);
    }

    /**
     * Reply to a consultation request.
     */
    public function reply(Request $request, ConsultationRequest $consultation): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $replies = $consultation->replies ?? [];
        $replies[] = [
            'message' => $validated['message'],
            'sender' => auth()->user() ? auth()->user()->name : 'SK Staff',
            'timestamp' => now()->toDateTimeString(),
        ];

        $consultation->update([
            'replies' => $replies,
            'status' => $consultation->status === 'Resolved' ? 'Resolved' : 'In Review',
        ]);

        return redirect()->route('admin.consultations.index')
            ->with('success', 'Reply successfully sent to the anonymous inquirer.');
    }

    /**
     * Show the public consultation form view (Public).
     */
    public function showForm(): View
    {
        return view('citizen.skonsulta.index');
    }

    /**
     * Show the public consultation tracking view (Public).
     */
    public function showTracker(): View
    {
        return view('citizen.skonsulta.track');
    }

    /**
     * Submit an anonymous consultation request (Public).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'in:General Concern,Suggestion,Report'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('consultation-attachments', 'public');
        }

        $consultation = ConsultationRequest::create([
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'attachment' => $attachmentPath,
            'status' => 'Pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Anonymous consultation request submitted successfully.',
            'tracking_id' => $consultation->tracking_id,
        ], 201);
    }

    /**
     * Track consultation by tracking ID (Public).
     */
    public function track(Request $request): JsonResponse
    {
        $trackingId = $request->input('tracking_id');
        if (empty($trackingId)) {
            return response()->json(['error' => 'Tracking ID is required.'], 400);
        }

        $consultation = ConsultationRequest::where('tracking_id', $trackingId)->first();

        if (!$consultation) {
            return response()->json(['error' => 'Invalid Tracking ID. Record not found.'], 404);
        }

        return response()->json([
            'tracking_id' => $consultation->tracking_id,
            'category' => $consultation->category,
            'subject' => $consultation->subject,
            'message' => $consultation->message,
            'attachment' => $consultation->attachment ? asset('storage/' . $consultation->attachment) : null,
            'status' => $consultation->status,
            'replies' => $consultation->replies ?? [],
            'created_at' => $consultation->created_at?->toDateTimeString(),
        ]);
    }
}
