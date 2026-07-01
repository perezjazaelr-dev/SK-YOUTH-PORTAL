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
use App\Models\Notification;
use App\Models\User;
use App\Models\Initiative;

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
        $divisionFilter = $request->input('division');
        
        $limit = $request->input('limit', 10);
        if (!in_array($limit, [10, 15, 25, 50, 100])) {
            $limit = 10;
        }

        $initiatives = Initiative::all();

        $cutoff = now()->subDays(30);

        // Fetch counts for widgets and notification indicators (excluding approved requests older than 30 days)
        $healthStats = [
            'total' => HealthRequest::where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->count(),
            'pending' => HealthRequest::whereIn('status', ['pending', 'review'])->count(),
            'approved' => HealthRequest::where('status', 'approved')->where('updated_at', '>=', $cutoff)->count(),
            'declined' => HealthRequest::where('status', 'declined')->count(),
        ];
        $medicineStats = [
            'total' => MedicineRequest::where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->count(),
            'pending' => MedicineRequest::whereIn('status', ['pending', 'review'])->count(),
            'approved' => MedicineRequest::where('status', 'approved')->where('updated_at', '>=', $cutoff)->count(),
            'declined' => MedicineRequest::where('status', 'declined')->count(),
        ];
        $silidStats = [
            'total' => SilidKarununganRequest::where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->count(),
            'pending' => SilidKarununganRequest::whereIn('status', ['pending', 'review'])->count(),
            'approved' => SilidKarununganRequest::where('status', 'approved')->where('updated_at', '>=', $cutoff)->count(),
            'declined' => SilidKarununganRequest::where('status', 'declined')->count(),
        ];
        $sportsStats = [
            'total' => SportsRegistration::count(),
            'pending' => SportsRegistration::whereIn('status', ['pending', 'review'])->count(),
            'approved' => SportsRegistration::where('status', 'approved')->count(),
            'declined' => SportsRegistration::where('status', 'declined')->count(),
        ];

        // Attach stats dynamically to each initiative
        foreach ($initiatives as $init) {
            if ($init->form_route) {
                $init->stats = match ($init->form_route) {
                    'forms.health.create', 'forms.mental-health.create' => $healthStats,
                    'forms.medicine.create' => $medicineStats,
                    'forms.silid.create' => $silidStats,
                    'forms.sports.create' => $sportsStats,
                    default => [
                        'total' => \App\Models\CustomRequest::where('initiative_id', $init->id)->where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->count(),
                        'pending' => \App\Models\CustomRequest::where('initiative_id', $init->id)->whereIn('status', ['pending', 'review'])->count(),
                        'approved' => \App\Models\CustomRequest::where('initiative_id', $init->id)->where('status', 'approved')->where('updated_at', '>=', $cutoff)->count(),
                        'declined' => \App\Models\CustomRequest::where('initiative_id', $init->id)->where('status', 'declined')->count(),
                    ]
                };
            } else {
                $init->stats = [
                    'total' => \App\Models\CustomRequest::where('initiative_id', $init->id)->where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->count(),
                    'pending' => \App\Models\CustomRequest::where('initiative_id', $init->id)->whereIn('status', ['pending', 'review'])->count(),
                    'approved' => \App\Models\CustomRequest::where('initiative_id', $init->id)->where('status', 'approved')->where('updated_at', '>=', $cutoff)->count(),
                    'declined' => \App\Models\CustomRequest::where('initiative_id', $init->id)->where('status', 'declined')->count(),
                ];
            }
        }

        $activeCustomRequestsCount = \App\Models\CustomRequest::where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->count();
        $activeCustomRequestsPending = \App\Models\CustomRequest::whereIn('status', ['pending', 'review'])->count();
        $activeCustomRequestsApproved = \App\Models\CustomRequest::where('status', 'approved')->where('updated_at', '>=', $cutoff)->count();
        $activeCustomRequestsDeclined = \App\Models\CustomRequest::where('status', 'declined')->count();

        $allStats = [
            'total' => $healthStats['total'] + $medicineStats['total'] + $silidStats['total'] + $activeCustomRequestsCount,
            'pending' => $healthStats['pending'] + $medicineStats['pending'] + $silidStats['pending'] + $activeCustomRequestsPending,
            'approved' => $healthStats['approved'] + $medicineStats['approved'] + $silidStats['approved'] + $activeCustomRequestsApproved,
            'declined' => $healthStats['declined'] + $medicineStats['declined'] + $silidStats['declined'] + $activeCustomRequestsDeclined,
        ];

        $archivedInitiatives = Initiative::onlyTrashed()->get();
        $archivedInitiativeIds = $archivedInitiatives->pluck('id')->toArray();
        $archivedRoutes = $archivedInitiatives->pluck('form_route')->filter()->toArray();

        $archivedStats = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'declined' => 0,
        ];

        // Custom requests in archived initiatives OR approved older than 30 days from active initiatives
        $customArchQuery = \App\Models\CustomRequest::where(function($q) use ($archivedInitiativeIds, $cutoff) {
            $q->whereIn('initiative_id', $archivedInitiativeIds)
              ->orWhere(function($sub) use ($cutoff) {
                  $sub->where('status', 'approved')->where('updated_at', '<', $cutoff);
              });
        });
        $archivedStats['total'] += $customArchQuery->count();
        $archivedStats['pending'] += (clone $customArchQuery)->whereIn('status', ['pending', 'review'])->count();
        $archivedStats['approved'] += (clone $customArchQuery)->where('status', 'approved')->count();
        $archivedStats['declined'] += (clone $customArchQuery)->where('status', 'declined')->count();

        // For predefined forms: if archived, count all. If active, count only approved older than 30 days.
        $predefinedRoutes = ['forms.health.create', 'forms.mental-health.create', 'forms.medicine.create', 'forms.silid.create', 'forms.sports.create'];
        foreach ($predefinedRoutes as $route) {
            $isArchived = in_array($route, $archivedRoutes);
            $query = match ($route) {
                'forms.health.create', 'forms.mental-health.create' => HealthRequest::query(),
                'forms.medicine.create' => MedicineRequest::query(),
                'forms.silid.create' => SilidKarununganRequest::query(),
                'forms.sports.create' => SportsRegistration::query(),
            };

            if (!$isArchived) {
                if ($route === 'forms.sports.create') {
                    $query->whereRaw('1 = 0'); // No auto-archived records for sports
                } else {
                    $query->where('status', 'approved')->where('updated_at', '<', $cutoff);
                }
            }

            $archivedStats['total'] += (clone $query)->count();
            $archivedStats['pending'] += (clone $query)->whereIn('status', ['pending', 'review'])->count();
            $archivedStats['approved'] += (clone $query)->where('status', 'approved')->count();
            $archivedStats['declined'] += (clone $query)->where('status', 'declined')->count();
        }

        $activeTab = request()->query('tab', 'all');
        $activeInitiative = null;

        if ($activeTab === 'all') {
            // Get all merged queries (excluding approved requests older than 30 days)
            $healthAll = HealthRequest::where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->with('processedBy')->latest();
            $medicineAll = MedicineRequest::where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->with('processedBy')->latest();
            $silidAll = SilidKarununganRequest::where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->with('processedBy')->latest();
            $sportsAll = SportsRegistration::with('processedBy')->latest();
            $customAll = \App\Models\CustomRequest::where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->with(['processedBy', 'initiative'])->latest();

            // Filter each query before fetching for performance
            if ($search) {
                $healthAll->where(function($q) use ($search) { $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"); });
                $medicineAll->where(function($q) use ($search) { $q->where('requestor_first_name', 'like', "%{$search}%")->orWhere('requestor_last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"); });
                $silidAll->where(function($q) use ($search) { $q->where('requestor_first_name', 'like', "%{$search}%")->orWhere('requestor_last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"); });
                $sportsAll->where(function($q) use ($search) { $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"); });
                $customAll->where(function($q) use ($search) { $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"); });
            }
            if ($status && in_array($status, ['pending', 'approved', 'declined'])) {
                $st = ($status === 'pending') ? ['pending', 'review'] : [$status];
                $healthAll->whereIn('status', $st);
                $medicineAll->whereIn('status', $st);
                $silidAll->whereIn('status', $st);
                $sportsAll->whereIn('status', $st);
                $customAll->whereIn('status', $st);
            }
            if ($yearFilter) {
                $healthAll->whereYear('created_at', $yearFilter);
                $medicineAll->whereYear('created_at', $yearFilter);
                $silidAll->whereYear('created_at', $yearFilter);
                $sportsAll->whereYear('created_at', $yearFilter);
                $customAll->whereYear('created_at', $yearFilter);
            }

            // Fetch and map
            $healthMapped = $healthAll->get()->map(function($item) {
                $item->type = 'health';
                $item->type_name = 'Health Consultation';
                $item->display_name = $item->last_name . ', ' . $item->first_name;
                return $item;
            });
            $medicineMapped = $medicineAll->get()->map(function($item) {
                $item->type = 'medicine';
                $item->type_name = 'Pabili Medicine Services';
                $item->display_name = $item->requestor_last_name . ', ' . $item->requestor_first_name;
                $item->first_name = $item->requestor_first_name;
                $item->last_name = $item->requestor_last_name;
                return $item;
            });
            $silidMapped = $silidAll->get()->map(function($item) {
                $item->type = 'silid';
                $item->type_name = 'Silid Karunungan Booking';
                $item->display_name = $item->requestor_last_name . ', ' . $item->requestor_first_name;
                $item->first_name = $item->requestor_first_name;
                $item->last_name = $item->requestor_last_name;
                return $item;
            });
            $customMapped = $customAll->get()->map(function($item) {
                $item->type = 'custom';
                $item->type_name = $item->initiative ? $item->initiative->title : 'Custom Request';
                $item->display_name = $item->last_name . ', ' . $item->first_name;
                return $item;
            });

            $merged = collect()->concat($healthMapped)->concat($medicineMapped)->concat($silidMapped)->concat($customMapped)->sortByDesc('created_at');

            $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('cPage');
            $currentItems = $merged->slice(($currentPage - 1) * $limit, $limit)->values();
            $paginatedRequests = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $merged->count(),
                $limit,
                $currentPage,
                ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'cPage']
            );
        } elseif ($activeTab === 'archive') {
            $archivedInitiatives = Initiative::onlyTrashed()->get();
            $archivedInitiativeIds = $archivedInitiatives->pluck('id')->toArray();
            $archivedRoutes = $archivedInitiatives->pluck('form_route')->filter()->toArray();

            // Load and merge all queries belonging to archived forms or auto-archived requests
            $healthArch = HealthRequest::where(function($q) use ($archivedRoutes, $cutoff) {
                if (in_array('forms.health.create', $archivedRoutes) || in_array('forms.mental-health.create', $archivedRoutes)) {
                    // Get all
                } else {
                    $q->where('status', 'approved')->where('updated_at', '<', $cutoff);
                }
            })->with('processedBy')->latest();

            $medicineArch = MedicineRequest::where(function($q) use ($archivedRoutes, $cutoff) {
                if (in_array('forms.medicine.create', $archivedRoutes)) {
                    // Get all
                } else {
                    $q->where('status', 'approved')->where('updated_at', '<', $cutoff);
                }
            })->with('processedBy')->latest();

            $silidArch = SilidKarununganRequest::where(function($q) use ($archivedRoutes, $cutoff) {
                if (in_array('forms.silid.create', $archivedRoutes)) {
                    // Get all
                } else {
                    $q->where('status', 'approved')->where('updated_at', '<', $cutoff);
                }
            })->with('processedBy')->latest();

            $sportsArch = SportsRegistration::where(function($q) use ($archivedRoutes, $cutoff) {
                if (in_array('forms.sports.create', $archivedRoutes)) {
                    // Get all
                } else {
                    $q->whereRaw('1 = 0');
                }
            })->with('processedBy')->latest();

            $customArch = \App\Models\CustomRequest::where(function($q) use ($archivedInitiativeIds, $cutoff) {
                $q->whereIn('initiative_id', $archivedInitiativeIds)
                  ->orWhere(function($sub) use ($cutoff) {
                      $sub->where('status', 'approved')->where('updated_at', '<', $cutoff);
                  });
            })->with(['processedBy', 'initiative'])->latest();

            // Filter each query
            if ($search) {
                $healthArch->where(function($q) use ($search) { $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"); });
                $medicineArch->where(function($q) use ($search) { $q->where('requestor_first_name', 'like', "%{$search}%")->orWhere('requestor_last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"); });
                $silidArch->where(function($q) use ($search) { $q->where('requestor_first_name', 'like', "%{$search}%")->orWhere('requestor_last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"); });
                $sportsArch->where(function($q) use ($search) { $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"); });
                $customArch->where(function($q) use ($search) { $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"); });
            }
            if ($status && in_array($status, ['pending', 'approved', 'declined'])) {
                $st = ($status === 'pending') ? ['pending', 'review'] : [$status];
                $healthArch->whereIn('status', $st);
                $medicineArch->whereIn('status', $st);
                $silidArch->whereIn('status', $st);
                $sportsArch->whereIn('status', $st);
                $customArch->whereIn('status', $st);
            }
            if ($yearFilter) {
                $healthArch->whereYear('created_at', $yearFilter);
                $medicineArch->whereYear('created_at', $yearFilter);
                $silidArch->whereYear('created_at', $yearFilter);
                $sportsArch->whereYear('created_at', $yearFilter);
                $customArch->whereYear('created_at', $yearFilter);
            }

            // Fetch and map only if the respective routes are archived or auto-archived
            $items = collect();
            
            $items = $items->merge($healthArch->get()->map(function($item) use ($cutoff) {
                $item->type = 'health';
                $isAuto = ($item->status === 'approved' && $item->updated_at->lt($cutoff));
                $item->type_name = 'Health Consultation' . ($isAuto ? ' (Auto-Archived)' : ' (Archived Form)');
                $item->display_name = $item->last_name . ', ' . $item->first_name;
                return $item;
            }));

            $items = $items->merge($medicineArch->get()->map(function($item) use ($cutoff) {
                $item->type = 'medicine';
                $isAuto = ($item->status === 'approved' && $item->updated_at->lt($cutoff));
                $item->type_name = 'Medicine Delivery' . ($isAuto ? ' (Auto-Archived)' : ' (Archived Form)');
                $item->display_name = $item->requestor_last_name . ', ' . $item->requestor_first_name;
                $item->first_name = $item->requestor_first_name;
                $item->last_name = $item->requestor_last_name;
                return $item;
            }));

            $items = $items->merge($silidArch->get()->map(function($item) use ($cutoff) {
                $item->type = 'silid';
                $isAuto = ($item->status === 'approved' && $item->updated_at->lt($cutoff));
                $item->type_name = 'Study Space' . ($isAuto ? ' (Auto-Archived)' : ' (Archived Form)');
                $item->display_name = $item->requestor_last_name . ', ' . $item->requestor_first_name;
                $item->first_name = $item->requestor_first_name;
                $item->last_name = $item->requestor_last_name;
                return $item;
            }));

            $items = $items->merge($sportsArch->get()->map(function($item) use ($cutoff) {
                $item->type = 'sports';
                $isAuto = ($item->status === 'approved' && $item->updated_at->lt($cutoff));
                $item->type_name = 'Sports League' . ($isAuto ? ' (Auto-Archived)' : ' (Archived Form)');
                $item->display_name = $item->last_name . ', ' . $item->first_name;
                return $item;
            }));

            $items = $items->merge($customArch->get()->map(function($item) use ($cutoff) {
                $item->type = 'custom';
                $isAuto = ($item->status === 'approved' && $item->updated_at->lt($cutoff));
                $title = $item->initiative ? $item->initiative->title : 'Custom Form';
                $item->type_name = $title . ($isAuto ? ' (Auto-Archived)' : ' (Archived Form)');
                $item->display_name = $item->last_name . ', ' . $item->first_name;
                return $item;
            }));

            // Sort and manually paginate
            $sortedItems = $items->sortByDesc('created_at');
            $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('cPage');
            $slice = $sortedItems->slice(($currentPage - 1) * $limit, $limit);
            $paginatedRequests = new \Illuminate\Pagination\LengthAwarePaginator(
                $slice->values(),
                $sortedItems->count(),
                $limit,
                $currentPage,
                ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'cPage']
            );
            $paginatedRequests->withQueryString();
        } else {
            $id = str_replace('init_', '', $activeTab);
            $activeInitiative = Initiative::findOrFail($id);

            if ($activeInitiative->form_route) {
                $query = match ($activeInitiative->form_route) {
                    'forms.health.create', 'forms.mental-health.create' => HealthRequest::where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->with('processedBy')->latest(),
                    'forms.medicine.create' => MedicineRequest::where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->with('processedBy')->latest(),
                    'forms.silid.create' => SilidKarununganRequest::where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->with('processedBy')->latest(),
                    'forms.sports.create' => SportsRegistration::with('processedBy')->latest(),
                    default => \App\Models\CustomRequest::where('initiative_id', $activeInitiative->id)->where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->with('processedBy')->latest()
                };
            } else {
                $query = \App\Models\CustomRequest::where('initiative_id', $activeInitiative->id)->where(fn($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))->with('processedBy')->latest();
            }

            if ($search) {
                if ($activeInitiative->form_route === 'forms.medicine.create' || $activeInitiative->form_route === 'forms.silid.create') {
                    $query->where(function($q) use ($search) {
                        $q->where('requestor_first_name', 'like', "%{$search}%")->orWhere('requestor_last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                    });
                } else {
                    $query->where(function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                    });
                }
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

            if ($activeInitiative->form_route === 'forms.sports.create' && $divisionFilter) {
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

            $paginatedRequests = $query->paginate($limit, ['*'], 'cPage')->withQueryString();
        }

        // Mask PII on paginated records based on role clearance
        $currentUser = auth()->user();
        $paginatedRequests->getCollection()->transform(fn($item) => PrivacyHelper::filterPII($item, $currentUser));

        // Extract distinct request years database-agnostically
        $driver = \DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $healthYears = HealthRequest::selectRaw("strftime('%Y', created_at) as year")->distinct()->pluck('year')->toArray();
            $medicineYears = MedicineRequest::selectRaw("strftime('%Y', created_at) as year")->distinct()->pluck('year')->toArray();
            $silidYears = SilidKarununganRequest::selectRaw("strftime('%Y', created_at) as year")->distinct()->pluck('year')->toArray();
            $sportsYears = SportsRegistration::selectRaw("strftime('%Y', created_at) as year")->distinct()->pluck('year')->toArray();
            $customYears = \App\Models\CustomRequest::selectRaw("strftime('%Y', created_at) as year")->distinct()->pluck('year')->toArray();
        } else {
            $healthYears = HealthRequest::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray();
            $medicineYears = MedicineRequest::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray();
            $silidYears = SilidKarununganRequest::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray();
            $sportsYears = SportsRegistration::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray();
            $customYears = \App\Models\CustomRequest::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray();
        }

        $years = array_unique(array_merge($healthYears, $medicineYears, $silidYears, $sportsYears, $customYears));
        rsort($years);
        if (empty($years)) {
            $years = [date('Y')];
        }

        return view('dashboard.requests-index', compact(
            'initiatives',
            'paginatedRequests',
            'activeTab',
            'activeInitiative',
            'search',
            'status',
            'allStats',
            'archivedStats',
            'yearFilter',
            'divisionFilter',
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

            // Notify user
            $user = User::where('email', $rawRequestModel->email)->first();
            if ($user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Request Under Review',
                    'message' => 'Your ' . $this->resolveTypeName($type) . ' request is now under review.',
                    'url' => route('profile.my-requests')
                ]);
            }

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

        // Notify user
        $user = User::where('email', $requestModel->email)->first();
        if ($user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Request Status: ' . ucfirst($status),
                'message' => 'Your ' . $this->resolveTypeName($type) . ' request status has been updated to ' . ucfirst($status) . '.',
                'url' => route('profile.my-requests')
            ]);
        }

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
            'custom' => \App\Models\CustomRequest::class,
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
            'custom' => 'Custom Request',
            default => 'General Request'
        };
    }
}
