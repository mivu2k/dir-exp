<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-3xl font-black text-[#242424] tracking-tight">Income Report Details</h2>
                <p class="text-[11px] font-bold text-emerald-600 mt-1 uppercase tracking-[0.2em]">{{ $incomeReport->voucher_no }} • Version v1.0</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('director.income.excel', ['incomeReport' => $incomeReport]) }}" class="px-5 py-2.5 bg-white border border-emerald-600/20 text-[10px] font-bold text-emerald-700 uppercase tracking-widest rounded-[4px] hover:bg-emerald-50 transition flex items-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Excel Export
                </a>
                <a href="{{ route('director.income.print', ['incomeReport' => $incomeReport]) }}" class="px-5 py-2.5 bg-white border border-[#E0E0E0] text-[10px] font-bold text-[#242424] uppercase tracking-widest rounded-[4px] hover:bg-[#F3F2F1] transition flex items-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Print PDF
                </a>
                <a href="{{ route('director.income.index') }}" class="px-5 py-2.5 bg-white border border-[#E0E0E0] text-[10px] font-bold text-[#424242] uppercase tracking-widest rounded-[4px] hover:bg-[#F3F2F1] transition shadow-sm">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Left Side: Data & Itemization -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Report Header -->
                <div class="card-premium p-10 bg-white border-t-4 border-emerald-600 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <div class="space-y-6">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-[0.2em] mb-2 font-mono">Report Title</p>
                                <h3 class="text-2xl font-black text-[#242424] leading-tight">{{ $incomeReport->title }}</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-2 font-mono">Director</p>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-[#FAFAFA] border border-[#E0E0E0] flex items-center justify-center text-[10px] font-black">{{ substr($incomeReport->director->name, 0, 1) }}</div>
                                        <span class="text-sm font-bold text-[#242424]">{{ $incomeReport->director->name }}</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-2 font-mono">Month</p>
                                    <p class="text-sm font-bold text-[#242424]">{{ date("F Y", mktime(0, 0, 0, $incomeReport->period_month, 1, $incomeReport->period_year)) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-emerald-50/50 p-6 rounded-[4px] border border-emerald-100 flex flex-col justify-between">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-emerald-600 tracking-widest mb-2 font-mono">Total Income</p>
                                <p class="text-4xl font-black text-emerald-700 tracking-tighter">Rs. {{ number_format($incomeReport->lines->sum('amount'), 0) }}</p>
                            </div>
                            <div class="mt-4">
                                <span class="px-3 py-1 rounded-[4px] text-[10px] font-black uppercase tracking-widest border
                                    {{ $incomeReport->status == 'approved' ? 'bg-[#107C10]/10 text-[#107C10] border-[#107C10]/20' : 
                                       ($incomeReport->status == 'submitted' ? 'bg-[#0F6CBD]/10 text-[#0F6CBD] border-[#0F6CBD]/20' : 
                                       ($incomeReport->status == 'rejected' ? 'bg-[#D1102A]/10 text-[#D1102A] border-[#D1102A]/20' : 'bg-[#F0F0F0] text-gray-400 border-gray-200')) }}">
                                    {{ $incomeReport->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($incomeReport->notes)
                    <div class="mt-10 pt-8 border-t border-[#F3F2F1]">
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-3 font-mono">Notes</p>
                        <p class="text-sm text-[#424242] font-medium leading-relaxed italic bg-[#FAFAFA] p-4 rounded-[2px] border-l-4 border-[#E0E0E0]">
                            "{{ $incomeReport->notes }}"
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Income Items Table -->
                <div class="card-premium p-0 bg-white overflow-hidden border border-[#EDEBE9]">
                    <div class="px-8 py-5 bg-[#FAFAFA] border-b border-[#E0E0E0] flex justify-between items-center text-[10px] font-black text-[#242424] uppercase tracking-widest">
                        Income Items
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-white border-b border-[#F0F0F0] shadow-sm">
                                    <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest w-32">Date</th>
                                    <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest w-48">Category/Source</th>
                                    <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest">Description</th>
                                    <th class="px-8 py-4 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest w-40">Amount (PKR)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F9F9F9]">
                                @foreach($incomeReport->lines as $line)
                                    <tr class="hover:bg-[#FAFAFA] transition duration-150">
                                        <td class="px-8 py-5 text-[11px] font-bold text-gray-500 whitespace-nowrap">{{ $line->date->format('d M Y') }}</td>
                                        <td class="px-8 py-5">
                                            <span class="px-2 py-0.5 border border-[#E0E0E0] rounded-[2px] text-[9px] font-black text-[#424242] uppercase bg-[#FAFAFA]">
                                                {{ $line->category->name }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-[11px] font-semibold text-[#242424]">{{ $line->description }}</td>
                                        <td class="px-8 py-5 text-right font-black text-[12px] text-emerald-700">Rs. {{ number_format($line->amount, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side: Summary Sidecar -->
            <div class="lg:col-span-4 space-y-8">
                <div class="card-premium p-8 bg-white border border-[#E0E0E0] shadow-sm">
                    <h4 class="text-xs font-black text-[#242424] uppercase tracking-widest mb-6 border-b border-[#F0F0F0] pb-4">Revenue Breakdown</h4>
                    <div class="space-y-4">
                        @foreach($categorySummary as $summary)
                            <div class="flex justify-between items-center">
                                <span class="text-[11px] font-bold text-gray-500 uppercase">{{ $summary['name'] }}</span>
                                <span class="text-[12px] font-black text-[#242424]">Rs. {{ number_format($summary['total'], 0) }}</span>
                            </div>
                        @endforeach
                        <div class="pt-4 border-t border-[#F0F0F0] flex justify-between items-center">
                            <span class="text-[11px] font-black text-[#242424] uppercase">Total</span>
                            <span class="text-[14px] font-black text-emerald-600">Rs. {{ number_format($incomeReport->lines->sum('amount'), 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
