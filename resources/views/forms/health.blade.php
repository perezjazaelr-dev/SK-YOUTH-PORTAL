@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-1">
    
    @php
        $timeOptions = [];
        for ($h = 8; $h <= 17; $h++) {
            $formattedH = str_pad($h, 2, '0', STR_PAD_LEFT);
            $timeOptions["$formattedH:00"] = "$formattedH:00";
            if ($h < 17) {
                $timeOptions["$formattedH:30"] = "$formattedH:30";
            }
        }
        $genderOptions = [
            'Male' => 'Male',
            'Female' => 'Female',
            'Prefer not to say' => 'Prefer not to say'
        ];
    @endphp

    <x-form-card 
        title="Health Consultation" 
        subtitle="Apply for free medical guidance or health services from SK Namayan representatives." 
        action="{{ route('forms.health.store') }}"
    >
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-form-input label="First Name" name="first_name" required="true" />
            <x-form-input label="Last Name" name="last_name" required="true" />
            <x-form-input label="Middle Name" name="middle_name" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-form-input label="Age" name="age" type="number" min="0" max="120" required="true" />
            <x-form-select label="Gender" name="gender" required="true" :options="$genderOptions" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-form-input label="Email Address" name="email" type="email" required="true" />
            <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" />
        </div>

        <x-form-input label="Concerns" name="concerns" type="textarea" required="true" placeholder="Detail your symptoms, advice needed, or other medical inquiries..." />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-form-input label="Preferred Date" name="preferred_date" type="date" min="{{ date('Y-m-d') }}" required="true" />
            <x-form-select label="Preferred Time Slot" name="preferred_time" required="true" :options="$timeOptions" />
        </div>

        <div class="pt-4">
            <button type="submit" class="btn-primary w-full">Submit Health Consultation Request</button>
        </div>
    </x-form-card>

</div>
@endsection
