<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SportsRegistration;
use App\Models\User;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Mail\StatusChangedMail;
use App\Helpers\PrivacyHelper;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;

class SportsLeagueController extends Controller
{
    /**
     * Display a listing of sports registrations.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $yearFilter = $request->input('year');
        $divisionFilter = $request->input('division');
        
        $limit = $request->input('limit', 10);
        if (!in_array($limit, [10, 15, 25, 50, 100])) {
            $limit = 10;
        }

        $query = SportsRegistration::with('processedBy')->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status && in_array($status, ['pending', 'approved', 'declined'])) {
            if ($status === 'pending') {
                $query->whereIn('status', ['pending', 'review']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($yearFilter) {
            $query->whereYear('created_at', $yearFilter);
        }

        if ($divisionFilter) {
            if ($divisionFilter === 'Basketball Midget Division') {
                $query->where('sport', 'Basketball')->where(function($q) {
                    $q->where('division', 'Basketball Midget Division')->orWhere('age', '<', 18);
                });
            } elseif ($divisionFilter === 'Basketball Senior Division') {
                $query->where('sport', 'Basketball')->where(function($q) {
                    $q->where('division', 'Basketball Senior Division')->orWhere('age', '>=', 18);
                });
            } elseif ($divisionFilter === 'Volleyball Womens') {
                $query->where('sport', 'Volleyball')->where(function($q) {
                    $q->where('division', 'Volleyball Womens')->orWhere('gender', 'Female');
                });
            } elseif ($divisionFilter === 'Volleyball Mens Division') {
                $query->where('sport', 'Volleyball')->where(function($q) {
                    $q->where('division', 'Volleyball Mens Division')->orWhere('gender', 'Male');
                });
            } else {
                $query->where('division', $divisionFilter);
            }
        }

        $paginatedRequests = $query->paginate($limit)->withQueryString();

        // Mask PII on paginated records based on role clearance
        $currentUser = auth()->user();
        $paginatedRequests->getCollection()->transform(fn($item) => PrivacyHelper::filterPII($item, $currentUser));

        // Extract distinct request years
        $driver = \DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $years = SportsRegistration::selectRaw("strftime('%Y', created_at) as year")->distinct()->pluck('year')->toArray();
        } else {
            $years = SportsRegistration::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray();
        }
        rsort($years);
        if (empty($years)) {
            $years = [date('Y')];
        }

        return view('admin.sports-league.index', compact(
            'paginatedRequests',
            'search',
            'status',
            'yearFilter',
            'divisionFilter',
            'limit',
            'years'
        ));
    }

    /**
     * Show individual sports registration details.
     */
    public function show($id): View
    {
        $req = SportsRegistration::with('processedBy')->findOrFail($id);

        if ($req->status === 'pending') {
            $req->status = 'review';
            $req->save();

            // Notify user
            $user = User::where('email', $req->email)->first();
            if ($user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Sports Request Under Review',
                    'message' => 'Your sports registration request is now under review.',
                    'url' => route('profile.my-requests')
                ]);
            }

            try {
                Mail::to($req->email)->send(new StatusChangedMail($req));
            } catch (\Exception $e) {
                // Silently swallow mail exceptions
            }
        }

        $currentUser = auth()->user();

        // Audit PII access
        if ($currentUser && $currentUser->hasPiiClearance() && strtolower($currentUser->email) !== strtolower($req->email)) {
            ActivityLog::create([
                'user_id' => $currentUser->id,
                'action' => 'pii_accessed',
                'subject_type' => get_class($req),
                'subject_id' => $req->id,
                'payload' => json_encode([
                    'accessed_by' => $currentUser->email,
                    'role' => $currentUser->role,
                    'ip_address' => request()->ip()
                ])
            ]);
        }

        $req = PrivacyHelper::filterPII($req, $currentUser);

        return view('admin.sports-league.show', compact('req'));
    }

    /**
     * Update the status of sports registration.
     */
    public function updateStatus(Request $request, $id, $status): RedirectResponse
    {
        if (!in_array($status, ['approved', 'declined', 'review'])) {
            return back()->with('error', 'Invalid status state.');
        }

        $req = SportsRegistration::findOrFail($id);
        $oldStatus = $req->status;
        $req->status = $status;
        $req->remarks = $request->input('remarks', $req->remarks);
        $req->processed_by = auth()->id();
        $req->save();

        // Send Notification & Email
        $user = User::where('email', $req->email)->first();
        if ($user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Sports Request Status: ' . ucfirst($status),
                'message' => 'Your sports registration has been marked as ' . $status . '.',
                'url' => route('profile.my-requests')
            ]);
        }

        try {
            Mail::to($req->email)->send(new StatusChangedMail($req));
        } catch (\Exception $e) {
            // Silently swallow
        }

        // Audit Log
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'status_updated',
            'subject_type' => get_class($req),
            'subject_id' => $req->id,
            'payload' => json_encode([
                'old_status' => $oldStatus,
                'new_status' => $status,
                'processed_by' => auth()->user()->email
            ])
        ]);

        return back()->with('success', 'Registration status updated to ' . ucfirst($status));
    }

    public function destroy(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $req = SportsRegistration::findOrFail($id);
        $req->delete();

        return redirect()->route('admin.sports-league.index')->with('success', 'Registration removed successfully.');
    }
}
