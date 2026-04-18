<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#242424]">Dashboard</h2>
                <p class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-tight">Fiscal Operations Summary</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-2 py-0.5 bg-[#F0F0F0] border border-[#E0E0E0] rounded-[4px] text-[10px] font-bold text-[#424242]">Enterprise Active</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Dashboard Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Metric 1: Income MTD -->
            <div class="card-premium p-6 border-t-4 border-emerald-600 bg-white shadow-sm">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-8 h-8 rounded-[4px] bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="text-[11px] font-bold text-[#424242] uppercase tracking-wide">Monthly Revenue</span>
                </div>
                <div class="text-3xl font-black text-emerald-700">Rs. {{ number_format($stats['income_mtd'], 0) }}</div>
                <p class="mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-tight">Approved Inflow</p>
            </div>

            <!-- Metric 2: Expenses MTD -->
            <div class="card-premium p-6 border-t-4 border-amber-500 bg-white shadow-sm">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-8 h-8 rounded-[4px] bg-amber-50 flex items-center justify-center text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                    </div>
                    <span class="text-[11px] font-bold text-[#424242] uppercase tracking-wide">Monthly Spend</span>
                </div>
                <div class="text-3xl font-black text-amber-700">Rs. {{ number_format($stats['expense_mtd'], 0) }}</div>
                <p class="mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-tight">Approved Outflow</p>
            </div>

            <!-- Metric 3: Net Balance -->
            <div class="card-premium p-6 border-t-4 border-[#0F6CBD] bg-white shadow-sm">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-8 h-8 rounded-[4px] bg-blue-50 flex items-center justify-center text-[#0F6CBD]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <span class="text-[11px] font-bold text-[#424242] uppercase tracking-wide">Net Position</span>
                </div>
                <div class="text-3xl font-black text-[#242424] tracking-tighter">Rs. {{ number_format($stats['income_mtd'] - $stats['expense_mtd'], 0) }}</div>
                <p class="mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-tight">MTD Balance</p>
            </div>

            <!-- Metric 4: Pending Actions -->
            <div class="card-premium p-6 bg-white overflow-hidden relative shadow-sm">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-8 h-8 rounded-[4px] bg-[#242424]/5 flex items-center justify-center text-[#242424]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="text-[11px] font-bold text-[#424242] uppercase tracking-wide">Pending Actions</span>
                </div>
                <div class="flex items-baseline space-x-4">
                    <div class="text-3xl font-black text-[#242424]">{{ $stats['pending_expenses'] + $stats['pending_income'] }}</div>
                    <div class="text-[9px] font-black uppercase text-gray-400 tracking-tighter">
                        <span class="text-emerald-600">{{ $stats['pending_income'] }} INC</span> / 
                        <span class="text-amber-600">{{ $stats['pending_expenses'] }} EXP</span>
                    </div>
                </div>
                @role('admin|accountant')
                <a href="{{ route('admin.approvals.index') }}" class="mt-4 flex items-center text-[10px] text-[#0F6CBD] font-black hover:underline uppercase tracking-widest">
                    <span>Approval Hub</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </a>
                @endrole
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Activity -->
            <div class="lg:col-span-2 space-y-4">
                <div class="px-2 flex items-center justify-between">
                    <h3 class="text-xs font-black text-[#242424] uppercase tracking-wider">Recent Activity Log</h3>
                    <a href="{{ route('ledger.index') }}" class="text-[9px] font-black text-[#0F6CBD] uppercase tracking-widest hover:underline">View History</a>
                </div>
                
                <div class="card-premium overflow-hidden bg-white shadow-sm border border-[#EDEBE9]">
                    <div class="divide-y divide-[#F3F2F1]">
                        @forelse($recentReports as $report)
                            <div class="p-6 hover:bg-[#FAFAFA] transition group relative">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-6">
                                        <div class="w-10 h-10 rounded-[4px] bg-[#F0F0F0] flex items-center justify-center text-[11px] font-bold text-gray-500 uppercase">
                                            {{ substr($report->director->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-[#242424] leading-none mb-2">{{ $report->title }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">{{ $report->voucher_no }} • {{ $report->updated_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-black text-[#242424] mb-2">Rs. {{ number_format($report->lines->sum('amount'), 0) }}</p>
                                        <span class="px-2 py-0.5 rounded-[4px] text-[9px] font-black uppercase border
                                            {{ $report->status == 'approved' ? 'bg-[#107C10]/10 text-[#107C10] border-[#107C10]/20' : ($report->status == 'submitted' ? 'bg-[#0F6CBD]/10 text-[#0F6CBD] border-[#0F6CBD]/20' : 'bg-[#F0F0F0] text-gray-400 border-gray-200') }}">
                                            {{ $report->status }}
                                        </span>
                                    </div>
                                </div>
                                <div class="absolute left-0 top-0 h-full w-[3px] bg-transparent group-hover:bg-[#0F6CBD] transition-all"></div>
                            </div>
                        @empty
                            <div class="p-20 text-center text-gray-400 text-[11px] font-black uppercase tracking-widest">No recent data detected.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Budget & Quick Actions -->
            <div class="space-y-8">
                <div>
                    <h3 class="text-xs font-black text-[#242424] uppercase tracking-wider px-2 mb-4">Budget Thresholds</h3>
                    <div class="card-premium p-6 space-y-6 bg-white shadow-sm border border-[#EDEBE9]">
                        @forelse($budgetSummary as $coa)
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-[10px] font-bold text-[#424242] uppercase tracking-tight whitespace-nowrap overflow-hidden text-ellipsis mr-2">{{ $coa->name }}</span>
                                    <span class="text-[10px] font-black {{ $coa->percentage > 90 ? 'text-[#D1102A]' : 'text-[#0F6CBD]' }}">{{ number_format($coa->percentage, 1) }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-[#FAFAFA] border border-[#F0F0F0] rounded-[2px] overflow-hidden">
                                    <div class="h-full rounded-[2px] transition-all duration-700 {{ $coa->percentage > 90 ? 'bg-[#D1102A]' : ($coa->percentage > 70 ? 'bg-[#EFB007]' : 'bg-[#0F6CBD]') }}" style="width: {{ min($coa->percentage, 100) }}%"></div>
                                </div>
                                <div class="mt-2 text-[9px] font-bold text-gray-400 flex justify-between uppercase">
                                    <span>Used: {{ number_format($coa->spent, 0) }}</span>
                                    <span>Cap: {{ number_format($coa->budget_limit, 0) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-400 text-[10px] font-black uppercase tracking-widest italic border border-dashed border-[#E0E0E0] rounded-[2px]">Thresholds not defined.</div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-black text-[#242424] uppercase tracking-wider px-2 mb-4">Operational Shortcuts</h3>
                    <div class="space-y-3">
                        @role('director')
                            <a href="{{ route('director.income.create') }}" class="w-full flex items-center justify-between p-4 bg-emerald-600 text-white rounded-[4px] hover:bg-emerald-700 transition text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-500/10">
                                <span>Report New Income</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                            </a>
                            <a href="{{ route('director.reports.create') }}" class="w-full flex items-center justify-between p-4 bg-[#0F6CBD] text-white rounded-[4px] hover:bg-[#115EA3] transition text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-500/10">
                                <span>New Expense Voucher</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                            </a>
                        @endrole
                        <a href="{{ route('reports.index') }}" class="w-full flex items-center justify-between p-4 bg-white border border-[#E0E0E0] text-[#242424] rounded-[4px] hover:bg-[#FAFAFA] transition text-[10px] font-black uppercase tracking-widest shadow-sm">
                            <span>Intelligence Reports</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
