@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 font-sans" x-data="{
    step: 1,
    partOfOrg: '0',
    isPwd: '0',

    validateStep(s) {
        const fields = document.querySelectorAll(`#step-${s} [required]`);
        let valid = true;
        fields.forEach(field => {
            if (!field.value || !field.checkValidity()) {
                field.reportValidity();
                valid = false;
            }
        });
        return valid;
    },
    
    nextStep() {
        if (this.validateStep(this.step)) {
            if (this.step < 4) {
                this.step++;
            }
        }
    },

    prevStep() {
        if (this.step > 1) {
            this.step--;
        }
    }
}">

    <!-- Page Header -->
    <div class="mb-8 pb-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-xs font-black tracking-widest text-[#1e40af] uppercase block">Youth Portal</span>
            <h1 class="text-2xl font-black text-slate-800 font-display uppercase tracking-tight mt-1">Katipunan ng Kabataan Self Profiling</h1>
            <p class="text-xs text-slate-500 mt-1">Please complete all steps of the KK Profiling registry form to verify your residency and citizen status.</p>
        </div>
        <a href="{{ route('profile.my-requests') }}" class="btn-outline text-xs py-2 px-4 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition cursor-pointer font-bold inline-block shrink-0 text-center">
            &larr; Return to Portal
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        
        <form id="profileForm" method="POST" action="{{ route('profile.profiling.store') }}" class="p-6 md:p-8 space-y-6">
            @csrf

            <!-- Step Indicator / Progress Bar -->
            <div class="border-b border-slate-100 pb-5">
                <div class="flex items-center justify-between text-xs font-semibold text-slate-400 select-none max-w-xl mx-auto">
                    <!-- Step 1 Indicator -->
                    <div class="flex flex-col items-center relative transition duration-300" :class="step >= 1 ? 'text-[#1e40af]' : 'text-slate-400'">
                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300"
                             :class="step >= 1 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-450'">1</div>
                        <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Consent</span>
                    </div>
                    <div class="flex-1 border-t-2 mx-4 transition duration-300" :class="step >= 2 ? 'border-[#1e40af]' : 'border-slate-200'"></div>

                    <!-- Step 2 Indicator -->
                    <div class="flex flex-col items-center relative transition duration-300" :class="step >= 2 ? 'text-[#1e40af]' : 'text-slate-400'">
                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300"
                             :class="step >= 2 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-450'">2</div>
                        <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Details</span>
                    </div>
                    <div class="flex-1 border-t-2 mx-4 transition duration-300" :class="step >= 3 ? 'border-[#1e40af]' : 'border-slate-200'"></div>

                    <!-- Step 3 Indicator -->
                    <div class="flex flex-col items-center relative transition duration-300" :class="step >= 3 ? 'text-[#1e40af]' : 'text-slate-400'">
                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300"
                             :class="step >= 3 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-450'">3</div>
                        <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Affiliations</span>
                    </div>
                    <div class="flex-1 border-t-2 mx-4 transition duration-300" :class="step >= 4 ? 'border-[#1e40af]' : 'border-slate-200'"></div>

                    <!-- Step 4 Indicator -->
                    <div class="flex flex-col items-center relative transition duration-300" :class="step >= 4 ? 'text-[#1e40af]' : 'text-slate-400'">
                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300"
                             :class="step >= 4 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-450'">4</div>
                        <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Inclusivity</span>
                    </div>
                </div>
            </div>

            <!-- STEP 1: Data Privacy Consent -->
            <div x-show="step === 1" id="step-1" class="space-y-4">
                <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 pb-2">1. Informed Data Privacy Consent</h3>
                <div class="p-6 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-655 leading-relaxed space-y-4 font-medium shadow-inner">
                    <p class="font-black text-slate-800 text-[13px] tracking-tight">Sangguniang Kabataan of Barangay Namayan - Data Privacy Notice & Consent Agreement</p>
                    <p>In accordance with <strong>Republic Act No. 10173</strong> (the <strong>Data Privacy Act of 2012</strong>), the Sangguniang Kabataan Council of Barangay Namayan hereby informs you of the protocols regarding your personal data:</p>
                    
                    <div class="space-y-3 pl-3 border-l-2 border-[#1e40af]/30">
                        <p><strong>1. Collection and Usage:</strong> We collect personal, demographic, educational, voter, and inclusivity information. This data will be processed and used solely for the Katipunan ng Kabataan profiling registry, youth services programming, community assistance targeting, and official reports to the National Youth Commission (NYC) and the Department of the Interior and Local Government (DILG).</p>
                        <p><strong>2. Storage and Security:</strong> Your data is transmitted over secure channels (HTTPS) and encrypted at rest in our systems. Only authorized SK officials have access to review or process database records.</p>
                        <p><strong>3. Rights of the Data Subject:</strong> You have the right to access, update, correct, or request deletion of your information from our database at any time by contacting the SK Secretariat.</p>
                    </div>

                    <p class="text-slate-505 text-[11px] leading-tight">By checking the box below, you signify that you are at least 15 years of age and voluntarily give your consent to these terms.</p>
                </div>
                <div class="mt-4 flex items-start">
                    <div class="flex items-center h-5">
                        <input id="consent_checkbox" name="consent_given" type="checkbox" value="1" required class="focus:ring-[#1e40af] h-4 w-4 text-[#1e40af] border-slate-350 rounded cursor-pointer">
                    </div>
                    <div class="ml-3 text-xs">
                        <label for="consent_checkbox" class="font-bold text-slate-700 cursor-pointer select-none">I have read and understood the Data Privacy Consent Notice and hereby give my voluntary consent to the collection, processing, use, and storage of my personal data for SK profiling purposes.</label>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Personal Details -->
            <div x-show="step === 2" id="step-2" class="space-y-4" x-cloak>
                <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 pb-2">1. Personal Information</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Surname <span class="text-rose-500">*</span></label>
                        <input type="text" name="surname" value="{{ old('surname') }}" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. Dela Cruz">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">First Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', explode(' ', $user->name)[0] ?? '') }}" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. Juan">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. Santiago">
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Suffix (Ext.)</label>
                        <select name="ext" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl">
                            <option value="">None</option>
                            <option value="Jr.">Jr.</option>
                            <option value="Sr.">Sr.</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Age <span class="text-rose-500">*</span></label>
                        <input type="number" name="age" value="{{ old('age') }}" min="15" max="30" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="15 to 30">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Sex <span class="text-rose-500">*</span></label>
                        <select name="sex" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl">
                            <option value="">Select Sex</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Gender Identity</label>
                        <input type="text" name="gender" value="{{ old('gender') }}" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. LGBTQIA+">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Date of Birth <span class="text-rose-500">*</span></label>
                        <input type="date" name="dob" value="{{ old('dob') }}" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Civil Status <span class="text-rose-500">*</span></label>
                        <select name="civil_status" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl">
                            <option value="">Select Civil Status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Divorced">Divorced</option>
                            <option value="Separated">Separated</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Youth Classification <span class="text-rose-500">*</span></label>
                        <select name="youth_classification" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl">
                            <option value="">Select Classification</option>
                            <option value="ISY">In-School Youth (ISY)</option>
                            <option value="OSY">Out-of-School Youth (OSY)</option>
                            <option value="WY">Working Youth (WY)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Purok (Barangay Namayan) <span class="text-rose-500">*</span></label>
                        <select name="purok_id" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl">
                            <option value="">Select Purok</option>
                            @foreach($puroks as $purok)
                                <option value="{{ $purok->id }}">
                                    {{ $purok->purok_name }} {{ $purok->street_name ? '('.$purok->street_name.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Street Address</label>
                        <input type="text" name="street_address" value="{{ old('street_address') }}" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. 594 J.P Rizal Street">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Contact Number <span class="text-rose-500">*</span></label>
                        <input type="text" name="contact_number" value="{{ old('contact_number') }}" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. 09171234567">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ $user->email }}" disabled class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed" placeholder="e.g. citizen@namayan.local">
                    </div>
                </div>
            </div>

            <!-- STEP 3: Affiliations -->
            <div x-show="step === 3" id="step-3" class="space-y-6" x-cloak>
                <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 pb-2">2. Affiliations & Voter Info</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-slate-700">
                    <!-- Registered SK Voter -->
                    <div class="space-y-2">
                        <span class="block font-bold text-slate-500 uppercase text-[10px]">Registered SK Voter? <span class="text-rose-500">*</span></span>
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="registered_sk_voter" value="1" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="registered_sk_voter" value="0" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                    </div>

                    <!-- Registered National Voter -->
                    <div class="space-y-2">
                        <span class="block font-bold text-slate-500 uppercase text-[10px]">Registered National Voter? <span class="text-rose-500">*</span></span>
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="registered_national_voter" value="1" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="registered_national_voter" value="0" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                    </div>

                    <!-- Attended KK Assembly -->
                    <div class="space-y-2">
                        <span class="block font-bold text-slate-500 uppercase text-[10px]">Attended KK Assembly? <span class="text-rose-500">*</span></span>
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="attended_kk_assembly" value="1" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="attended_kk_assembly" value="0" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                    </div>

                    <!-- Part of Youth Org -->
                    <div class="space-y-2">
                        <span class="block font-bold text-slate-500 uppercase text-[10px]">Part of Youth Organization? <span class="text-rose-500">*</span></span>
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="part_of_youth_org" value="1" x-model="partOfOrg" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="part_of_youth_org" value="0" x-model="partOfOrg" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Youth Org Name (Conditional if Yes) -->
                <div x-show="partOfOrg === '1'" x-transition class="space-y-2" x-cloak>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Name of Youth Organization <span class="text-rose-500">*</span></label>
                    <input type="text" name="youth_org_name" value="{{ old('youth_org_name') }}" :required="partOfOrg === '1'" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. Sangguniang Kabataan Movement">
                </div>

                <!-- Interested in joining (Conditional if No) -->
                <div x-show="partOfOrg === '0'" x-transition class="space-y-2 text-xs text-slate-700" x-cloak>
                    <span class="block font-bold text-slate-500 uppercase text-[10px]">Interested in joining a Youth Organization? <span class="text-rose-500">*</span></span>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="interested_in_joining" value="1" :required="partOfOrg === '0'" class="text-[#1e40af] focus:ring-[#1e40af]">
                            <span class="ml-2">Yes</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="interested_in_joining" value="0" :required="partOfOrg === '0'" class="text-[#1e40af] focus:ring-[#1e40af]">
                            <span class="ml-2">No</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- STEP 4: Inclusivity & Education -->
            <div x-show="step === 4" id="step-4" class="space-y-6" x-cloak>
                <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 pb-2">3. Inclusivity & Education</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-slate-700">
                    <!-- Part of LGBTQIA -->
                    <div class="space-y-2">
                        <span class="block font-bold text-slate-500 uppercase text-[10px]">Part of the LGBTQIA+ Community? <span class="text-rose-500">*</span></span>
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="part_of_lgbtqia" value="1" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="part_of_lgbtqia" value="0" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                    </div>

                    <!-- Person With Disability (PWD) -->
                    <div class="space-y-2">
                        <span class="block font-bold text-slate-500 uppercase text-[10px]">Person with Disability (PWD)? <span class="text-rose-500">*</span></span>
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="pwd" value="1" x-model="isPwd" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="pwd" value="0" x-model="isPwd" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Disability Name (Conditional if Yes) -->
                <div x-show="isPwd === '1'" x-transition class="space-y-2" x-cloak>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Registered Disability <span class="text-rose-500">*</span></label>
                    <input type="text" name="registered_disability" value="{{ old('registered_disability') }}" :required="isPwd === '1'" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. Visual Impairment">
                </div>

                <!-- Highest Educational Attainment -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Highest Educational Attainment <span class="text-rose-500">*</span></label>
                    <input type="text" name="highest_educational_attainment" value="{{ old('highest_educational_attainment') }}" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. College Graduate, 2nd Year College">
                </div>
            </div>
            
            <!-- Navigation Footer -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between shrink-0">
                <button type="button" 
                        x-show="step > 1" 
                        @click="prevStep()" 
                        class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold rounded-xl transition text-xs uppercase tracking-wider select-none cursor-pointer">
                    &larr; Back
                </button>
                <div x-show="step === 1" class="w-10"></div> <!-- Placeholder -->
                
                <button type="button" 
                        x-show="step < 4" 
                        @click="nextStep()" 
                        class="btn-primary text-xs uppercase tracking-wider py-2 px-5 font-bold rounded-xl select-none cursor-pointer">
                    Next &rarr;
                </button>
                
                <button type="submit" 
                        x-show="step === 4" 
                        class="btn-success text-xs uppercase tracking-wider py-2 px-5 font-bold rounded-xl select-none cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white border border-transparent transition active:scale-95 shadow-sm">
                    Submit Profile
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
