<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#242424]">Operational Expenditure Repository</h2>
                <p class="text-xs font-semibold text-gray-400 mt-1 uppercase tracking-tight">Financial Records Management • Protocol v4.2</p>
            </div>
            <div class="flex items-center space-x-2">
                @hasanyrole('admin|director')
                    <a href="{{ route('director.reports.create') }}" class="px-6 py-2 bg-[#0F6CBD] text-white text-[10px] font-bold uppercase tracking-widest rounded-[4px] hover:bg-[#115EA3] transition shadow-lg shadow-blue-500/10">
                        Initiate New Voucher
                    </a>
                @endhasanyrole
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Filter Bar --}}
        <div class="card-premium px-6 py-4 bg-white border border-[#E0E0E0] shadow-sm">
            <form action="{{ route('director.reports.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-end gap-3">
                <div class="flex-1">
                    <label class="text-[9px] uppercase font-bold text-gray-400 mb-1.5 block tracking-widest leading-none">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="fluent-input text-[11px] font-bold w-full h-[38px]">
                </div>
                <div class="flex-1">
                    <label class="text-[9px] uppercase font-bold text-gray-400 mb-1.5 block tracking-widest leading-none">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="fluent-input text-[11px] font-bold w-full h-[38px]">
                </div>
                
                @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant'))
                    <div class="flex-1">
                        <label class="text-[9px] uppercase font-bold text-gray-400 mb-1.5 block tracking-widest leading-none">Director Portfolio</label>
                        <select name="director_id" class="fluent-input fluent-select text-[11px] font-bold w-full h-[38px]">
                            <option value="">All Portfolios</option>
                            @foreach($directors as $dir)
                                <option value="{{ $dir->id }}" {{ request('director_id') == $dir->id ? 'selected' : '' }}>{{ $dir->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                
                <div class="flex gap-2 {{ (Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant')) ? '' : 'flex-1' }}">
                    <button type="submit" class="h-[38px] flex-grow lg:flex-none px-6 bg-[#F8F9FA] border border-[#E0E0E0] text-[#242424] text-[9px] font-black uppercase tracking-widest rounded-[4px] hover:bg-[#F0F0F0] transition shadow-sm whitespace-nowrap">
                        Apply Filter
                    </button>
                    <a href="{{ route('director.reports.index') }}" class="h-[38px] w-[38px] bg-white border border-[#E0E0E0] text-gray-400 hover:text-[#D1102A] transition rounded-[4px] flex items-center justify-center shadow-sm" title="Clear Filters">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                    </a>
                </div>
            </form>
        </div>

        <!-- Records Counter / KPI Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="card-premium p-4 bg-white flex items-center justify-between">
                <div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Volume</p>
                    <p class="text-xl font-bold text-[#242424]">{{ $reports->total() }} Records</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#FAFAFA] flex items-center justify-center text-[#0F6CBD]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </div>
            </div>
            <!-- More KPIs could go here... -->
        </div>

        <div class="card-premium bg-white overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#FAFAFA] border-b border-[#E0E0E0]">
                            <th class="px-6 py-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Protocol ID</th>
                            @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant'))
                                <th class="px-6 py-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Entity Ownership</th>
                            @endif
                            <th class="px-6 py-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Record Subject</th>
                            <th class="px-6 py-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Op. Period</th>
                            <th class="px-6 py-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest text-right">Portfolio Value</th>
                            <th class="px-6 py-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F0F0F0]">
                        @forelse($reports as $report)
                            <tr class="group hover:bg-[#FAFAFA] transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-[11px] font-bold text-[#0F6CBD]">{{ $report->voucher_no }}</td>
                                @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant'))
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-6 h-6 rounded-full bg-[#EDEBE9] border border-[#E0E0E0] flex items-center justify-center text-[9px] font-black text-[#424242]">
                                                {{ substr($report->director->name, 0, 1) }}
                                            </div>
                                            <span class="text-[11px] font-bold text-[#242424]">{{ $report->director->name }}</span>
                                        </div>
                                    </td>
                                @endif
                                <td class="px-6 py-4">
                                    <p class="text-[11px] font-bold text-[#242424] truncate max-w-xs">{{ $report->title }}</p>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-tight mt-0.5">Revision v{{ $report->version }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-[11px] font-bold text-gray-500">
                                    {{ date("M Y", mktime(0, 0, 0, $report->period_month, 1, $report->period_year)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-[11px] font-black text-[#242424]">
                                    Rs. {{ number_format($report->lines_sum_amount ?? $report->lines->sum('amount'), 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-0.5 rounded-[4px] text-[9px] font-bold uppercase tracking-tight shadow-sm
                                        {{ $report->status == 'approved' ? 'bg-[#107C10]/10 text-[#107C10] border border-[#107C10]/20' : 
                                           ($report->status == 'submitted' ? 'bg-[#0F6CBD]/10 text-[#0F6CBD] border border-[#0F6CBD]/20' : 
                                           ($report->status == 'rejected' ? 'bg-[#D1102A]/10 text-[#D1102A] border border-[#D1102A]/20' : 'bg-[#F0F0F0] text-gray-400')) }}">
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end space-x-1">
                                        <a href="{{ route('director.reports.show', $report) }}" class="p-1.5 rounded-[4px] hover:bg-[#F0F0F0] text-gray-400 hover:text-[#0F6CBD] transition" title="Inspect">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        
                                        @if(in_array($report->status, ['draft', 'rejected']) || Auth::user()->hasRole('admin'))
                                            <a href="{{ route('director.reports.edit', $report) }}" class="p-1.5 rounded-[4px] hover:bg-[#F0F0F0] text-gray-400 hover:text-[#0F6CBD] transition" title="Revise">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </a>
                                        @endif

                                        @if($report->status == 'draft' || Auth::user()->hasRole('admin'))
                                            <form id="delete-report-{{ $report->id }}" action="{{ route('director.reports.destroy', $report) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button" 
                                                    @click="$dispatch('open-confirm', { 
                                                        title: 'Data Destruction', 
                                                        message: 'Are you sure you want to permanently delete this operational record?', 
                                                        type: 'danger',
                                                        confirmText: 'Confirm Purge',
                                                        action: () => document.getElementById('delete-report-{{ $report->id }}').submit() 
                                                    })"
                                                    class="p-1.5 rounded-[4px] hover:bg-[#D1102A]/5 text-gray-400 hover:text-[#D1102A] transition" title="Purge">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 rounded-full bg-[#FAFAFA] flex items-center justify-center mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </div>
                                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">No Operational Records Detected</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reports->hasPages())
                <div class="px-6 py-4 border-t border-[#E0E0E0] bg-[#FAFAFA]">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
