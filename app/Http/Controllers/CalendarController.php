<?php

namespace App\Http\Controllers;

use App\Models\HealthRequest;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use App\Models\SportsRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    /**
     * Show the master calendar dashboard view.
     */
    public function index(): View
    {
        return view('dashboard.calendar');
    }

    /**
     * Fetch aggregated events list as JSON feed.
     */
    public function events(Request $request): JsonResponse
    {
        $startInput = $request->query('start');
        $endInput = $request->query('end');

        // Parse dates or default to current month range
        $startDate = $startInput ? Carbon::parse($startInput)->startOfDay() : now()->startOfMonth();
        $endDate = $endInput ? Carbon::parse($endInput)->endOfDay() : now()->endOfMonth();

        $events = [];

        // 1. Health Consultation Requests (Blue #2563eb)
        $health = HealthRequest::whereBetween('preferred_date', [$startDate, $endDate])->get();
        foreach ($health as $item) {
            $events[] = [
                'id' => 'health_' . $item->id,
                'title' => '🏥 Health: ' . $item->first_name . ' ' . $item->last_name,
                'start' => $item->preferred_date->format('Y-m-d') . 'T' . ($item->preferred_time ?? '09:00:00'),
                'url' => route('dashboard.requests.show', ['health', $item->id]),
                'backgroundColor' => '#2563eb',
                'borderColor' => '#1d4ed8',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'health',
                    'status' => $item->status,
                    'time' => $item->preferred_time
                ]
            ];
        }

        // 2. Silid Karunungan Booking Requests (Pink #db2777)
        $silid = SilidKarununganRequest::whereBetween('preferred_date', [$startDate, $endDate])->get();
        foreach ($silid as $item) {
            $events[] = [
                'id' => 'silid_' . $item->id,
                'title' => '📚 Silid: ' . $item->requestor_first_name . ' ' . $item->requestor_last_name,
                'start' => $item->preferred_date->format('Y-m-d') . 'T' . ($item->preferred_time ?? '09:00:00'),
                'url' => route('dashboard.requests.show', ['silid', $item->id]),
                'backgroundColor' => '#db2777',
                'borderColor' => '#be185d',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'silid',
                    'status' => $item->status,
                    'time' => $item->preferred_time
                ]
            ];
        }

        // 3. Sports Registrations (Emerald #059669)
        $sports = SportsRegistration::whereBetween('event_date', [$startDate, $endDate])->get();
        foreach ($sports as $item) {
            $events[] = [
                'id' => 'sports_' . $item->id,
                'title' => '⚽ Sports: ' . $item->first_name . ' ' . $item->last_name . ' (' . $item->sport . ')',
                'start' => $item->event_date->format('Y-m-d') . 'T08:00:00',
                'url' => route('dashboard.requests.show', ['sports', $item->id]),
                'backgroundColor' => '#059669',
                'borderColor' => '#047857',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'sports',
                    'status' => $item->status,
                    'sport' => $item->sport,
                    'team' => $item->team_name
                ]
            ];
        }

        // 4. Medicine Delivery Requests (Purple #7c3aed - placed on creation date)
        $medicine = MedicineRequest::whereBetween('created_at', [$startDate, $endDate])->get();
        foreach ($medicine as $item) {
            $events[] = [
                'id' => 'medicine_' . $item->id,
                'title' => '💊 Medicine: ' . $item->requestor_first_name . ' ' . $item->requestor_last_name,
                'start' => $item->created_at->format('Y-m-d\TH:i:s'),
                'url' => route('dashboard.requests.show', ['medicine', $item->id]),
                'backgroundColor' => '#7c3aed',
                'borderColor' => '#6d28d9',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'medicine',
                    'status' => $item->status,
                    'address' => $item->complete_address
                ]
            ];
        }

        return response()->json($events);
    }
}
