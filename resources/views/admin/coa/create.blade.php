<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#242424]">Register Financial Classification</h2>
                <p class="text-xs font-semibold text-gray-400 mt-1 uppercase tracking-tight">Chart of Accounts • System Extension</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="card-premium p-8 bg-white">
                <form method="POST" action="{{ route('admin.coa.store') }}" class="space-y-8">
                    @csrf

                    <div class="flex items-center space-x-2 mb-8 pb-4 border-b border-[#F0F0F0]">
                        <div class="w-1.5 h-6 bg-[#0F6CBD] rounded-full"></div>
                        <h3 class="text-xs font-bold text-[#242424] uppercase tracking-widest">Classification Details</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Code -->
                        <div>
                            <x-input-label for="code" value="Resource Protocol Code" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                            <input id="code" name="code" type="text" value="{{ old('code') }}" class="fluent-input w-full font-mono uppercase" placeholder="EXP-XXX" required autofocus>
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            <p class="mt-1 text-[9px] text-[#0F6CBD] font-bold italic uppercase tracking-tighter">Internal Identifier Protocol</p>
                        </div>

                        <!-- Type -->
                        <div>
                            <x-input-label for="type" value="Financial Nature" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                            <select id="type" name="type" class="fluent-input fluent-select w-full font-bold">
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Operational Expenditure (Expense)</option>
                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Revenue Stream (Income)</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Director Assignment -->
                        <div>
                            <x-input-label for="director_id" value="Assign to Director" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                            <select id="director_id" name="director_id" class="fluent-input fluent-select w-full font-bold">
                                <option value="">— Global (Shared by all directors) —</option>
                                @foreach($directors as $director)
                                    <option value="{{ $director->id }}" {{ old('director_id') == $director->id ? 'selected' : '' }}>
                                        {{ $director->name }} {{ $director->department ? '('.$director->department.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-[9px] text-gray-400 italic">Leave blank to create a shared global category.</p>
                            <x-input-error :messages="$errors->get('director_id')" class="mt-2" />
                        </div>

                        <!-- Name -->
                        <div class="md:col-span-2">
                            <x-input-label for="name" value="Classification Designation" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                            <input id="name" name="name" type="text" value="{{ old('name') }}" class="fluent-input w-full" placeholder="e.g., Strategic Partnership Travel" required>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <x-input-label for="description" value="Operational Directive" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                            <textarea id="description" name="description" class="fluent-input w-full h-24" placeholder="Brief context for this account head...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Budget Limit -->
                        <div>
                            <x-input-label for="budget_limit" value="Fiscal Safeguard Limit" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rs.</span>
                                <input id="budget_limit" name="budget_limit" type="number" step="1" value="{{ old('budget_limit') }}" class="fluent-input w-full pl-12 font-bold" placeholder="0">
                            </div>
                            <x-input-error :messages="$errors->get('budget_limit')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-[#F0F0F0]">
                        <a href="{{ route('admin.coa.index') }}" class="px-6 py-2 border border-[#E0E0E0] text-[10px] font-bold text-[#424242] uppercase rounded-[4px] hover:bg-[#F0F0F0] transition tracking-widest">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-2 bg-[#0F6CBD] text-white text-[10px] font-bold uppercase rounded-[4px] hover:bg-[#115EA3] transition tracking-widest shadow-lg shadow-blue-500/10">
                            Commit Classification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
