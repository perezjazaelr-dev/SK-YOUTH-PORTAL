<?php

namespace App\Http\Controllers;

use App\Helpers\PrivacyHelper;
use App\Mail\StatusChangedMail;
use App\Models\ActivityLog;
use App\Models\HealthRequest;
use App\Models\KkProfile;
use App\Models\MedicineRequest;
use App\Models\Purok;
use App\Models\SilidKarununganRequest;
use App\Models\SportsRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard overview.
     */
    public function index(Request $request): View
    {
        // KK Profiling Demographics counts for stats widgets
        $totalYouth = KkProfile::count();
        $totalIsy = KkProfile::where('youth_classification', 'ISY')->count();
        $totalOsy = KkProfile::where('youth_classification', 'OSY')->count();
        $totalWy = KkProfile::where('youth_classification', 'WY')->count();
        $totalSkVoters = KkProfile::where('registered_sk_voter', true)->count();

        // Query Purok youth population statistics
        $puroksData = Purok::withCount('kkProfiles')->orderBy('purok_name')->get();

        $chartData = $puroksData->map(function ($purok) {
            return [
                'purok' => $purok->purok_name,
                'count' => $purok->kk_profiles_count,
            ];
        })->toArray();

        // Youth Classification Distribution
        $classificationDistribution = [
            'isy' => $totalIsy,
            'osy' => $totalOsy,
            'wy' => $totalWy,
        ];

        // Accomplished service requests (status = approved) counts by program
        $accomplishedByProgram = [
            'health' => HealthRequest::where('status', 'approved')->count(),
            'medicine' => MedicineRequest::where('status', 'approved')->count(),
            'silid' => SilidKarununganRequest::where('status', 'approved')->count(),
            'sports' => SportsRegistration::where('status', 'approved')->count(),
        ];

        // Accomplishment Trends over Time (Monthly counts of accomplished/approved requests over the last 6 months)
        $accomplishmentTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $year = $date->year;
            $month = $date->month;
            $label = $date->format('M Y');

            $healthCount = HealthRequest::where('status', 'approved')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $medicineCount = MedicineRequest::where('status', 'approved')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $silidCount = SilidKarununganRequest::where('status', 'approved')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $sportsCount = SportsRegistration::where('status', 'approved')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $accomplishmentTrends[] = [
                'label' => $label,
                'count' => $healthCount + $medicineCount + $silidCount + $sportsCount,
            ];
        }

        return view('dashboard.index', compact(
            'totalYouth',
            'totalIsy',
            'totalOsy',
            'totalWy',
            'totalSkVoters',
            'chartData',
            'classificationDistribution',
            'accomplishedByProgram',
            'accomplishmentTrends'
        ));
    }

    /**
     * Show citizen requests list.
     */
    public function requestsIndex(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status'); // Filter status: pending, approved, declined
        $yearFilter = $request->input('year');
        
        $limit = $request->input('limit', 10);
        if (!in_array($limit, [10, 15, 25, 50, 100])) {
            $limit = 10;
        }

        // Query Health Requests
        $healthQuery = HealthRequest::with('processedBy')->latest();
        if ($search) {
            $healthQuery->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($status && in_array($status, ['pending', 'approved', 'declined'])) {
            if ($status === 'pending') {
                $healthQuery->whereIn('status', ['pending', 'review']);
            } else {
                $healthQuery->where('status', $status);
            }
        }
        if ($yearFilter) {
            $healthQuery->whereYear('created_at', $yearFilter);
        }
        $healthRequests = $healthQuery->paginate($limit, ['*'], 'hPage')->withQueryString();

        // Query Medicine Requests
        $medicineQuery = MedicineRequest::with('processedBy')->latest();
        if ($search) {
            $medicineQuery->where(function($q) use ($search) {
                $q->where('requestor_first_name', 'like', "%{$search}%")
                  ->orWhere('requestor_last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($status && in_array($status, ['pending', 'approved', 'declined'])) {
            if ($status === 'pending') {
                $medicineQuery->whereIn('status', ['pending', 'review']);
            } else {
                $medicineQuery->where('status', $status);
            }
        }
        if ($yearFilter) {
            $medicineQuery->whereYear('created_at', $yearFilter);
        }
        $medicineRequests = $medicineQuery->paginate($limit, ['*'], 'mPage')->withQueryString();

        // Query Silid Bookings
        $silidQuery = SilidKarununganRequest::with('processedBy')->latest();
        if ($search) {
            $silidQuery->where(function($q) use ($search) {
                $q->where('requestor_first_name', 'like', "%{$search}%")
                  ->orWhere('requestor_last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($status && in_array($status, ['pending', 'approved', 'declined'])) {
            if ($status === 'pending') {
                $silidQuery->whereIn('status', ['pending', 'review']);
            } else {
                $silidQuery->where('status', $status);
            }
        }
        if ($yearFilter) {
            $silidQuery->whereYear('created_at', $yearFilter);
        }
        $silidRequests = $silidQuery->paginate($limit, ['*'], 'sPage')->withQueryString();

        // Query Sports Registrations
        $sportsQuery = SportsRegistration::with('processedBy')->latest();
        if ($search) {
            $sportsQuery->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($status && in_array($status, ['pending', 'approved', 'declined'])) {
            if ($status === 'pending') {
                $sportsQuery->whereIn('status', ['pending', 'review']);
            } else {
                $sportsQuery->where('status', $status);
            }
        }
        if ($yearFilter) {
            $sportsQuery->whereYear('created_at', $yearFilter);
        }
        $sportsRequests = $sportsQuery->paginate($limit, ['*'], 'spPage')->withQueryString();

        // Fetch counts for widgets and notification indicators
        $healthStats = [
            'total' => HealthRequest::count(),
            'pending' => HealthRequest::whereIn('status', ['pending', 'review'])->count(),
            'approved' => HealthRequest::where('status', 'approved')->count(),
            'declined' => HealthRequest::where('status', 'declined')->count(),
        ];
        $medicineStats = [
            'total' => MedicineRequest::count(),
            'pending' => MedicineRequest::whereIn('status', ['pending', 'review'])->count(),
            'approved' => MedicineRequest::where('status', 'approved')->count(),
            'declined' => MedicineRequest::where('status', 'declined')->count(),
        ];
        $silidStats = [
            'total' => SilidKarununganRequest::count(),
            'pending' => SilidKarununganRequest::whereIn('status', ['pending', 'review'])->count(),
            'approved' => SilidKarununganRequest::where('status', 'approved')->count(),
            'declined' => SilidKarununganRequest::where('status', 'declined')->count(),
        ];
        $sportsStats = [
            'total' => SportsRegistration::count(),
            'pending' => SportsRegistration::whereIn('status', ['pending', 'review'])->count(),
            'approved' => SportsRegistration::where('status', 'approved')->count(),
            'declined' => SportsRegistration::where('status', 'declined')->count(),
        ];

        // Mask PII on paginated records based on role clearance
        $currentUser = auth()->user();
        $healthRequests->getCollection()->transform(fn($item) => PrivacyHelper::filterPII($item, $currentUser));
        $medicineRequests->getCollection()->transform(fn($item) => PrivacyHelper::filterPII($item, $currentUser));
        $silidRequests->getCollection()->transform(fn($item) => PrivacyHelper::filterPII($item, $currentUser));
        $sportsRequests->getCollection()->transform(fn($item) => PrivacyHelper::filterPII($item, $currentUser));

        // Extract distinct request years database-agnostically
        $driver = \DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $healthYears = HealthRequest::selectRaw("strftime('%Y', created_at) as year")->distinct()->pluck('year')->toArray();
            $medicineYears = MedicineRequest::selectRaw("strftime('%Y', created_at) as year")->distinct()->pluck('year')->toArray();
            $silidYears = SilidKarununganRequest::selectRaw("strftime('%Y', created_at) as year")->distinct()->pluck('year')->toArray();
            $sportsYears = SportsRegistration::selectRaw("strftime('%Y', created_at) as year")->distinct()->pluck('year')->toArray();
        } else {
            $healthYears = HealthRequest::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray();
            $medicineYears = MedicineRequest::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray();
            $silidYears = SilidKarununganRequest::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray();
            $sportsYears = SportsRegistration::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray();
        }

        $years = array_unique(array_merge($healthYears, $medicineYears, $silidYears, $sportsYears));
        rsort($years);
        if (empty($years)) {
            $years = [date('Y')];
        }

        return view('dashboard.requests-index', compact(
            'healthRequests',
            'medicineRequests',
            'silidRequests',
            'sportsRequests',
            'search',
            'status',
            'healthStats',
            'medicineStats',
            'silidStats',
            'sportsStats',
            'yearFilter',
            'limit',
            'years'
        ));
    }

    /**
     * Show individual request details.
     */
    public function show($type, $id): View
    {
        $rawRequestModel = $this->resolveModel($type, $id)->load('processedBy');

        if ($rawRequestModel->status === 'pending') {
            $rawRequestModel->status = 'review';
            $rawRequestModel->save();

            // Fire status email silently on success
            try {
                Mail::to($rawRequestModel->email)->send(new StatusChangedMail($rawRequestModel));
            } catch (\Exception $e) {
                // Silently swallow mail exceptions so HTTP transaction succeeds
            }
        }

        $basename = class_basename($rawRequestModel);
        
        $currentUser = auth()->user();

        // If user has DPO/Admin PII clearance, audit log that they read the unmasked data
        if ($currentUser && $currentUser->hasPiiClearance() && strtolower($currentUser->email) !== strtolower($rawRequestModel->email)) {
            ActivityLog::create([
                'user_id' => $currentUser->id,
                'action' => 'pii_accessed',
                'subject_type' => get_class($rawRequestModel),
                'subject_id' => $rawRequestModel->id,
                'payload' => json_encode([
                    'accessed_by' => $currentUser->email,
                    'role' => $currentUser->role,
                    'ip_address' => request()->ip()
                ]),
                'ip_address' => request()->ip()
            ]);
        }

        // Apply PII Masking/Redaction
        $requestModel = PrivacyHelper::filterPII($rawRequestModel, $currentUser);

        // Fetch activity logs for this request subject
        $logs = ActivityLog::where('subject_type', get_class($rawRequestModel))
            ->where('subject_id', $id)
            ->with('user')
            ->oldest()
            ->get();

        return view('dashboard.show', [
            'req' => $requestModel,
            'type' => $type,
            'typeName' => $this->resolveTypeName($type),
            'basename' => $basename,
            'logs' => $logs
        ]);
    }

    /**
     * Update request status.
     */
    public function updateStatus($type, $id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'review', 'approved', 'declined'])) {
            return back()->with('error', 'Invalid status state.');
        }

        $requestModel = $this->resolveModel($type, $id);
        $requestModel->status = $status;
        if (in_array($status, ['approved', 'declined'])) {
            $requestModel->processed_by = auth()->id();
        } else {
            $requestModel->processed_by = null;
        }
        $requestModel->save();

        // Fire status email silently on success
        try {
            Mail::to($requestModel->email)->send(new StatusChangedMail($requestModel));
        } catch (\Exception $e) {
            // Silently swallow mail exceptions so HTTP transaction succeeds
        }

        return redirect()->route('dashboard.requests.show', [$type, $id])
            ->with('success', 'Request status updated successfully to ' . ucfirst($status) . '.');
    }

    /**
     * Resolve model class based on type identifier.
     */
    private function resolveModel($type, $id)
    {
        $class = match($type) {
            'health' => HealthRequest::class,
            'medicine' => MedicineRequest::class,
            'silid' => SilidKarununganRequest::class,
            'sports' => SportsRegistration::class,
            default => abort(404, 'Invalid request type.')
        };

        return $class::findOrFail($id);
    }

    /**
     * Resolve type visual names.
     */
    private function resolveTypeName($type): string
    {
        return match($type) {
            'health' => 'Health Consultation',
            'medicine' => 'Pabili Medicine Services',
            'silid' => 'Silid Karunungan Booking',
            'sports' => 'Sports Registration',
            default => 'General Request'
        };
    }
}
