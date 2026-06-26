@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-1">
    
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
        title="Sports Registration" 
        subtitle="Apply for local leagues, sports activities, and tournament registrations organized by SK Namayan." 
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
@endsection
