@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-1">
    
    <x-form-card 
        title="{{ $initiative->title }}" 
        subtitle="{{ $initiative->description }}" 
        action="{{ route('forms.custom.store', $initiative->id) }}"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-form-input label="First Name" name="first_name" required="true" value="{{ old('first_name', auth()->user()->first_name ?? '') }}" />
            <x-form-input label="Last Name" name="last_name" required="true" value="{{ old('last_name', auth()->user()->last_name ?? '') }}" />
        </div>

        <div class="grid grid-cols-1 gap-4">
            <x-form-input label="Email Address" name="email" type="email" required="true" value="{{ old('email', auth()->user()->email ?? '') }}" />
        </div>

        @if(!empty($initiative->custom_fields) && is_array($initiative->custom_fields))
            <div class="space-y-4 pt-4 border-t border-slate-100 mt-4">
                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Additional Information Required</span>
                <div class="grid grid-cols-1 gap-4">
                    @foreach($initiative->custom_fields as $field)
                        <x-form-input
                            label="{{ $field['label'] }}"
                            name="custom_fields[{{ $field['name'] }}]"
                            type="{{ $field['type'] ?? 'text' }}"
                            required="{{ ($field['required'] ?? false) ? 'true' : 'false' }}"
                            placeholder="{{ $field['placeholder'] ?? '' }}"
                            value="{{ old('custom_fields.' . $field['name']) }}"
                        />
                    @endforeach
                </div>
            </div>
        @endif

        <div class="pt-4">
            <button type="submit" class="btn-primary w-full">Submit Request</button>
        </div>
    </x-form-card>

</div>
@endsection
