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
    @endphp

    <x-form-card 
        title="Silid Karunungan Booking" 
        subtitle="Book studying slots at local research library facilities with internet access." 
        action="{{ route('forms.silid.store') }}"
    >
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-form-input label="Requestor First Name" name="requestor_first_name" required="true" />
            <x-form-input label="Requestor Last Name" name="requestor_last_name" required="true" />
            <x-form-input label="Requestor Middle Name" name="requestor_middle_name" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-form-input label="Requestor Age" name="requestor_age" type="number" min="0" max="120" required="true" />
            <x-form-input label="Email Address" name="email" type="email" required="true" />
            <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-form-input label="Preferred Date" name="preferred_date" type="date" min="{{ date('Y-m-d') }}" required="true" />
            <x-form-select label="Preferred Time Slot" name="preferred_time" required="true" :options="$timeOptions" />
        </div>

        <div class="pt-4">
            <button type="submit" class="btn-primary w-full bg-[#1e40af]">Submit Booking Request</button>
        </div>
    </x-form-card>

</div>
@endsection
