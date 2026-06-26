@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-1">
    
    @php
        $genderOptions = [
            'Male' => 'Male',
            'Female' => 'Female',
            'Prefer not to say' => 'Prefer not to say'
        ];
    @endphp

    <x-form-card 
        title="Pabili Medicine Services" 
        subtitle="Request essential medicine purchasing support and delivery services to your home." 
        action="{{ route('forms.medicine.store') }}"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-form-input label="Requestor First Name" name="requestor_first_name" required="true" />
            <x-form-input label="Requestor Last Name" name="requestor_last_name" required="true" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-form-input label="Requestor Age" name="requestor_age" type="number" min="0" max="120" required="true" />
            <x-form-select label="Requestor Gender" name="requestor_gender" required="true" :options="$genderOptions" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-form-input label="Email Address" name="email" type="email" required="true" />
            <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" />
        </div>

        <x-form-input label="Complete Delivery Address" name="complete_address" type="textarea" required="true" placeholder="Enter house number, street, barangay, and landmark..." />

        <div class="pt-4">
            <button type="submit" class="btn-primary w-full">Submit Medicine Request</button>
        </div>
    </x-form-card>

</div>
@endsection
