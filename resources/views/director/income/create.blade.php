<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#242424]">New Income Report</h2>
                <p class="text-xs font-semibold text-gray-400 mt-1 uppercase tracking-tight">Revenue Entry Form v4.2</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-2 py-0.5 bg-[#F0F0F0] border border-[#E0E0E0] rounded-[4px] text-[10px] font-bold text-[#424242] uppercase">Status: Drafting</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="incomeForm()">
        <form action="{{ route('director.income.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Director Selection (Admin/Accountant Only) -->
            @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant'))
            <div class="card-premium p-6 bg-[#107C10]/5 border-[#107C10]/20">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 rounded-full bg-[#107C10] flex items-center justify-center text-white shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <div class="flex-grow">
                        <label class="text-[10px] uppercase tracking-widest text-[#107C10] font-black mb-1 block">Assign to Director</label>
                        <select name="director_id" x-model="selectedDirectorId" @change="refreshCategories()" class="fluent-input fluent-select border-[#107C10]/30 focus:border-[#107C10] font-bold text-sm bg-white" required>
                            <option value="">Select Director...</option>
                            @foreach($directors as $dir)
                                <option value="{{ $dir->id }}">{{ $dir->name }} ({{ $dir->email }})</option>
                            @endforeach
                        </select>
                        <p class="text-[9px] text-[#107C10] mt-1 font-bold italic">Verification Mode: Entering revenue on behalf of director.</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Basic Information -->
            <div class="card-premium p-8 bg-white border border-[#E0E0E0]">
                <div class="flex items-center space-x-2 mb-8 pb-4 border-b border-[#F0F0F0]">
                    <div class="w-1.5 h-6 bg-[#107C10] rounded-full"></div>
                    <h3 class="text-xs font-bold text-[#242424] uppercase tracking-widest">General Information</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                    <div class="md:col-span-8">
                        <x-input-label for="title" value="Income Title" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                        <x-text-input id="title" name="title" type="text" :value="old('title')" required placeholder="e.g., Monthly Grant Allocation" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>
                    <div class="md:col-span-4">
                        <x-input-label for="voucher_no" value="Voucher Number" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                        <div class="px-3 py-2 bg-[#F8F9FA] border border-[#107C10]/20 border-dashed rounded-[4px] text-[10px] font-black text-[#107C10] uppercase tracking-widest flex items-center justify-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            <span>[System Assigned]</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-10">
                    <div>
                        <x-input-label value="Reporting Month" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                        <div class="flex items-center space-x-2">
                            <select name="period_month" class="fluent-input fluent-select flex-grow text-[11px] font-bold">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ date("F", mktime(0, 0, 0, $m, 1)) }}</option>
                                @endforeach
                            </select>
                            <select name="period_year" class="fluent-input fluent-select w-24 text-[11px] font-bold">
                                @foreach(range(date('Y')-1, date('Y')+1) as $y)
                                    <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <x-input-label value="Submitted By" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                        <p class="text-xs font-bold text-[#242424] px-1">{{ Auth::user()->name }}</p>
                    </div>
                    <div>
                        <x-input-label for="notes" value="Notes" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                        <textarea id="notes" name="notes" rows="1" class="fluent-input text-[11px] h-[33px] resize-none" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Income Items -->
            <div class="card-premium bg-white overflow-hidden border border-[#E0E0E0]">
                <div class="px-8 py-4 bg-[#FAFAFA] border-b border-[#E0E0E0] flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <div class="w-1.5 h-6 bg-[#107C10] rounded-full"></div>
                        <h3 class="text-xs font-bold text-[#242424] uppercase tracking-widest">Income Items</h3>
                    </div>
                    <button type="button" @click="addLine()" class="px-3 py-1.5 bg-white border border-[#E0E0E0] text-[10px] font-bold text-emerald-600 uppercase rounded-[4px] hover:bg-[#F0F0F0] transition shadow-sm">
                        + Add Line
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-[#FAFAFA] border-b border-[#E0E0E0]">
                                <th class="px-8 py-3 text-[9px] font-bold text-gray-400 uppercase tracking-[0.15em] w-40">Date</th>
                                <th class="px-8 py-3 text-[9px] font-bold text-gray-400 uppercase tracking-[0.15em]">Description</th>
                                <th class="px-8 py-3 text-[9px] font-bold text-gray-400 uppercase tracking-[0.15em] w-56">Category/Source</th>
                                <th class="px-8 py-3 text-[9px] font-bold text-gray-400 uppercase tracking-[0.15em] w-48 border-l border-[#E0E0E0]">Amount</th>
                                <th class="w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E0E0E0]">
                            <template x-for="(line, index) in lines" :key="index">
                                <tr class="group hover:bg-[#FAFAFA]/50 transition duration-150">
                                    <td class="px-8 py-3">
                                        <input type="date" :name="'lines['+index+'][date]'" x-model="line.date" class="fluent-input text-[11px] font-bold bg-transparent border-transparent hover:border-[#E0E0E0] focus:bg-white w-full" required>
                                    </td>
                                    <td class="px-8 py-3">
                                        <input type="text" :name="'lines['+index+'][description]'" x-model="line.description" placeholder="Revenue source details..." class="fluent-input text-[11px] bg-transparent border-transparent hover:border-[#E0E0E0] focus:bg-white w-full" required>
                                    </td>
                                    <td class="px-8 py-3">
                                        <select :name="'lines['+index+'][category_id]'" x-model="line.category_id" class="fluent-input fluent-select text-[11px] font-bold bg-transparent border-transparent hover:border-[#E0E0E0] focus:bg-white w-full" required>
                                            <option value="">Choose category...</option>
                                            <template x-for="cat in availableCategories" :key="cat.id">
                                                <option :value="cat.id" x-text="cat.name"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="px-8 py-3 border-l border-[#E0E0E0]">
                                        <div class="relative">
                                            <p class="absolute left-0 top-1/2 -translate-y-1/2 text-gray-300 text-[11px] font-bold italic">Rs.</p>
                                            <input type="number" step="1" :name="'lines['+index+'][amount]'" x-model.number="line.amount" @input="calculateGrandTotal()" class="fluent-input text-[11px] font-bold bg-transparent border-transparent hover:border-[#E0E0E0] focus:bg-white w-full pl-8 text-right" placeholder="0" required>
                                        </div>
                                    </td>
                                    <td class="pr-6 py-3">
                                        <button type="button" @click="removeLine(index)" class="text-gray-300 hover:text-[#D1102A] transition" x-show="lines.length > 1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="px-8 py-6 flex flex-col md:flex-row justify-between items-center gap-6 border-t border-[#E0E0E0] bg-[#FAFAFA]">
                    <div class="text-gray-400 text-[10px] italic font-semibold">
                        Enter all credits and income received for this reporting period.
                    </div>
                    
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.15em] mb-1">Total Income</p>
                        <p class="text-3xl font-bold text-emerald-600 tracking-tighter" x-text="formatCurrency(grandTotal)"></p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-end gap-3 p-6 card-premium bg-white border border-[#E0E0E0]">
                <a href="{{ route('director.income.index') }}" class="px-6 py-2 border border-[#E0E0E0] text-[10px] font-bold text-[#424242] uppercase rounded-[4px] hover:bg-[#F0F0F0] transition tracking-widest">
                    Cancel
                </a>
                
                <button type="submit" name="save_draft" value="1" class="px-6 py-2 border border-emerald-600 text-[10px] font-bold text-emerald-600 uppercase rounded-[4px] hover:bg-emerald-50 transition tracking-widest">
                    Save as Draft
                </button>
                
                <button type="submit" 
                        @click.prevent="$dispatch('open-confirm', { 
                            title: 'Submit Revenue', 
                            message: 'Submit this report for approval?', 
                            confirmText: 'Submit Now',
                            action: () => { 
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'submit_report';
                                input.value = '1';
                                $el.form.appendChild(input);
                                $el.form.submit();
                            } 
                        })"
                        class="px-8 py-2 bg-emerald-600 text-white text-[10px] font-black uppercase rounded-[4px] hover:bg-emerald-700 transition tracking-widest shadow-lg shadow-emerald-500/10">
                    Submit Report
                </button>
            </div>
        </form>
    </div>

    <script>
        function incomeForm() {
            return {
                selectedDirectorId: '{{ Auth::id() }}',
                availableCategories: @json($categories),
                lines: {!! json_encode(old('lines', [['date' => date('Y-m-d'), 'description' => '', 'category_id' => '', 'amount' => 0]])) !!},
                grandTotal: 0,
                init() {
                    this.calculateGrandTotal();
                },
                addLine() {
                    this.lines.push({ date: new Date().toISOString().split('T')[0], description: '', category_id: '', amount: 0 });
                    this.calculateGrandTotal();
                },
                removeLine(index) {
                    this.lines.splice(index, 1);
                    this.calculateGrandTotal();
                },
                calculateGrandTotal() {
                    this.grandTotal = this.lines.reduce((sum, line) => {
                        return sum + (parseFloat(line.amount) || 0);
                    }, 0);
                },
                formatCurrency(value) {
                    return 'Rs. ' + Math.round(value).toLocaleString();
                },
                async refreshCategories() {
                    if (!this.selectedDirectorId) return;
                    try {
                        const response = await fetch(`/director/reports/categories/${this.selectedDirectorId}?type=income`);
                        this.availableCategories = await response.json();
                    } catch (e) {
                        console.error("Failed to refresh categories", e);
                    }
                }
            }
        }
    </script>
</x-app-layout>
