<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#242424]">Income Approvals</h2>
                <p class="text-xs font-semibold text-gray-400 mt-1 uppercase tracking-tight">Review Revenue Deposits & Grants • Version v4.2</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-2 py-0.5 bg-emerald-600/10 border border-emerald-600/20 rounded-[4px] text-[10px] font-black text-emerald-600 uppercase">Queue Status: Active</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Approval Repository Table -->
        <div class="card-premium bg-white overflow-hidden shadow-sm border border-[#E0E0E0]">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#FAFAFA] border-b border-[#E0E0E0]">
                            <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Submitted By</th>
                            <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Voucher Number</th>
                            <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Reporting Period</th>
                            <th class="px-8 py-4 text-right text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Total Amount</th>
                            <th class="px-8 py-4 text-center text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</th>
                            <th class="px-8 py-4 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F0F0F0]">
                        @forelse($reports as $report)
                            <tr class="group hover:bg-[#FAFAFA] transition duration-150 {{ $report->status == 'submitted' ? 'bg-emerald-50/30' : '' }}">
                                <td class="px-8 py-5 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-[#EDEBE9] border border-[#E0E0E0] flex items-center justify-center text-[10px] font-black text-[#424242]">
                                            {{ substr($report->director->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-black text-[#242424]">{{ $report->director->name }}</p>
                                            <p class="text-[9px] text-gray-500 uppercase font-bold tracking-tighter">{{ $report->director->department ?? 'General' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap">
                                    <span class="font-mono text-[11px] font-black text-[#0F6CBD]">{{ $report->voucher_no }}</span>
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-[11px] font-bold text-gray-500">
                                    {{ date("F Y", mktime(0, 0, 0, $report->period_month, 1, $report->period_year)) }}
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-right font-black text-sm text-emerald-700">
                                    Rs. {{ number_format($report->lines->sum('amount'), 0) }}
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 rounded-[4px] text-[9px] font-black uppercase tracking-widest border shadow-sm
                                        {{ $report->status == 'approved' ? 'bg-[#107C10]/10 text-[#107C10] border-[#107C10]/20' : 
                                           ($report->status == 'submitted' ? 'bg-emerald-600/10 text-emerald-600 border-emerald-600/20 animate-pulse' : 
                                           ($report->status == 'rejected' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-[#F0F0F0] text-gray-400')) }}">
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.income_approvals.show', $report) }}" class="inline-flex items-center px-4 py-2 bg-[#242424] text-white text-[9px] font-black uppercase tracking-widest rounded-[4px] hover:bg-[#000000] transition shadow-md">
                                        Review
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-full bg-[#FAFAFA] flex items-center justify-center mb-6">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                        <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">No income reports pending review</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($reports->hasPages())
                <div class="bg-[#FAFAFA] px-8 py-4 border-t border-[#E0E0E0]">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
