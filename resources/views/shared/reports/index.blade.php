<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-3xl font-extrabold text-[#242424] tracking-tight">Fiscal Intelligence Center</h2>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="text-[11px] font-bold {{ $type == 'income' ? 'text-emerald-600' : 'text-[#0F6CBD]' }} uppercase tracking-[0.2em]">
                        {{ $type == 'income' ? 'Revenue Summary' : 'Expenditure Summary' }}
                    </span>
                    <span class="text-gray-300 text-xs">•</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">v4.2 Analysis</span>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                {{-- Expense / Income toggle --}}
                <div class="flex bg-white border border-[#E0E0E0] rounded-[4px] p-1 shadow-sm">
                    <a href="{{ request()->fullUrlWithQuery(['type' => 'expense']) }}" class="px-4 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-[2px] transition {{ $type == 'expense' ? 'bg-[#0F6CBD] text-white shadow-md' : 'text-gray-400 hover:text-[#242424]' }}">
                        Expenses
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['type' => 'income']) }}" class="px-4 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-[2px] transition {{ $type == 'income' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-400 hover:text-[#242424]' }}">
                        Income
                    </a>
                </div>

                <span class="h-5 w-[1px] bg-[#E0E0E0] hidden sm:block"></span>

                {{-- Excel Export --}}
                <a href="{{ route('reports.excel', request()->query()) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 border border-[#107C10] text-[#107C10] text-[10px] font-bold uppercase tracking-widest rounded-[4px] hover:bg-[#F3F9F1] transition"
                   title="Download as Excel">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>

                {{-- PDF Export --}}
                <a href="{{ route('reports.pdf', request()->query()) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#0F6CBD] text-white text-[10px] font-bold uppercase tracking-widest rounded-[4px] hover:bg-[#115EA3] transition shadow-lg shadow-blue-500/10"
                   title="Download as PDF">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-12">
        <!-- Filter Bar -->
        <div class="card-premium px-6 py-5 bg-white border border-[#E0E0E0] shadow-sm">
            <form action="{{ route('reports.index') }}" method="GET">
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="flex flex-col lg:flex-row lg:items-end gap-3">

                    {{-- From Date --}}
                    <div class="flex-1">
                        <label class="text-[9px] uppercase font-bold text-gray-400 mb-1.5 block tracking-widest leading-none">From Date</label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                               class="fluent-input text-[11px] font-bold w-full h-[38px]">
                    </div>

                    {{-- To Date --}}
                    <div class="flex-1">
                        <label class="text-[9px] uppercase font-bold text-gray-400 mb-1.5 block tracking-widest leading-none">To Date</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                               class="fluent-input text-[11px] font-bold w-full h-[38px]">
                    </div>

                    {{-- Category --}}
                    <div class="flex-1">
                        <label class="text-[9px] uppercase font-bold text-gray-400 mb-1.5 block tracking-widest leading-none">Category Focus</label>
                        <select name="category_id" class="fluent-input fluent-select text-[11px] font-bold w-full h-[38px]">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Action --}}
                    <div class="flex gap-2">
                        <button type="submit" class="h-[38px] px-6 bg-[#F8F9FA] border border-[#E0E0E0] text-[#242424] text-[9px] font-black uppercase tracking-widest rounded-[4px] hover:bg-[#F0F0F0] transition shadow-sm whitespace-nowrap">
                            Generate Analysis
                        </button>
                        @if(request()->anyFilled(['director_id', 'category_id', 'start_date', 'end_date']))
                            <a href="{{ route('reports.index', ['type' => $type]) }}"
                               class="h-[38px] w-10 flex-shrink-0 flex items-center justify-center bg-[#F3F2F1] text-gray-400 hover:text-red-500 rounded-[4px] transition"
                               title="Reset Filters">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Data Grid -->
            <div class="lg:col-span-8 space-y-8">
                <div class="flex items-center space-x-3 px-2">
                    <div class="w-1.5 h-8 {{ $type == 'income' ? 'bg-emerald-600' : 'bg-[#0F6CBD]' }} rounded-full"></div>
                    <h3 class="text-sm font-black text-[#242424] uppercase tracking-[0.15em]">
                        {{ $type == 'income' ? 'Revenue Breakdown' : 'Expenditure Breakdown' }}
                    </h3>
                </div>
                
                <div class="space-y-6">
                    @forelse($groupedData as $directorName => $data)
                        <div class="card-premium p-0 overflow-hidden bg-white border border-[#EDEBE9] shadow-sm hover:shadow-md transition-shadow">
                            <!-- Header -->
                            <div class="px-8 py-6 bg-[#FAFAFA] border-b border-[#EDEBE9] flex justify-between items-center">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 rounded-full bg-white border-2 border-[#E0E0E0] flex items-center justify-center text-xs font-black text-[#242424]">
                                        {{ substr($directorName, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Director</p>
                                        <h4 class="text-base font-black text-[#242424]">{{ $directorName }}</h4>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Consolidated Total</p>
                                    <p class="text-xl font-black {{ $type == 'income' ? 'text-emerald-700' : 'text-[#0F6CBD]' }}">
                                        Rs. {{ number_format($data['total'], 0) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Categories -->
                            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                                @foreach($data['categories'] as $catName => $catTotal)
                                    <div class="flex items-center justify-between py-3 border-b border-[#F3F2F1] hover:bg-[#FAFAFA] transition px-2 rounded-[2px] group">
                                        <div class="flex flex-col">
                                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter mb-0.5 group-hover:text-[#424242] transition">Classification</span>
                                            <span class="text-[12px] font-bold text-[#424242]">{{ $catName }}</span>
                                        </div>
                                        <span class="text-[13px] font-black text-[#242424]">Rs. {{ number_format($catTotal, 0) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="card-premium p-20 text-center bg-white border border-[#E0E0E0] rounded-[4px]">
                            <div class="w-16 h-16 bg-[#FAFAFA] rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">No financial data detected for this scope.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Global Stats Sidebar -->
            <div class="lg:col-span-4 space-y-8">
                <div class="flex items-center space-x-3 px-2">
                    <div class="w-1.5 h-8 bg-[#000000] rounded-full"></div>
                    <h3 class="text-sm font-black text-[#242424] uppercase tracking-[0.15em]">Global Breakdown</h3>
                </div>
                
                <div class="card-premium bg-white sticky top-8 border border-[#E0E0E0] p-8 shadow-sm">
                    <div class="space-y-8">
                        @forelse($categoryData as $catName => $catTotal)
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-bold text-[#424242] uppercase tracking-tight">{{ $catName }}</span>
                                    <span class="text-[11px] font-black text-[#242424]">{{ number_format($catTotal, 0) }}</span>
                                </div>
                                <div class="h-1.5 w-full bg-[#F3F2F1] rounded-full overflow-hidden">
                                    <div class="h-full {{ $type == 'income' ? 'bg-emerald-600' : 'bg-[#0F6CBD]' }} rounded-full transition-all duration-1000 shadow-[0_0_8px_rgba(16,124,16,0.2)]" style="width: {{ $grandTotal > 0 ? ($catTotal / $grandTotal) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest text-center py-10 italic">Scope is empty.</p>
                        @endforelse

                        <div class="pt-8 border-t border-[#EDEBE9] mt-8">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Period Output</p>
                            <p class="text-4xl font-black {{ $type == 'income' ? 'text-emerald-700' : 'text-[#242424]' }} tracking-tighter">
                                Rs. {{ number_format($grandTotal, 0) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
