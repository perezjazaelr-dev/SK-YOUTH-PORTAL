@extends('layouts.app')

@section('content')
<div class="flex-1 bg-[#f8fafc] dark:bg-slate-950 font-sans min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
            <a href="{{ route('landing') }}" class="hover:text-[#1e40af] dark:hover:text-blue-400">Home</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 dark:text-slate-100">Sports League Registration</span>
        </div>

        <!-- Page Header -->
        <div class="space-y-2">
            <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-widest">
                SK Namayan Sports Portal
            </span>
            <h1 class="text-3xl font-black text-slate-800 dark:text-white font-display uppercase tracking-tight leading-tight">
                Sports League Registration
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed">
                Join our active league divisions. Select a sports league division below, fill in the customized dynamic requirements built by the administrative council, and submit your registration application.
            </p>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/30 rounded-2xl flex items-start gap-3 shadow-sm">
                <span class="text-emerald-500 text-lg">✓</span>
                <div>
                    <h4 class="text-sm font-bold text-emerald-800 dark:text-emerald-400">Success!</h4>
                    <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            
            <!-- Division Selector (Col span 4) -->
            <div class="md:col-span-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-[#1e40af] dark:text-blue-400">Active Divisions</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Select a division to load its form</p>
                </div>

                <div class="flex flex-col gap-2">
                    @forelse($divisions as $div)
                        <a href="{{ route('forms.sports.create', ['division_id' => $div->id]) }}" 
                           class="flex flex-col p-3 rounded-2xl border transition-all text-left group {{ $selectedForm && $selectedForm->id === $div->id ? 'border-[#1e40af] bg-blue-50/30 dark:bg-blue-950/20' : 'border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40' }}">
                            <span class="text-xs font-extrabold text-slate-800 dark:text-slate-100 group-hover:text-[#1e40af] dark:group-hover:text-blue-400 transition-colors">
                                {{ $div->division_name }}
                            </span>
                            <span class="text-[9px] font-mono text-slate-400 dark:text-slate-500 uppercase mt-1">
                                {{ $div->league->name }} ({{ $div->league->sport }})
                            </span>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">No active divisions found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Dynamic Registration Form (Col span 8) -->
            <div class="md:col-span-8 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm relative">
                
                @if($selectedForm)
                    <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-[9px] font-black uppercase tracking-wider text-[#1e40af] dark:text-blue-400">Selected Division Form</span>
                        <h2 class="text-lg font-black text-slate-800 dark:text-white uppercase leading-snug">{{ $selectedForm->division_name }}</h2>
                        <p class="text-xs text-slate-400 mt-1 font-mono uppercase">{{ $selectedForm->league->name }}</p>
                        @if($selectedForm->description)
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">{{ $selectedForm->description }}</p>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('forms.sports.store') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        <input type="hidden" name="registration_form_id" value="{{ $selectedForm->id }}">

                        <div class="space-y-4">
                            @foreach($selectedForm->formFields as $field)
                                @php
                                    $fieldName = $field->field_name;
                                    $oldVal = old("answers.{$fieldName}");
                                @endphp
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5">
                                        {{ $field->field_label }}
                                        @if($field->is_required)
                                            <span class="text-rose-500">*</span>
                                        @endif
                                    </label>

                                    <!-- Render Text Input -->
                                    @if($field->field_type === 'text')
                                        <input type="text" 
                                               name="answers[{{ $fieldName }}]" 
                                               value="{{ $oldVal }}"
                                               placeholder="{{ $field->placeholder }}"
                                               {{ $field->is_required ? 'required' : '' }}
                                               class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition">
                                    
                                    <!-- Render Textarea -->
                                    @elseif($field->field_type === 'textarea')
                                        <textarea name="answers[{{ $fieldName }}]" 
                                                  placeholder="{{ $field->placeholder }}"
                                                  rows="3"
                                                  {{ $field->is_required ? 'required' : '' }}
                                                  class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition resize-none">{{ $oldVal }}</textarea>
                                    
                                    <!-- Render Number Input -->
                                    @elseif($field->field_type === 'number')
                                        <input type="number" 
                                               name="answers[{{ $fieldName }}]" 
                                               value="{{ $oldVal }}"
                                               placeholder="{{ $field->placeholder }}"
                                               {{ $field->is_required ? 'required' : '' }}
                                               class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition">
                                    
                                    <!-- Render Date Picker -->
                                    @elseif($field->field_type === 'date')
                                        <input type="date" 
                                               name="answers[{{ $fieldName }}]" 
                                               value="{{ $oldVal }}"
                                               {{ $field->is_required ? 'required' : '' }}
                                               class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition">
                                    
                                    <!-- Render Dropdown Select -->
                                    @elseif($field->field_type === 'select')
                                        <select name="answers[{{ $fieldName }}]" 
                                                {{ $field->is_required ? 'required' : '' }}
                                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition">
                                            <option value="">{{ $field->placeholder ?: 'Select an option' }}</option>
                                            @if($field->options)
                                                @foreach($field->options as $option)
                                                    <option value="{{ $option }}" {{ $oldVal === $option ? 'selected' : '' }}>{{ $option }}</option>
                                                @endforeach
                                            @endif
                                        </select>

                                    <!-- Render Radio Choice -->
                                    @elseif($field->field_type === 'radio')
                                        <div class="flex flex-col gap-2 mt-1">
                                            @if($field->options)
                                                @foreach($field->options as $option)
                                                    <label class="inline-flex items-center gap-2 text-xs text-slate-700 dark:text-slate-350 cursor-pointer">
                                                        <input type="radio" 
                                                               name="answers[{{ $fieldName }}]" 
                                                               value="{{ $option }}"
                                                               {{ $oldVal === $option ? 'checked' : '' }}
                                                               {{ $field->is_required ? 'required' : '' }}
                                                               class="rounded-full border-slate-300 dark:border-slate-700 text-[#1e40af] focus:ring-0">
                                                        <span>{{ $option }}</span>
                                                    </label>
                                                @endforeach
                                            @endif
                                        </div>

                                    <!-- Render Checkbox Select -->
                                    @elseif($field->field_type === 'checkbox')
                                        <div class="flex flex-col gap-2 mt-1">
                                            @if($field->options)
                                                @foreach($field->options as $option)
                                                    <label class="inline-flex items-center gap-2 text-xs text-slate-700 dark:text-slate-350 cursor-pointer">
                                                        <input type="checkbox" 
                                                               name="answers[{{ $fieldName }}][]" 
                                                               value="{{ $option }}"
                                                               {{ is_array($oldVal) && in_array($option, $oldVal, true) ? 'checked' : '' }}
                                                               class="rounded border-slate-300 dark:border-slate-700 text-[#1e40af] focus:ring-0">
                                                        <span>{{ $option }}</span>
                                                    </label>
                                                @endforeach
                                            @endif
                                        </div>
                                    
                                    <!-- Render File Upload -->
                                    @elseif($field->field_type === 'file')
                                        <input type="file" 
                                               name="answers[{{ $fieldName }}]" 
                                               {{ $field->is_required ? 'required' : '' }}
                                               class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-[#1e40af] dark:file:bg-slate-800 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-slate-750 transition cursor-pointer">
                                    @endif

                                    @error("answers.{$fieldName}")
                                        <p class="text-rose-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button type="submit" class="inline-flex items-center px-6 py-3 rounded-xl bg-[#1e40af] hover:bg-blue-700 text-white text-[11px] font-black uppercase tracking-wider transition active:scale-95 shadow-sm">
                                Submit Registration
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-16 space-y-3">
                        <div class="text-3xl">🏆</div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">No Active Registration Forms</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">There are no registration forms open for public registration currently. Please check back later.</p>
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
