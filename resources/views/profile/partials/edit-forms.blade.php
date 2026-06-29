    <!-- Section 1: Profile Information -->
    <div class="card space-y-5">
        <div>
            <h2 class="text-sm font-bold text-slate-800 font-display uppercase tracking-tight">Profile Information</h2>
            <p class="text-[10px] text-slate-400 mt-0.5">Update your account's profile name and email address.</p>
        </div>

        <form method="POST" action="{{ route('profile.update-info') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="First Name" name="first_name" :value="$user->first_name" required="true" />
                <x-form-input label="Last Name" name="last_name" :value="$user->last_name" required="true" />
            </div>
            <x-form-input label="Email Address" name="email" :value="$user->email" type="email" required="true" />

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-primary text-xs px-5">Save Information</button>
            </div>
        </form>
    </div>

    <!-- Section 2: Change Password -->
    <div class="card space-y-5">
        <div>
            <h2 class="text-sm font-bold text-slate-800 font-display uppercase tracking-tight">Update Password</h2>
            <p class="text-[10px] text-slate-400 mt-0.5">Ensure your account is using a long, random password to stay secure.</p>
        </div>

        <form method="POST" action="{{ route('profile.update-password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <x-form-input label="Current Password" name="current_password" type="password" required="true" />
            <x-form-input label="New Password" name="password" type="password" required="true" />
            <x-form-input label="Confirm New Password" name="password_confirmation" type="password" required="true" />

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-primary text-xs px-5">Change Password</button>
            </div>
        </form>
    </div>

    <!-- Section 3: Danger Zone -->
    <div class="card border-rose-100 bg-rose-50/10 space-y-5" x-data="{ confirming: false }">
        <div>
            <h2 class="text-sm font-bold text-rose-800 font-display uppercase tracking-tight">Danger Zone</h2>
            <p class="text-[10px] text-slate-400 mt-0.5">Permanently delete your citizen account and purge all active records.</p>
        </div>

        <div class="space-y-4">
            @if(Auth::user()->isSuperAdmin())
                <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-xl text-xs flex items-center space-x-2">
                    <span>🛡️</span>
                    <div><strong>Self-deletion is disabled:</strong> Superadmin accounts cannot delete their own profile to prevent locking out the system.</div>
                </div>
            @else
                <p class="text-xs text-slate-500 leading-relaxed">
                    Once your account is deleted, all of its resources and request histories will be permanently deleted. Before proceeding, please download any data or information you wish to retain.
                </p>

                <button type="button" 
                        @click="confirming = true" 
                        x-show="!confirming" 
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm shadow-rose-600/15">
                    Delete Account
                </button>

                <!-- Confirm account delete form -->
                <form method="POST" action="{{ route('profile.destroy') }}" 
                      x-show="confirming" 
                      x-cloak
                      class="space-y-4 p-4 border border-rose-100 bg-white rounded-2xl animate-fade-in">
                    @csrf
                    @method('DELETE')
                    
                    <div>
                        <h3 class="text-xs font-bold text-rose-800 uppercase tracking-wider mb-2">Are you sure you want to delete your account?</h3>
                        <p class="text-[10px] text-slate-400 mb-3">Please enter your account password to confirm permanent deletion.</p>
                        <x-form-input label="Account Password" name="password" type="password" required="true" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" 
                                @click="confirming = false" 
                                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm shadow-rose-600/15">
                            Confirm Permanent Deletion
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
