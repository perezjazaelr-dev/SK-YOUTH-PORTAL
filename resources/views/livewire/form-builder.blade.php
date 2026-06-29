<div>
    <!-- Load SortableJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <!-- Hidden Inputs for Traditional Form Submission -->
    @foreach($fields as $index => $field)
        <input type="hidden" name="custom_fields[{{ $index }}][label]" value="{{ $field['label'] }}">
        <input type="hidden" name="custom_fields[{{ $index }}][name]" value="{{ $field['key'] }}">
        <input type="hidden" name="custom_fields[{{ $index }}][type]" value="{{ $field['type'] }}">
        <input type="hidden" name="custom_fields[{{ $index }}][placeholder]" value="{{ $field['placeholder'] ?? '' }}">
        <input type="hidden" name="custom_fields[{{ $index }}][required]" value="{{ $field['required'] ? '1' : '0' }}">
        @if($field['type'] === 'file')
            <input type="hidden" name="custom_fields[{{ $index }}][maxSizeInMB]" value="{{ $field['maxSizeInMB'] ?? 2 }}">
            <input type="hidden" name="custom_fields[{{ $index }}][allowedTypes]" value="{{ is_array($field['allowedTypes']) ? implode(',', $field['allowedTypes']) : $field['allowedTypes'] }}">
        @endif
        @if($field['type'] === 'dropdown')
            <input type="hidden" name="custom_fields[{{ $index }}][options]" value="{{ $field['options'] ?? '' }}">
        @endif
    @endforeach

    <div class="text-slate-800 dark:text-slate-100 space-y-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-5 md:p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-5">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 font-display">Form Fields Configuration</h3>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500">Design custom fields to request from citizens.</p>
                </div>
                <button 
                    type="button" 
                    wire:click="addField" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md hover:shadow-lg active:scale-95 transition-all cursor-pointer flex items-center gap-1"
                >
                    <span>➕</span> Add Field
                </button>
            </div>

            <!-- Empty State -->
            @if(empty($fields))
                <div class="text-center py-10 border border-dashed border-slate-200 dark:border-slate-850 rounded-2xl bg-slate-50/50 dark:bg-slate-950/20 text-xs text-slate-400 dark:text-slate-500">
                    No custom fields configured. Click "Add Field" to begin building your form.
                </div>
            @endif

            <!-- Fields List Container -->
            <div 
                x-data="{
                    initSortable() {
                        Sortable.create(this.$refs.container, {
                            handle: '.drag-handle',
                            animation: 150,
                            ghostClass: 'bg-blue-50/50',
                            onEnd: (evt) => {
                                let orderedIds = Array.from(this.$refs.container.querySelectorAll('[data-id]'))
                                    .map(item => item.getAttribute('data-id'));
                                @this.call('updateFieldOrder', orderedIds);
                            }
                        });
                    }
                }"
                x-init="initSortable"
                x-ref="container"
                class="space-y-4"
            >
                @foreach($fields as $index => $field)
                    <div 
                        data-id="{{ $field['id'] }}" 
                        wire:key="field-card-{{ $field['id'] }}"
                        class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-850 rounded-2xl relative shadow-sm transition hover:shadow-md"
                    >
                        <!-- Drag Handle -->
                        <div class="drag-handle absolute top-3 left-3 cursor-grab active:cursor-grabbing text-slate-400 dark:text-slate-600 p-1.5 hover:bg-white dark:hover:bg-slate-900 border border-transparent hover:border-slate-200 dark:hover:border-slate-800 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 8h16M4 16h16" />
                            </svg>
                        </div>

                        <!-- Delete Button -->
                        <button 
                            type="button" 
                            wire:click="deleteField('{{ $field['id'] }}')" 
                            class="absolute top-3 right-3 text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-white dark:hover:bg-slate-900 border border-transparent hover:border-slate-200 dark:hover:border-slate-800 transition active:scale-90" 
                            title="Remove Field"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>

                        <div class="pl-10 pr-8 space-y-4">
                            <!-- Type Selection & Label -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Field Label (Visible to Users)</label>
                                    <input 
                                        type="text" 
                                        value="{{ $field['label'] }}"
                                        @change="@this.call('updateField', '{{ $field['id'] }}', 'label', $event.target.value)"
                                        placeholder="e.g. Upload Birth Certificate"
                                        class="field text-xs py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:ring-2 focus:ring-blue-500/10 dark:text-white"
                                        required
                                    >
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Field Key (System Identifier)</label>
                                    <input 
                                        type="text" 
                                        value="{{ $field['key'] }}"
                                        @change="@this.call('updateField', '{{ $field['id'] }}', 'key', $event.target.value)"
                                        placeholder="e.g. birth_certificate"
                                        class="field text-xs py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 font-mono text-slate-600 dark:text-slate-350"
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Field Config Options -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Input Field Type</label>
                                    <select 
                                        @change="@this.call('updateField', '{{ $field['id'] }}', 'type', $event.target.value)"
                                        class="field text-xs py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 dark:text-white"
                                    >
                                        <option value="text" @selected($field['type'] === 'text')>Single Line Text</option>
                                        <option value="dropdown" @selected($field['type'] === 'dropdown')>Dropdown Menu</option>
                                        <option value="file" @selected($field['type'] === 'file')>File Upload</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Placeholder/Hint</label>
                                    <input 
                                        type="text" 
                                        value="{{ $field['placeholder'] ?? '' }}"
                                        @change="@this.call('updateField', '{{ $field['id'] }}', 'placeholder', $event.target.value)"
                                        placeholder="e.g. Enter value..."
                                        class="field text-xs py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 dark:text-white"
                                    >
                                </div>
                                <div class="flex items-center pt-4">
                                    <label class="inline-flex items-center text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer select-none">
                                        <input 
                                            type="checkbox" 
                                            @checked($field['required'])
                                            @change="@this.call('updateField', '{{ $field['id'] }}', 'required', $event.target.checked)"
                                            class="rounded border-slate-300 dark:border-slate-800 text-[#1e40af] focus:ring-[#1e40af]/20 mr-2 w-4 h-4"
                                        >
                                        Required Input
                                    </label>
                                </div>
                            </div>

                            <!-- Nested File Upload Configurations -->
                            @if($field['type'] === 'file')
                                <div class="pt-3 border-t border-slate-200/50 dark:border-slate-850 grid grid-cols-1 sm:grid-cols-2 gap-4 animate-fade-in">
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Max Allowed File Size (MB)</label>
                                        <input 
                                            type="number" 
                                            min="1" 
                                            max="100" 
                                            value="{{ $field['maxSizeInMB'] ?? 2 }}"
                                            @change="@this.call('updateField', '{{ $field['id'] }}', 'maxSizeInMB', $event.target.value)"
                                            class="field text-xs py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 dark:text-white"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Allowed Formats (Comma Separated)</label>
                                        <input 
                                            type="text" 
                                            value="{{ is_array($field['allowedTypes']) ? implode(', ', $field['allowedTypes']) : $field['allowedTypes'] }}"
                                            @change="@this.call('updateField', '{{ $field['id'] }}', 'allowedTypes', $event.target.value)"
                                            placeholder="e.g. pdf, png, jpg"
                                            class="field text-xs py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 dark:text-white font-mono"
                                        >
                                    </div>
                                </div>
                            @endif

                            <!-- Nested Dropdown Configurations -->
                            @if($field['type'] === 'dropdown')
                                <div class="pt-3 border-t border-slate-200/50 dark:border-slate-850 animate-fade-in">
                                    <label class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Dropdown Options (Comma Separated)</label>
                                    <input 
                                        type="text" 
                                        value="{{ $field['options'] ?? '' }}"
                                        @change="@this.call('updateField', '{{ $field['id'] }}', 'options', $event.target.value)"
                                        placeholder="e.g. Option 1, Option 2, Option 3"
                                        class="field text-xs py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 dark:text-white"
                                    >
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
