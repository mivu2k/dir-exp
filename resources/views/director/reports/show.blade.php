<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-3xl font-extrabold text-[#242424] tracking-tight">Expense Report Details</h2>
                <p class="text-[11px] font-bold text-[#0F6CBD] mt-1 uppercase tracking-[0.2em]">{{ $expenseReport->voucher_no }} • Version v4.2</p>
            </div>
            <div class="flex items-center space-x-3">
                @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant') || Auth::user()->hasRole('director'))
                    <a href="{{ route('director.reports.excel', ['expenseReport' => $expenseReport]) }}" class="px-5 py-2.5 bg-white border border-emerald-600/20 text-[10px] font-bold text-emerald-700 uppercase tracking-widest rounded-[4px] hover:bg-emerald-50 transition flex items-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Excel Export
                    </a>
                    <a href="{{ route('director.reports.print', ['expenseReport' => $expenseReport]) }}" class="px-5 py-2.5 bg-white border border-[#E0E0E0] text-[10px] font-bold text-[#242424] uppercase tracking-widest rounded-[4px] hover:bg-[#F3F2F1] transition flex items-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-[#0F6CBD]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        Print PDF
                    </a>
                @endif
                
                @if(in_array($expenseReport->status, ['draft', 'rejected']) && (Auth::user()->id === $expenseReport->director_id || Auth::user()->hasRole('admin')))
                    <a href="{{ route('director.reports.edit', ['expenseReport' => $expenseReport]) }}" class="px-6 py-2.5 bg-[#0F6CBD] text-white text-[10px] font-bold uppercase tracking-widest rounded-[4px] hover:bg-[#115EA3] transition shadow-lg shadow-blue-500/10">
                        Edit Report
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-12">
        <!-- Report Metadata -->
        <div class="card-premium p-10 bg-white grid grid-cols-1 md:grid-cols-3 gap-12 border border-[#EDEBE9]">
            <div class="md:col-span-2 space-y-8">
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-[0.2em] mb-2 font-mono">Report Title</p>
                    <h3 class="text-2xl font-black text-[#242424] leading-tight">{{ $expenseReport->title }}</h3>
                </div>
                
                <div class="grid grid-cols-2 gap-x-12 gap-y-8">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-2 font-mono">Director</p>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-[#FAFAFA] border border-[#E0E0E0] flex items-center justify-center text-[10px] font-black">{{ substr($expenseReport->director->name, 0, 1) }}</div>
                            <span class="text-sm font-bold text-[#242424]">{{ $expenseReport->director->name }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-2 font-mono">Billing Month</p>
                        <p class="text-sm font-bold text-[#242424]">{{ date("F Y", mktime(0, 0, 0, $expenseReport->period_month, 1, $expenseReport->period_year)) }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-2 font-mono">Notes</p>
                        <p class="text-sm text-[#424242] font-medium leading-relaxed bg-[#FAFAFA] p-4 rounded-[4px] border-l-4 border-[#E0E0E0] italic">
                            {{ $expenseReport->notes ?? 'No additional notes provided.' }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-[#F8F9FA] border border-[#EDEBE9] rounded-[4px] p-8 flex flex-col justify-between shadow-inner">
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-2 font-mono">Status</p>
                        <span class="px-4 py-1.5 rounded-[4px] text-[10px] font-black uppercase tracking-widest border
                            {{ $expenseReport->status == 'approved' ? 'bg-[#107C10]/10 text-[#107C10] border-[#107C10]/20' : 
                               ($expenseReport->status == 'submitted' ? 'bg-[#0F6CBD]/10 text-[#0F6CBD] border-[#0F6CBD]/20' : 
                               ($expenseReport->status == 'rejected' ? 'bg-[#D1102A]/10 text-[#D1102A] border-[#D1102A]/20' : 'bg-[#E0E0E0]/20 text-gray-400 border-gray-200')) }}">
                            {{ $expenseReport->status }}
                        </span>
                    </div>

                    @if($expenseReport->status == 'draft' && Auth::user()->id === $expenseReport->director_id)
                        <form action="{{ route('director.reports.update', $expenseReport) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="title" value="{{ $expenseReport->title }}">
                            <input type="hidden" name="submit_report" value="1">
                            @foreach($expenseReport->lines as $index => $line)
                                <input type="hidden" name="lines[{{$index}}][date]" value="{{ $line->date->format('Y-m-d') }}">
                                <input type="hidden" name="lines[{{$index}}][description]" value="{{ $line->description }}">
                                <input type="hidden" name="lines[{{$index}}][category_id]" value="{{ $line->category_id }}">
                                <input type="hidden" name="lines[{{$index}}][amount]" value="{{ $line->amount }}">
                                <input type="hidden" name="lines[{{$index}}][existing_attachment]" value="{{ $line->attachment_url }}">
                            @endforeach
                            <button type="submit" 
                                    @click.prevent="$dispatch('open-confirm', { 
                                        title: 'Final Submission', 
                                        message: 'Are you sure you want to submit this report for approval?', 
                                        confirmText: 'Submit Now',
                                        action: () => $el.form.submit() 
                                    })"
                                    class="w-full py-3 bg-[#107C10] text-white text-[11px] font-bold uppercase tracking-[0.1em] rounded-[4px] hover:bg-[#0E6C0E] transition shadow-lg shadow-green-500/10">
                                Submit for Approval
                            </button>
                        </form>
                    @endif
                </div>
                
                <div class="mt-12 flex flex-col items-end">
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-1 font-mono text-right w-full">Total Amount</p>
                    <p class="text-4xl font-black text-[#242424] tracking-tighter">Rs. {{ number_format($expenseReport->lines->sum('amount'), 0) }}</p>
                </div>
            </div>
        </div>

        @if($expenseReport->rejection_reason)
            <div class="p-6 bg-[#D1102A]/5 border border-[#D1102A]/20 rounded-[4px] flex items-center space-x-6">
                <div class="flex-shrink-0 text-[#D1102A]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-black text-[#D1102A] tracking-widest mb-1">Rejection Details</p>
                    <p class="text-sm text-[#D1102A] font-bold leading-relaxed">"{{ $expenseReport->rejection_reason }}"</p>
                </div>
            </div>
        @endif

        <!-- Expense Items Table -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div class="{{ (Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant')) ? 'lg:col-span-8' : 'lg:col-span-12' }} card-premium bg-white p-0 overflow-hidden border border-[#EDEBE9]">
                <div class="px-8 py-5 bg-[#FAFAFA] border-b border-[#EDEBE9] flex justify-between items-center text-[11px] font-black text-[#242424] uppercase tracking-widest">
                    Expense Items
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-sans">
                        <thead>
                            <tr class="bg-[#FAFAFA] border-b border-[#EDEBE9] text-[9px] uppercase font-bold text-gray-400 tracking-widest font-mono">
                                <th class="px-8 py-4">Date</th>
                                <th class="px-8 py-4">Category</th>
                                <th class="px-8 py-4">Description</th>
                                <th class="px-8 py-4 text-right">Amount (PKR)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F3F2F1]">
                            @foreach($expenseReport->lines as $line)
                                <tr class="hover:bg-[#FAFAFA] transition duration-150">
                                    <td class="px-8 py-5 text-[12px] font-bold text-gray-600 whitespace-nowrap">{{ $line->date->format('d M Y') }}</td>
                                    <td class="px-8 py-5">
                                        <span class="px-2 py-0.5 border border-[#E0E0E0] rounded-[2px] text-[10px] font-black text-[#424242] uppercase bg-[#FAFAFA]">
                                            {{ $line->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-[12px] font-medium text-[#242424]">{{ $line->description }}</td>
                                    <td class="px-8 py-5 text-right font-black text-sm text-[#242424]">Rs. {{ number_format($line->amount, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary Sidebar -->
            @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant'))
                <div class="lg:col-span-4 space-y-8">
                    <div class="card-premium bg-white p-8 overflow-hidden space-y-6">
                        <div class="text-[11px] font-black text-[#242424] uppercase tracking-widest border-b border-[#F3F2F1] pb-4">
                            Category Summary
                        </div>
                        <div class="space-y-6">
                            @foreach($categorySummary as $summary)
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-[11px] font-bold text-[#424242]">
                                        <span class="uppercase tracking-tight">{{ $summary['name'] }}</span>
                                        <span class="text-[#242424]">Rs. {{ number_format($summary['total'], 0) }}</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-[#F3F2F1] rounded-full overflow-hidden shadow-inner">
                                        <div class="h-full bg-[#0F6CBD] rounded-full" style="width: {{ $expenseReport->lines->sum('amount') > 0 ? ($summary['total'] / $expenseReport->lines->sum('amount')) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($expenseReport->status == 'approved')
                        <div class="card-premium p-8 bg-[#107C10]/5 border-[#107C10]/10 flex flex-col space-y-4">
                            <div class="text-[11px] font-black text-[#107C10] uppercase tracking-widest border-b border-[#107C10]/10 pb-2">Approval Details</div>
                            <div class="space-y-1">
                                <p class="text-[12px] font-bold text-[#242424]">Approved by <strong>{{ $expenseReport->reviewer->name }}</strong></p>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Date: {{ $expenseReport->reviewed_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
