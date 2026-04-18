<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#242424]">Approval Requests</h2>
                <p class="text-xs font-semibold text-gray-400 mt-1 uppercase tracking-tight">Review & Manage Director Expenditures • Version v4.2</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-2 py-0.5 bg-[#0F6CBD]/10 border border-[#0F6CBD]/20 rounded-[4px] text-[10px] font-black text-[#0F6CBD] uppercase">Queue Status: Active</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Workspace -->
        <div class="card-premium p-6 bg-white shadow-sm">
            <form action="{{ route('admin.approvals.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                <div class="md:col-span-1">
                    <label class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-2 block">Director</label>
                    <select name="director_id" class="fluent-input fluent-select w-full text-[11px] font-bold">
                        <option value="">All Directors</option>
                        @foreach($directors as $dir)
                            <option value="{{ $dir->id }}" {{ request('director_id') == $dir->id ? 'selected' : '' }}>{{ $dir->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-2 block">Status</label>
                    <select name="status" class="fluent-input fluent-select w-full text-[11px] font-bold">
                        <option value="">Pending Review (Default)</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full py-2.5 bg-[#0F6CBD] text-white text-[10px] font-black rounded-[4px] uppercase tracking-widest hover:bg-[#115EA3] transition shadow-lg shadow-blue-500/10">
                        Filter List
                    </button>
                </div>
                <div class="text-right">
                    @if(request()->anyFilled(['director_id', 'status']))
                        <a href="{{ route('admin.approvals.index') }}" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest hover:text-[#D1102A] transition">
                            Clear Filters
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Approval Repository Table -->
        <div class="card-premium bg-white overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#FAFAFA] border-b border-[#E0E0E0] sticky top-0 z-10 box-shadow-sm">
                            <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Submitted By</th>
                            <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Voucher Number</th>
                            <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Billing Month</th>
                            <th class="px-8 py-4 text-right text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Total Amount</th>
                            <th class="px-8 py-4 text-center text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</th>
                            <th class="px-8 py-4 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F0F0F0]">
                        @forelse($reports as $report)
                            <tr class="group hover:bg-[#FAFAFA] transition duration-150 {{ $report->status == 'submitted' ? 'bg-[#0F6CBD]/5' : '' }}">
                                <td class="px-8 py-5 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-[#EDEBE9] border border-[#E0E0E0] flex items-center justify-center text-[10px] font-black text-[#424242]">
                                            {{ substr($report->director->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-black text-[#242424]">{{ $report->director->name }}</p>
                                            <p class="text-[9px] text-gray-500 uppercase font-bold tracking-tighter">{{ $report->director->department ?? 'Corporate' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap">
                                    <span class="font-mono text-[11px] font-black text-[#0F6CBD]">{{ $report->voucher_no }}</span>
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-[11px] font-bold text-gray-500">
                                    {{ date("F Y", mktime(0, 0, 0, $report->period_month, 1, $report->period_year)) }}
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-right font-black text-sm text-[#242424]">
                                    Rs. {{ number_format($report->lines->sum('amount'), 0) }}
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 rounded-[4px] text-[9px] font-black uppercase tracking-widest border shadow-sm
                                        {{ $report->status == 'approved' ? 'bg-[#107C10]/10 text-[#107C10] border-[#107C10]/20' : 
                                           ($report->status == 'submitted' ? 'bg-[#0F6CBD]/10 text-[#0F6CBD] border-[#0F6CBD]/20 animate-pulse' : 
                                           ($report->status == 'rejected' ? 'bg-[#D1102A]/10 text-[#D1102A] border-[#D1102A]/20' : 'bg-[#F0F0F0] text-gray-400 border-gray-200')) }}">
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.approvals.show', $report) }}" class="inline-flex items-center px-4 py-2 bg-[#242424] text-white text-[9px] font-black uppercase tracking-widest rounded-[4px] hover:bg-[#000000] transition shadow-md">
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
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </div>
                                        <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">No reports currently in queue</p>
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
