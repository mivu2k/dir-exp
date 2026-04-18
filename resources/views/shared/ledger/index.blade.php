<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-[#242424] tracking-tight">Organization Ledger</h2>
                <p class="text-[11px] font-bold text-[#0F6CBD] mt-1 uppercase tracking-[0.2em]">Consolidated Financial History • Version v4.2</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('ledger.excel', request()->query()) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 border border-[#107C10] text-[#107C10] text-[10px] font-bold uppercase tracking-widest rounded-[4px] hover:bg-[#F3F9F1] transition"
                   title="Download as Excel">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>
                <a href="{{ route('ledger.pdf', request()->query()) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#0F6CBD] text-white text-[10px] font-bold uppercase tracking-widest rounded-[4px] hover:bg-[#115EA3] transition shadow-lg shadow-blue-500/10"
                   title="Download as PDF">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ── Filter Bar ─────────────────────────────────────── --}}
        <div class="card-premium px-6 py-5 bg-white shadow-sm border border-[#E0E0E0]">
            <form action="{{ route('ledger.index') }}" method="GET">
                <div class="flex flex-col lg:flex-row lg:items-end gap-4">

                    {{-- Date: From --}}
                    <div class="flex-1">
                        <label class="text-[9px] uppercase font-bold text-gray-400 mb-1.5 block tracking-widest leading-none">From Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                               class="fluent-input text-[11px] font-bold w-full h-[38px]">
                    </div>

                    {{-- Date: To --}}
                    <div class="flex-1">
                        <label class="text-[9px] uppercase font-bold text-gray-400 mb-1.5 block tracking-widest leading-none">To Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                               class="fluent-input text-[11px] font-bold w-full h-[38px]">
                    </div>

                    {{-- Director --}}
                    <div class="flex-1">
                        <label class="text-[9px] uppercase font-bold text-gray-400 mb-1.5 block tracking-widest leading-none">Director Portfolio</label>
                        <select name="director_id" class="fluent-input fluent-select text-[11px] font-bold w-full h-[38px]">
                            <option value="">All Portfolios</option>
                            @foreach($directors as $dir)
                                <option value="{{ $dir->id }}" {{ request('director_id') == $dir->id ? 'selected' : '' }}>{{ $dir->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="flex-[1.5]">
                        <label class="text-[9px] uppercase font-bold text-gray-400 mb-1.5 block tracking-widest leading-none">Search Description</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Keywords..."
                               class="fluent-input text-[11px] font-bold w-full h-[38px]">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="h-[38px] px-6 bg-[#F8F9FA] border border-[#E0E0E0] text-[#242424] text-[9px] font-black uppercase tracking-widest rounded-[4px] hover:bg-[#F0F0F0] transition shadow-sm whitespace-nowrap">
                            Filter
                        </button>
                        <a href="{{ route('ledger.index') }}" class="h-[38px] w-[38px] bg-white border border-[#E0E0E0] text-gray-400 hover:text-[#D1102A] transition rounded-[4px] flex items-center justify-center shadow-sm" title="Clear Filters">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- ── Ledger Entries ──────────────────────────────────── --}}
        <div class="space-y-4">
            @php
                $groupedLines = $lines->groupBy(fn($l) => $l->report?->voucher_no ?? 'UNKNOWN-VOUCHER');
            @endphp

            @forelse($groupedLines as $voucherNo => $voucherLines)
                @php
                    $firstLine  = $voucherLines->first();
                    $isIncome   = $firstLine->type === 'income';
                    $batchTotal = $voucherLines->sum('amount');
                    $status     = $firstLine->report->status ?? 'unknown';

                    $statusClass = match($status) {
                        'approved'  => 'bg-[#F3F9F1] text-[#107C10] border-[#9FD89F]',
                        'submitted' => 'bg-[#EFF6FC] text-[#0F6CBD] border-[#C7E0F4]',
                        'rejected'  => 'bg-[#FDE7E9] text-[#D1102A] border-[#F4ABBA]',
                        'draft'     => 'bg-[#FAF9F8] text-gray-400 border-[#E0E0E0]',
                        default     => 'bg-gray-50 text-gray-400 border-gray-200',
                    };
                @endphp

                <div class="card-premium p-0 bg-white border border-[#EDEBE9] overflow-hidden shadow-sm hover:shadow-md transition-all duration-200">

                    {{-- Voucher Group Header --}}
                    <div class="px-6 py-4 border-b border-[#EDEBE9] flex flex-wrap justify-between items-center gap-4
                                {{ $isIncome ? 'bg-emerald-50/30' : 'bg-[#FAFAFA]' }}">

                        {{-- Left: icon + voucher info --}}
                        <div class="flex items-center gap-4">
                            {{-- Type icon --}}
                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                                        {{ $isIncome ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200' }}">
                                @if($isIncome)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 text-emerald-600" style="width:1.1rem;height:1.1rem" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 text-amber-600" style="width:1.1rem;height:1.1rem" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                                @endif
                            </div>

                            {{-- Voucher + type label --}}
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">
                                    {{ $isIncome ? 'Income Report' : 'Expense Report' }}
                                </p>
                                <h4 class="text-[13px] font-black text-[#242424] font-mono tracking-tight">{{ $voucherNo }}</h4>
                            </div>

                            {{-- Divider + Director --}}
                            <div class="hidden sm:flex items-center gap-4">
                                <div class="h-8 w-px bg-[#E0E0E0]"></div>
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">Director</p>
                                    <p class="text-[12px] font-bold text-[#424242]">{{ $firstLine->report->director->name ?? '—' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Right: status badge + batch total --}}
                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Status</p>
                                <span class="inline-block px-2 py-0.5 rounded-[3px] border text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">
                                    {{ $status }}
                                </span>
                            </div>
                            <div class="text-right min-w-[110px]">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Batch Total</p>
                                <p class="text-[15px] font-black whitespace-nowrap {{ $isIncome ? 'text-emerald-700' : 'text-[#D1102A]' }}">
                                    {{ $isIncome ? '+' : '−' }} Rs.&nbsp;{{ number_format($batchTotal, 0) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Itemized Lines Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-white border-b border-[#F0F0F0]">
                                <tr>
                                    <th class="px-6 py-2.5 text-[9px] font-black text-gray-400 uppercase tracking-widest w-32">Date</th>
                                    <th class="px-6 py-2.5 text-[9px] font-black text-gray-400 uppercase tracking-widest w-48">Classification</th>
                                    <th class="px-6 py-2.5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Description</th>
                                    <th class="px-6 py-2.5 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest w-36">Amount (PKR)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F5F5F5]">
                                @foreach($voucherLines as $line)
                                    <tr class="hover:bg-[#FAFAFA] transition duration-100">
                                        <td class="px-6 py-3 text-[11px] font-semibold text-gray-500 whitespace-nowrap">
                                            {{ $line->date->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="inline-block px-2 py-0.5 rounded-[2px] border border-[#E0E0E0] text-[8.5px] font-black uppercase text-[#424242] bg-white shadow-sm whitespace-nowrap">
                                                {{ $line->category->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-[11px] font-semibold text-[#424242]">{{ $line->description }}</td>
                                        <td class="px-6 py-3 text-right font-black text-[12px] whitespace-nowrap
                                                   {{ $line->type === 'income' ? 'text-emerald-700' : 'text-[#D1102A]' }}">
                                            {{ $line->type === 'income' ? '+' : '−' }}&nbsp;{{ number_format($line->amount, 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="card-premium p-20 text-center bg-white border border-[#E0E0E0] rounded-[4px] shadow-sm">
                    <div class="w-16 h-16 rounded-full bg-[#FAFAFA] flex items-center justify-center mx-auto mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">No unified operational history detected.</p>
                </div>
            @endforelse
        </div>

        {{-- ── Pagination ──────────────────────────────────────── --}}
        <div>{{ $lines->links() }}</div>

        {{-- ── Summary Bar ─────────────────────────────────────── --}}
        @php
            $netBalance   = $lines->sum('amount_signed');
            $totalIncome  = $lines->where('type', 'income')->sum('amount');
            $totalExpense = $lines->where('type', 'expense')->sum('amount');
        @endphp
        <div class="card-premium bg-[#1A1A1A] p-8 border-none shadow-2xl relative overflow-hidden">
            <div class="absolute right-0 top-0 w-64 h-64 bg-[#0F6CBD]/10 rounded-full -mr-32 -mt-32 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <p class="text-[10px] font-black text-[#0F6CBD] uppercase tracking-[0.3em] mb-1">Consolidated Organization Audit</p>
                    <h3 class="text-2xl font-bold text-white tracking-tight">Net Financial Position</h3>
                    <p class="text-[10px] text-gray-500 mt-1 italic">* Current page data — apply filters to narrow scope.</p>
                </div>
                <div class="flex items-center gap-10 border-l border-[#333] pl-10">
                    <div class="text-right">
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1">Entries</p>
                        <p class="text-xl font-black text-white">{{ $lines->total() }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1">Total Income</p>
                        <p class="text-xl font-black text-emerald-400">+ Rs.&nbsp;{{ number_format($totalIncome, 0) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1">Total Expense</p>
                        <p class="text-xl font-black text-red-400">− Rs.&nbsp;{{ number_format($totalExpense, 0) }}</p>
                    </div>
                    <div class="text-right border-l border-[#333] pl-10">
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1">Net Balance</p>
                        <p class="text-3xl font-black tracking-tighter {{ $netBalance >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $netBalance >= 0 ? '+' : '−' }} Rs.&nbsp;{{ number_format(abs($netBalance), 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
