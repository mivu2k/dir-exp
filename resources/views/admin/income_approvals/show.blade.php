<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-3xl font-black text-[#242424] tracking-tight">Review Revenue Deposit</h2>
                <p class="text-[11px] font-bold text-emerald-600 mt-1 uppercase tracking-[0.2em]">{{ $incomeReport->voucher_no }} • Verification Node v4.2</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.income_approvals.index') }}" class="px-5 py-2.5 bg-white border border-[#E0E0E0] text-[10px] font-bold text-[#424242] uppercase tracking-widest rounded-[4px] hover:bg-[#F3F2F1] transition shadow-sm">
                    Back to Queue
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-12">
        @if($incomeReport->status == 'submitted')
        <!-- Action Control Panel -->
        <div class="card-premium p-10 bg-white border-b-4 border-emerald-600 shadow-xl">
            <div class="flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="space-y-2">
                    <h3 class="text-xl font-black text-[#242424] uppercase tracking-tight">Executive Decision Required</h3>
                    <p class="text-sm text-gray-500 font-medium">Verify the revenue entry details before committing to the fiscal ledger.</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button @click="$dispatch('open-modal', 'reject-modal')" class="px-8 py-3 bg-white border border-[#D1102A] text-[11px] font-black text-[#D1102A] uppercase tracking-widest rounded-[4px] hover:bg-red-50 transition">
                        Reject Report
                    </button>
                    <form action="{{ route('admin.income_approvals.approve', $incomeReport) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-10 py-3 bg-emerald-600 text-white text-[11px] font-black uppercase tracking-widest rounded-[4px] hover:bg-emerald-700 transition shadow-lg shadow-emerald-500/20">
                            Approve Deposit
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Data Sidebar -->
            <div class="lg:col-span-4 space-y-8">
                <div class="card-premium p-8 bg-white border border-[#E0E0E0] shadow-sm">
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-[0.2em] mb-6 font-mono">Entity Overview</p>
                    <div class="space-y-6">
                        <div>
                            <p class="text-[9px] uppercase font-bold text-gray-400 mb-1">Director</p>
                            <p class="text-sm font-black text-[#242424]">{{ $incomeReport->director->name }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] uppercase font-bold text-gray-400 mb-1">Title</p>
                            <p class="text-sm font-black text-[#242424]">{{ $incomeReport->title }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[9px] uppercase font-bold text-gray-400 mb-1">Month</p>
                                <p class="text-sm font-black text-[#242424]">{{ date("M Y", mktime(0, 0, 0, $incomeReport->period_month, 1, $incomeReport->period_year)) }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] uppercase font-bold text-gray-400 mb-1">Total</p>
                                <p class="text-sm font-black text-emerald-600">Rs. {{ number_format($incomeReport->lines->sum('amount'), 0) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($incomeReport->notes)
                <div class="card-premium p-8 bg-[#FAFAFA] border border-[#E0E0E0]">
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-3 font-mono">Director Notes</p>
                    <p class="text-xs text-[#666] italic leading-relaxed">"{{ $incomeReport->notes }}"</p>
                </div>
                @endif
            </div>

            <!-- Itemization Grid -->
            <div class="lg:col-span-8">
                <div class="card-premium p-0 bg-white border border-[#EDEBE9] shadow-sm">
                    <div class="px-8 py-5 bg-[#FAFAFA] border-b border-[#E0E0E0] text-[10px] font-black text-[#242424] uppercase tracking-widest">
                        Transaction Breakdown
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-white border-b border-[#F0F0F0]">
                                    <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase w-32">Date</th>
                                    <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase w-48">Source</th>
                                    <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase">Description</th>
                                    <th class="px-8 py-4 text-right text-[9px] font-black text-gray-400 uppercase w-40">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F9F9F9]">
                                @foreach($incomeReport->lines as $line)
                                    <tr class="hover:bg-[#FAFAFA] transition duration-150">
                                        <td class="px-8 py-5 text-[11px] font-bold text-gray-500">{{ $line->date->format('d M Y') }}</td>
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
        </div>
    </div>

    <!-- Rejection Modal -->
    <x-modal name="reject-modal" :show="false" focusable>
        <form action="{{ route('admin.income_approvals.reject', $incomeReport) }}" method="POST" class="p-10">
            @csrf
            <h2 class="text-xl font-black text-[#242424] uppercase tracking-tight">Reject Revenue Deposit</h2>
            <p class="mt-2 text-sm text-gray-500 font-medium italic">Please provide a technical reason for returning this report for revision.</p>
            
            <div class="mt-8">
                <textarea name="reason" rows="4" class="fluent-input w-full text-sm font-bold resize-none" placeholder="e.g., Categorical mismatch or amount discrepancy..." required></textarea>
            </div>

            <div class="mt-10 flex justify-end space-x-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-6 py-2.5 bg-white border border-[#E0E0E0] text-[10px] font-bold text-[#424242] uppercase tracking-widest rounded-[4px] hover:bg-[#F3F2F1] transition">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-2.5 bg-[#D1102A] text-white text-[10px] font-black uppercase tracking-widest rounded-[4px] hover:bg-[#A80017] transition shadow-lg shadow-red-500/10">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
