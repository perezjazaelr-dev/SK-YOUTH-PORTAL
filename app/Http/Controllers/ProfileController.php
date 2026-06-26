<?php

namespace App\Http\Controllers;

use App\Models\HealthRequest;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use App\Models\SportsRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's submissions across all 4 request models.
     */
    public function myRequests(Request $request)
    {
        if ($request->user()->canAccessDashboard()) {
            return redirect()->route('dashboard.index');
        }

        $email = auth()->user()->email;

        $health = HealthRequest::with('processedBy')->where('email', $email)->get()->map(function ($item) {
            $item->type_label = 'Health Consultation';
            $item->type_prefix = 'HEA';
            $item->icon = '🏥';
            $item->icon_name = 'health';
            $item->detail = 'Appointment: ' . $item->preferred_date->format('M d, Y') . ' @ ' . $item->preferred_time;
            return $item;
        });

        $medicine = MedicineRequest::with('processedBy')->where('email', $email)->get()->map(function ($item) {
            $item->type_label = 'Pabili Medicine Services';
            $item->type_prefix = 'MED';
            $item->icon = '💊';
            $item->icon_name = 'medicine';
            $item->detail = 'Address: ' . $item->complete_address;
            return $item;
        });

        $silid = SilidKarununganRequest::with('processedBy')->where('email', $email)->get()->map(function ($item) {
            $item->type_label = 'Silid Karunungan Booking';
            $item->type_prefix = 'SIL';
            $item->icon = '📚';
            $item->icon_name = 'education';
            $item->detail = 'Schedule: ' . $item->preferred_date->format('M d, Y') . ' @ ' . $item->preferred_time;
            return $item;
        });

        $sports = SportsRegistration::with('processedBy')->where('email', $email)->get()->map(function ($item) {
            $item->type_label = 'Sports Registration';
            $item->type_prefix = 'SPO';
            $item->icon = '⚽';
            $item->icon_name = 'sports';
            $item->detail = 'Sport: ' . $item->sport . ' (Team: ' . ($item->team_name ?? 'None') . ')';
            return $item;
        });

        $results = collect()
            ->concat($health)
            ->concat($medicine)
            ->concat($silid)
            ->concat($sports)
            ->sortByDesc('created_at');

        // Calculate counts
        $total = $results->count();
        $pending = $results->whereIn('status', ['pending', 'review'])->count();
        $approved = $results->where('status', 'approved')->count();
        $declined = $results->where('status', 'declined')->count();

        // Check if KK Profile exists
        $hasProfile = \App\Models\KkProfile::where('email', $email)->exists();

        return view('profile.my-requests', compact('results', 'total', 'pending', 'approved', 'declined', 'hasProfile'));
    }

    /**
     * Display the user's profile edit forms.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's basic profile information.
     */
    public function updateInfo(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile information updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Password changed successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Account deleted successfully.');
    }
}
