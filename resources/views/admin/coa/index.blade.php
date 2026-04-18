<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-3xl font-extrabold text-[#242424] tracking-tight">Classification Directory</h2>
                <p class="text-[11px] font-bold text-[#0F6CBD] mt-1 uppercase tracking-[0.2em]">Chart of Accounts Framework • Version v4.2</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.coa.bulk-create') }}" class="px-6 py-2.5 border border-[#0F6CBD] text-[#0F6CBD] text-[11px] font-bold uppercase tracking-widest rounded-[4px] hover:bg-[#EFF6FC] transition">
                    Bulk Create
                </a>
                <a href="{{ route('admin.coa.create') }}" class="px-6 py-2.5 bg-[#0F6CBD] text-white text-[11px] font-bold uppercase tracking-widest rounded-[4px] hover:bg-[#115EA3] transition shadow-lg shadow-blue-500/10">
                    Register New Category
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-12">
        <!-- Search & Filter -->
        <div class="card-premium p-10 bg-white shadow-sm border border-[#E0E0E0]">
            <form action="{{ route('admin.coa.index') }}" method="GET" class="flex flex-wrap items-end gap-8">
                <div class="flex-grow min-w-[300px]">
                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-3 block tracking-widest">Search</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" class="fluent-input w-full pl-12 text-[12px] font-bold" placeholder="Designation or Category Code...">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-3 block tracking-widest">Nature</label>
                    <select name="type" class="fluent-input fluent-select w-48 text-[11px] font-bold">
                        <option value="">All Scopes</option>
                        <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Revenue</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expenditure</option>
                    </select>
                </div>
                <button type="submit" class="px-8 py-3 bg-[#FAFAFA] border border-[#E0E0E0] text-[#242424] text-[11px] font-black rounded-[4px] uppercase tracking-widest hover:bg-[#F3F2F1] transition shadow-sm">
                    Apply Filter
                </button>
            </form>
        </div>

        <!-- Director-Centric Accordion -->
        <div class="space-y-6" x-data="{ activeDirector: 'global' }">
            <!-- Global / Shared Categories -->
            <div class="card-premium p-0 bg-white border border-blue-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <button @click="activeDirector = (activeDirector === 'global' ? null : 'global')" 
                        class="w-full px-10 py-6 flex items-center justify-between text-left hover:bg-blue-50/30 transition duration-200">
                    <div class="flex items-center space-x-6">
                        <div class="w-12 h-12 rounded-full border-2 border-blue-400 flex items-center justify-center bg-blue-50 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" /></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-[#242424] uppercase tracking-wider">Global Standards (Shared)</h3>
                            <p class="text-[10px] font-bold text-blue-600 uppercase tracking-[0.15em] mt-0.5">Base Classifications for All Directors</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-8">
                        <div class="hidden md:block text-right border-r border-[#E0E0E0] pr-8">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Universal Categories</p>
                            <p class="text-[13px] font-black text-[#242424]">{{ \App\Models\ChartOfAccount::whereNull('director_id')->count() }}</p>
                        </div>
                        <div class="transition-transform duration-300" :class="{ 'rotate-180': activeDirector === 'global' }">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                </button>

                <div x-show="activeDirector === 'global'" class="border-t border-[#F3F2F1] bg-blue-50/5">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-white border-b border-[#EDEBE9]">
                                    <th class="px-10 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest w-32">Code</th>
                                    <th class="px-10 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest">Category Name</th>
                                    <th class="px-10 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest w-40 text-center">Nature</th>
                                    <th class="px-10 py-4 text-right"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F3F2F1]">
                                @foreach(\App\Models\ChartOfAccount::whereNull('director_id')->get() as $account)
                                    <tr class="group hover:bg-white transition duration-150">
                                        <td class="px-10 py-5 font-mono text-[11px] font-bold text-[#0F6CBD]">{{ $account->code }}</td>
                                        <td class="px-10 py-5">
                                            <p class="text-[12px] font-black text-[#242424]">{{ $account->name }}</p>
                                        </td>
                                        <td class="px-10 py-5 text-center">
                                            <span class="px-2 py-0.5 rounded-[2px] text-[8px] font-black uppercase tracking-widest border {{ $account->type == 'income' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                                {{ $account->type }}
                                            </span>
                                        </td>
                                        <td class="px-10 py-5 text-right">
                                            <a href="{{ route('admin.coa.edit', $account) }}" class="p-2 text-gray-300 hover:text-[#0F6CBD] transition">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @forelse($directors as $director)
                <div class="card-premium p-0 bg-white border border-[#EDEBE9] overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <!-- Accordion Trigger -->
                    <button @click="activeDirector = (activeDirector === {{ $director->id }} ? null : {{ $director->id }})" 
                            class="w-full px-10 py-6 flex items-center justify-between text-left hover:bg-[#FAFAFA] transition duration-200">
                        <div class="flex items-center space-x-6">
                            <div class="w-12 h-12 rounded-full border-2 border-[#E0E0E0] flex items-center justify-center bg-white shadow-sm overflow-hidden">
                                <span class="text-xs font-black text-[#242424] uppercase">{{ substr($director->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-[#242424] uppercase tracking-wider">{{ $director->name }}</h3>
                                <p class="text-[10px] font-bold text-[#0F6CBD] uppercase tracking-[0.15em] mt-0.5">{{ $director->department ?? 'General Operations' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-8">
                            <div class="hidden md:block text-right border-r border-[#E0E0E0] pr-8">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Assigned Categories</p>
                                <p class="text-[13px] font-black text-[#242424]">{{ $director->chartOfAccounts->count() }}</p>
                            </div>
                            <div class="transition-transform duration-300" :class="{ 'rotate-180': activeDirector === {{ $director->id }} }">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </div>
                        </div>
                    </button>

                    <!-- Accordion Content -->
                    <div x-show="activeDirector === {{ $director->id }}" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-y-95"
                         x-transition:enter-end="opacity-100 scale-y-100"
                         class="border-t border-[#F3F2F1] bg-[#FAFAFA]/50">
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-white/80 border-b border-[#EDEBE9]">
                                        <th class="px-10 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest w-32">Code</th>
                                        <th class="px-10 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest">Category Name</th>
                                        <th class="px-10 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest w-40 text-center">Nature</th>
                                        <th class="px-10 py-4 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest w-48">Budget Limit</th>
                                        <th class="px-10 py-4 text-right"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#F3F2F1]">
                                    @forelse($director->chartOfAccounts as $account)
                                        <tr class="group hover:bg-white transition duration-150">
                                            <td class="px-10 py-5 whitespace-nowrap font-mono text-[11px] font-bold text-[#0F6CBD] uppercase">{{ $account->code }}</td>
                                            <td class="px-10 py-5">
                                                <p class="text-[12px] font-black text-[#242424]">{{ $account->name }}</p>
                                                <p class="text-[9px] text-gray-400 font-bold mt-0.5 truncate max-w-xs uppercase">{{ $account->description ?? 'No directive.' }}</p>
                                            </td>
                                            <td class="px-10 py-5 text-center">
                                                <span class="px-2 py-0.5 rounded-[2px] text-[8px] font-black uppercase tracking-widest border
                                                    {{ $account->type == 'income' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                                                    {{ $account->type }}
                                                </span>
                                            </td>
                                            <td class="px-10 py-5 text-right whitespace-nowrap">
                                                <p class="text-[12px] font-black text-[#242424]">Rs. {{ number_format($account->budget_limit, 0) }}</p>
                                            </td>
                                            <td class="px-10 py-5 text-right">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <a href="{{ route('admin.coa.edit', $account) }}" class="p-2 text-gray-300 hover:text-[#0F6CBD] transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                    </a>
                                                    <form id="archive-coa-{{ $account->id }}" action="{{ route('admin.coa.destroy', $account) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                    <button type="button" 
                                                            @click="$dispatch('open-confirm', { 
                                                                title: 'Archive Category', 
                                                                message: 'Are you sure you want to archive this financial category?', 
                                                                type: 'danger',
                                                                confirmText: 'Confirm Archive',
                                                                action: () => document.getElementById('archive-coa-{{ $account->id }}').submit() 
                                                            })"
                                                            class="p-2 text-gray-300 hover:text-[#D1102A] transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-10 py-12 text-center text-[10px] font-bold text-gray-300 uppercase tracking-widest italic">
                                                No specific categories assigned to this director.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card-premium p-20 text-center bg-white border border-[#E0E0E0]">
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">No directors found in the fiscal registry.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
