<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#242424]">Income Repository</h2>
                <p class="text-xs font-semibold text-gray-400 mt-1 uppercase tracking-tight">Revenue Records Management • Version v4.2</p>
            </div>
            <div class="flex items-center space-x-2">
                @hasanyrole('admin|director')
                    <a href="{{ route('director.income.create') }}" class="px-6 py-2 bg-[#0F6CBD] text-white text-[10px] font-bold uppercase tracking-widest rounded-[4px] hover:bg-[#115EA3] transition shadow-lg shadow-blue-500/10">
                        New Income Report
                    </a>
                @endhasanyrole
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Filter Bar --}}
        <div class="card-premium px-6 py-4 bg-white border border-[#E0E0E0] shadow-sm">
            <form action="{{ route('director.income.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-end gap-3">
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
                    <a href="{{ route('director.income.index') }}" class="h-[38px] w-[38px] bg-white border border-[#E0E0E0] text-gray-400 hover:text-[#D1102A] transition rounded-[4px] flex items-center justify-center shadow-sm" title="Clear Filters">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                    </a>
                </div>
            </form>
        </div>

        <!-- Records Counter -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="card-premium p-4 bg-white flex items-center justify-between">
                <div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Reports</p>
                    <p class="text-xl font-bold text-[#242424]">{{ $reports->total() }} Records</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#FAFAFA] flex items-center justify-center text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
        </div>

        <div class="card-premium bg-white overflow-hidden shadow-sm border border-[#E0E0E0]">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#FAFAFA] border-b border-[#E0E0E0]">
                            <th class="px-6 py-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Voucher Number</th>
                            @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant'))
                                <th class="px-6 py-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Director</th>
                            @endif
                            <th class="px-6 py-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Report Title</th>
                            <th class="px-6 py-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">Month</th>
                            <th class="px-6 py-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest text-right">Amount (PKR)</th>
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-[11px] font-bold text-gray-500">
                                    {{ date("M Y", mktime(0, 0, 0, $report->period_month, 1, $report->period_year)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-[11px] font-black text-[#242424]">
                                    Rs. {{ number_format($report->lines->sum('amount'), 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-0.5 rounded-[4px] text-[9px] font-bold uppercase tracking-tight border
                                        {{ $report->status == 'approved' ? 'bg-[#107C10]/10 text-[#107C10] border-[#107C10]/20' : 
                                           ($report->status == 'submitted' ? 'bg-[#0F6CBD]/10 text-[#0F6CBD] border-[#0F6CBD]/20' : 
                                           ($report->status == 'rejected' ? 'bg-[#D1102A]/10 text-[#D1102A] border-[#D1102A]/20' : 'bg-[#F0F0F0] text-gray-400 border-gray-200')) }}">
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end space-x-1">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('director.income.show', $report) }}" class="p-1 px-2 border border-[#E0E0E0] rounded-[3px] text-[9px] font-bold text-gray-500 hover:bg-[#F0F0F0] uppercase tracking-tighter transition">View</a>
                                        @if(in_array($report->status, ['draft', 'rejected']))
                                            <a href="{{ route('director.income.edit', $report) }}" class="p-1 px-2 border border-[#0F6CBD]/20 rounded-[3px] text-[9px] font-bold text-[#0F6CBD] hover:bg-[#0F6CBD]/5 uppercase tracking-tighter transition">Edit</a>
                                            
                                            <form id="delete-income-{{ $report->id }}" action="{{ route('director.income.destroy', $report) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button" 
                                                    @click="$dispatch('open-confirm', { 
                                                        title: 'Delete Report', 
                                                        message: 'Are you sure you want to permanently delete this income report?', 
                                                        type: 'danger',
                                                        confirmText: 'Delete Protocol',
                                                        action: () => document.getElementById('delete-income-{{ $report->id }}').submit() 
                                                    })"
                                                    class="p-1 px-2 border border-[#D1102A]/20 rounded-[3px] text-[9px] font-bold text-[#D1102A] hover:bg-[#D1102A]/5 uppercase tracking-tighter transition">
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 rounded-full bg-[#FAFAFA] flex items-center justify-center mb-4 text-gray-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">No Income Records Found</p>
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
