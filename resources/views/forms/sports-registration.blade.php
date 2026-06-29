@extends('layouts.app')

@section('content')
<div class="flex-1 flex flex-col min-h-0 bg-slate-50 dark:bg-slate-950 font-sans">

    <!-- Page Header -->
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#1e3a8a] text-white shrink-0">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-[max(1.5rem,env(safe-area-inset-top))] pb-8 md:py-12 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="max-w-2xl space-y-2.5">
                <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5 min-w-0">
                    <a href="{{ route('landing') }}" class="hover:text-white active:scale-95 shrink-0">Home</a>
                    <span aria-hidden="true" class="shrink-0">/</span>
                    <span class="text-white truncate" aria-current="page">Sports League</span>
                </nav>
                <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[9px] font-black uppercase tracking-widest">Active Programs</span>
                <h1 class="text-2xl sm:text-3xl font-black font-display uppercase tracking-tight leading-tight">Sports Registration</h1>
                <p class="text-sm text-slate-300 leading-relaxed">Apply for local leagues, sports activities, and tournament registrations organized by SK Namayan.</p>
            </div>
            <a href="{{ route('profile.my-requests') }}" class="inline-flex items-center min-h-10 px-4 bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-xs uppercase tracking-wider rounded-xl active:scale-95 transition-all text-white shrink-0 self-start sm:self-center">
                &larr; Return to Portal
            </a>
        </div>
    </section>

    <!-- Main Content Container -->
    <div class="max-w-3xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-10 flex-1 flex flex-col">
        @php
            $genderOptions = [
                'Male' => 'Male',
                'Female' => 'Female',
                'Prefer not to say' => 'Prefer not to say'
            ];
            $sportOptions = [
                'Basketball' => 'Basketball',
                'Volleyball' => 'Volleyball',
                'Football' => 'Football',
                'Badminton' => 'Badminton',
                'Table Tennis' => 'Table Tennis',
                'Swimming' => 'Swimming',
                'Athletics' => 'Athletics',
                'Boxing' => 'Boxing',
                'Martial Arts' => 'Martial Arts',
                'Esports' => 'Esports',
                'Other' => 'Other'
            ];
        @endphp

        <x-form-card 
            title="Registration Form" 
            subtitle="Please provide accurate personal and team details for the tournament registry." 
            action="{{ route('forms.sports.store') }}"
        >
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-form-input label="First Name" name="first_name" required="true" />
                <x-form-input label="Last Name" name="last_name" required="true" />
                <x-form-input label="Middle Name" name="middle_name" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Age (10–30)" name="age" type="number" min="10" max="30" required="true" />
                <x-form-select label="Gender" name="gender" required="true" :options="$genderOptions" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Email Address" name="email" type="email" required="true" />
                <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-select label="Choose Sport" name="sport" required="true" :options="$sportOptions" />
                <x-form-input label="Team Name (Optional)" name="team_name" placeholder="Leave empty if signing up individually" />
            </div>

            <x-form-input label="Preferred Event Date" name="event_date" type="date" min="{{ date('Y-m-d') }}" required="true" />

            <x-form-input label="Remarks / Queries" name="remarks" type="textarea" placeholder="Add any special requirements, team configurations, or general remarks..." />

            <div class="pt-4">
                <button type="submit" class="btn-primary w-full">Submit Sports Registration</button>
            </div>
        </x-form-card>
    </div>
</div>
@endsection
