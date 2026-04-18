<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#242424]">Bulk Account Creation</h2>
                <p class="text-xs font-semibold text-gray-400 mt-1 uppercase tracking-tight">Chart of Accounts • Batch Import</p>
            </div>
            <a href="{{ route('admin.coa.index') }}" class="inline-flex items-center gap-2 px-5 py-2 border border-[#E0E0E0] text-[10px] font-bold text-[#424242] uppercase rounded-[4px] hover:bg-[#F0F0F0] transition tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Accounts
            </a>
        </div>
    </x-slot>

    <div x-data="bulkCoa()" class="max-w-6xl mx-auto space-y-12">
        
        {{-- Clone Utility --}}
        <div class="card-premium p-8 bg-[#242424] text-white overflow-hidden relative">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
            </div>
            <div class="relative z-10">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-2 h-8 bg-[#0F6CBD] rounded-full"></div>
                    <div>
                        <h3 class="text-lg font-black uppercase tracking-widest">Protocol Replication</h3>
                        <p class="text-[10px] text-[#0F6CBD] font-bold uppercase tracking-[0.2em] mt-1">Deep Clone Account Framework</p>
                    </div>
                </div>

                <form action="{{ route('admin.coa.clone') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    @csrf
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Source Director (Template)</label>
                        <select name="source_director_id" class="w-full bg-[#1A1A1A] border-white/10 text-white rounded-[4px] text-xs font-bold py-2.5 focus:border-[#0F6CBD] transition" required>
                            <option value="">Select Source...</option>
                            @foreach($directors as $director)
                                <option value="{{ $director->id }}">{{ $director->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-center md:pb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#0F6CBD] opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Target Director (Recipient)</label>
                        <select name="target_director_id" class="w-full bg-[#1A1A1A] border-white/10 text-white rounded-[4px] text-xs font-bold py-2.5 focus:border-[#0F6CBD] transition" required>
                            <option value="">Select Target...</option>
                            @foreach($directors as $director)
                                <option value="{{ $director->id }}">{{ $director->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <button type="submit" 
                                @click.prevent="$dispatch('open-confirm', { 
                                    title: 'Framework Replication', 
                                    message: 'This will copy ALL account categories from the source director to the target. Continue?', 
                                    confirmText: 'Execute Clone ⚡',
                                    action: () => $el.form.submit() 
                                })"
                                class="w-full py-3 bg-[#0F6CBD] text-white text-[10px] font-black uppercase tracking-widest rounded-[4px] hover:bg-[#115EA3] transition shadow-xl shadow-blue-500/10">
                            Execute Framework Replication ⚡
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 card-premium border-l-4 border-red-400 bg-red-50 p-4">
                <p class="text-xs font-bold text-red-700 mb-2 uppercase tracking-wider">Validation Errors</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="text-xs text-red-600">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.coa.bulk-store') }}" @submit="prepareSubmit">
            @csrf

            <div class="card-premium p-8 bg-white space-y-8">

                {{-- Section Header --}}
                <div class="flex items-center space-x-2 pb-4 border-b border-[#F0F0F0]">
                    <div class="w-1.5 h-6 bg-[#0F6CBD] rounded-full"></div>
                    <h3 class="text-xs font-bold text-[#242424] uppercase tracking-widest">Director Assignment</h3>
                </div>

                {{-- Director selector (applies to ALL rows) --}}
                <div class="max-w-sm">
                    <x-input-label for="director_id" value="Assign All to Director" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                    <select id="director_id" name="director_id" class="fluent-input fluent-select w-full font-bold">
                        <option value="">— Global (Shared by all directors) —</option>
                        @foreach($directors as $director)
                            <option value="{{ $director->id }}" {{ old('director_id') == $director->id ? 'selected' : '' }}>
                                {{ $director->name }}{{ $director->department ? ' ('.$director->department.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[9px] text-gray-400 italic">All rows below will be assigned to this director.</p>
                    <x-input-error :messages="$errors->get('director_id')" class="mt-2" />
                </div>

                {{-- Smart Paste Utility --}}
                <div x-data="{ 
                        manualInput: '', 
                        processList() {
                            if (!this.manualInput.trim()) return;
                            const names = this.manualInput.split('\n').map(n => n.trim()).filter(n => n.length > 0);
                            names.forEach(name => {
                                // Add to rows if space remains
                                if (rows.length < 50) {
                                    // Simple code generation: EXP- + first 8 chars of name (alphanumeric only)
                                    const safeName = name.replace(/[^a-z0-9]/gi, '').toUpperCase().substring(0, 8);
                                    const code = (document.getElementById('type_all')?.value === 'income' ? 'INC-' : 'EXP-') + safeName + '-' + Math.floor(Math.random() * 1000);
                                    
                                    // Check if rows already has an empty row, reuse it if it's the only one
                                    if (rows.length === 1 && !rows[0].name && !rows[0].code) {
                                        rows[0].name = name;
                                        rows[0].code = code;
                                        rows[0].type = document.getElementById('type_all')?.value || 'expense';
                                    } else {
                                        rows.push({
                                            id: nextId++,
                                            code: code,
                                            name: name,
                                            type: document.getElementById('type_all')?.value || 'expense',
                                            description: '',
                                            budget_limit: ''
                                        });
                                    }
                                }
                            });
                            this.manualInput = '';
                            $dispatch('open-confirm', { title: 'Success', message: `Added ${names.length} rows from list.` });
                        }
                    }" class="bg-[#FAFAFA] border border-dashed border-[#E0E0E0] p-6 rounded-[4px] space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-[10px] font-black text-[#242424] uppercase tracking-widest">Smart Paste Entry</h4>
                            <p class="text-[9px] text-gray-400 font-bold uppercase mt-1">Paste a list of names (one per line) to auto-populate the grid</p>
                        </div>
                        <select id="type_all" class="text-[9px] font-bold border-[#E0E0E0] rounded-[3px] bg-white px-2 py-1">
                            <option value="expense">As Expenses</option>
                            <option value="income">As Income</option>
                        </select>
                    </div>
                    <textarea 
                        x-model="manualInput"
                        class="fluent-input w-full h-24 text-[11px] font-semibold" 
                        placeholder="Travel & Accommodation&#10;Office Supplies&#10;Marketing Materials..."></textarea>
                    <button type="button" @click="processList" class="px-4 py-2 bg-white border border-[#0F6CBD] text-[#0F6CBD] text-[9px] font-black uppercase rounded-[3px] hover:bg-[#0F6CBD] hover:text-white transition">
                        Integrate List into Batch
                    </button>
                </div>

                <div>
                    <div class="flex items-center space-x-2 pb-4 border-b border-[#F0F0F0] mb-4">
                        <div class="w-1.5 h-6 bg-[#0F6CBD] rounded-full"></div>
                        <h3 class="text-xs font-bold text-[#242424] uppercase tracking-widest">Account Heads</h3>
                        <span class="ml-auto text-[10px] text-gray-400" x-text="`${rows.length} row${rows.length === 1 ? '' : 's'}`"></span>
                    </div>

                    {{-- Table Header --}}
                    <div class="hidden md:grid grid-cols-12 gap-2 mb-2 px-2">
                        <div class="col-span-2 text-[9px] font-bold text-gray-400 uppercase tracking-widest">#&nbsp; Code</div>
                        <div class="col-span-3 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Name / Designation</div>
                        <div class="col-span-2 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Type</div>
                        <div class="col-span-3 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Description</div>
                        <div class="col-span-1 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Budget</div>
                        <div class="col-span-1"></div>
                    </div>

                    {{-- Dynamic Rows --}}
                    <div class="space-y-2" id="rows-container">
                        <template x-for="(row, index) in rows" :key="row.id">
                            <div class="grid grid-cols-12 gap-2 p-2 rounded-[4px] border border-[#F0F0F0] hover:border-[#C7E0F4] hover:bg-[#FAFCFF] transition group">

                                {{-- Row number + Code --}}
                                <div class="col-span-12 md:col-span-2 flex items-center gap-2">
                                    <span class="text-[10px] text-gray-400 font-bold w-5 text-right flex-shrink-0" x-text="index + 1"></span>
                                    <input
                                        type="text"
                                        :name="`accounts[${index}][code]`"
                                        x-model="row.code"
                                        class="fluent-input w-full font-mono uppercase text-xs"
                                        placeholder="EXP-001"
                                        required
                                    >
                                </div>

                                {{-- Name --}}
                                <div class="col-span-12 md:col-span-3">
                                    <input
                                        type="text"
                                        :name="`accounts[${index}][name]`"
                                        x-model="row.name"
                                        class="fluent-input w-full text-xs"
                                        placeholder="e.g., Travel & Accommodation"
                                        required
                                    >
                                </div>

                                {{-- Type --}}
                                <div class="col-span-6 md:col-span-2">
                                    <select
                                        :name="`accounts[${index}][type]`"
                                        x-model="row.type"
                                        class="fluent-input fluent-select w-full font-bold text-xs"
                                    >
                                        <option value="expense">Expense</option>
                                        <option value="income">Income</option>
                                    </select>
                                </div>

                                {{-- Description --}}
                                <div class="col-span-12 md:col-span-3">
                                    <input
                                        type="text"
                                        :name="`accounts[${index}][description]`"
                                        x-model="row.description"
                                        class="fluent-input w-full text-xs"
                                        placeholder="Optional note..."
                                    >
                                </div>

                                {{-- Budget --}}
                                <div class="col-span-6 md:col-span-1">
                                    <input
                                        type="number"
                                        :name="`accounts[${index}][budget_limit]`"
                                        x-model="row.budget_limit"
                                        class="fluent-input w-full text-xs"
                                        placeholder="0"
                                        min="0"
                                        step="1"
                                    >
                                </div>

                                {{-- Remove --}}
                                <div class="col-span-12 md:col-span-1 flex items-center justify-center">
                                    <button
                                        type="button"
                                        @click="removeRow(row.id)"
                                        x-show="rows.length > 1"
                                        class="text-gray-300 hover:text-red-500 transition p-1 rounded group-hover:text-gray-400"
                                        title="Remove row"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Add Row Button --}}
                    <div class="mt-4 flex items-center gap-4">
                        <button
                            type="button"
                            @click="addRow"
                            :disabled="rows.length >= 50"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-dashed border-[#0F6CBD] text-[#0F6CBD] text-[10px] font-bold uppercase tracking-widest rounded-[4px] hover:bg-[#EFF6FC] transition disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            Add Row
                        </button>
                        <span class="text-[10px] text-gray-400" x-show="rows.length >= 50">Maximum 50 rows per batch.</span>
                        <span class="text-[10px] text-gray-400" x-show="rows.length < 50" x-text="`${50 - rows.length} rows remaining`"></span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-[#F0F0F0]">
                    <a href="{{ route('admin.coa.index') }}" class="px-6 py-2 border border-[#E0E0E0] text-[10px] font-bold text-[#424242] uppercase rounded-[4px] hover:bg-[#F0F0F0] transition tracking-widest">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-2 bg-[#0F6CBD] text-white text-[10px] font-bold uppercase rounded-[4px] hover:bg-[#115EA3] transition tracking-widest shadow-lg shadow-blue-500/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="`Commit ${rows.length} Account${rows.length === 1 ? '' : 's'}`"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function bulkCoa() {
            return {
                nextId: 2,
                rows: [
                    { id: 1, code: '', name: '', type: 'expense', description: '', budget_limit: '' }
                ],

                addRow() {
                    if (this.rows.length >= 50) return;
                    this.rows.push({
                        id: this.nextId++,
                        code: '',
                        name: '',
                        type: 'expense',
                        description: '',
                        budget_limit: ''
                    });
                    // Focus the new row's code field after render
                    this.$nextTick(() => {
                        const inputs = document.querySelectorAll('input[placeholder="EXP-001"]');
                        if (inputs.length) inputs[inputs.length - 1].focus();
                    });
                },

                removeRow(id) {
                    if (this.rows.length <= 1) return;
                    this.rows = this.rows.filter(r => r.id !== id);
                },

                prepareSubmit() {
                    // Trim all code fields to uppercase before submit
                    this.rows.forEach(r => {
                        r.code = r.code.trim().toUpperCase();
                    });
                }
            };
        }
    </script>
</x-app-layout>
