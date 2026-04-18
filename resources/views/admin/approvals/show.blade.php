<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-3xl font-black text-[#242424] tracking-tight">Review Expense Report</h2>
                <p class="text-[11px] font-bold text-[#0F6CBD] mt-1 uppercase tracking-[0.2em]">{{ $report->voucher_no }} • Version v4.2</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.approvals.index') }}" class="px-5 py-2.5 bg-white border border-[#E0E0E0] text-[10px] font-bold text-[#424242] uppercase tracking-widest rounded-[4px] hover:bg-[#F3F2F1] transition shadow-sm">
                    Back to List
                </a>
                <a href="{{ route('director.reports.print', ['expenseReport' => $report]) }}" class="px-5 py-2.5 bg-white border border-[#E0E0E0] text-[10px] font-bold text-[#242424] uppercase tracking-widest rounded-[4px] hover:bg-[#F3F2F1] transition flex items-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-[#0F6CBD]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Print Report
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-12" x-data="{ showRejectModal: false, showReverseModal: false }">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Left Side: Data & Itemization -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Report Header -->
                <div class="card-premium p-10 bg-white border-t-4 {{ $report->status == 'approved' ? 'border-[#107C10]' : ($report->status == 'rejected' ? 'border-[#D1102A]' : 'border-[#0F6CBD]') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <div class="space-y-6">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-[0.2em] mb-2 font-mono text-right md:text-left">Report Title</p>
                                <h3 class="text-2xl font-black text-[#242424] leading-tight">{{ $report->title }}</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-2 font-mono">Director</p>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-[#FAFAFA] border border-[#E0E0E0] flex items-center justify-center text-[10px] font-black">{{ substr($report->director->name, 0, 1) }}</div>
                                        <span class="text-sm font-bold text-[#242424]">{{ $report->director->name }}</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-2 font-mono">Billing Month</p>
                                    <p class="text-sm font-bold text-[#242424]">{{ date("F Y", mktime(0, 0, 0, $report->period_month, 1, $report->period_year)) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-[#FAFAFA] p-6 rounded-[4px] border border-[#E0E0E0] flex flex-col justify-between">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-2 font-mono">Total Amount</p>
                                <p class="text-4xl font-black text-[#242424] tracking-tighter">Rs. {{ number_format($report->lines->sum('amount'), 0) }}</p>
                            </div>
                            <div class="mt-4">
                                <span class="px-3 py-1 rounded-[4px] text-[10px] font-black uppercase tracking-widest border
                                    {{ $report->status == 'approved' ? 'bg-[#107C10]/10 text-[#107C10] border-[#107C10]/20' : 
                                       ($report->status == 'submitted' ? 'bg-[#0F6CBD]/10 text-[#0F6CBD] border-[#0F6CBD]/20' : 
                                       ($report->status == 'rejected' ? 'bg-[#D1102A]/10 text-[#D1102A] border-[#D1102A]/20' : 'bg-[#E0E0E0]/20 text-gray-400 border-gray-200')) }}">
                                    {{ $report->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 pt-8 border-t border-[#F3F2F1]">
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-3 font-mono">Notes</p>
                        <p class="text-sm text-[#424242] font-medium leading-relaxed italic bg-[#FAFAFA] p-4 rounded-[2px] border-l-4 border-[#E0E0E0]">
                            "{{ $report->notes ?? 'No notes provided.' }}"
                        </p>
                    </div>
                </div>

                <!-- Expense Items Table -->
                <div class="card-premium p-0 bg-white overflow-hidden border border-[#EDEBE9]">
                    <div class="px-8 py-5 bg-[#FAFAFA] border-b border-[#E0E0E0] flex justify-between items-center text-[10px] font-black text-[#242424] uppercase tracking-widest">
                        Expense Items
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left sticky-header">
                            <thead>
                                <tr class="bg-white border-b border-[#F0F0F0] shadow-sm">
                                    <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest">Date</th>
                                    <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest">Category</th>
                                    <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest">Description</th>
                                    <th class="px-8 py-4 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">Amount (PKR)</th>
                                    <th class="px-8 py-4 text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F9F9F9]">
                                @foreach($report->lines as $line)
                                    <tr class="hover:bg-[#FAFAFA] transition duration-150">
                                        <td class="px-8 py-5 text-[11px] font-bold text-gray-500 whitespace-nowrap">{{ $line->date->format('d M Y') }}</td>
                                        <td class="px-8 py-5">
                                            <span class="px-2 py-0.5 border border-[#E0E0E0] rounded-[2px] text-[9px] font-black text-[#424242] uppercase bg-[#FAFAFA]">
                                                {{ $line->category->name }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-[11px] font-semibold text-[#242424]">{{ $line->description }}</td>
                                        <td class="px-8 py-5 text-right font-black text-[12px] text-[#242424]">Rs. {{ number_format($line->amount, 0) }}</td>
                                        <td class="px-8 py-5 text-center">
                                            @if($line->attachment_url)
                                                <a href="{{ Storage::url($line->attachment_url) }}" target="_blank" class="text-[#0F6CBD] hover:text-[#115EA3] transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side: Action Sidecar -->
            <div class="lg:col-span-4 space-y-8">
                <div class="card-premium p-0 overflow-hidden bg-[#242424] shadow-xl sticky top-8">
                    <div class="p-8 space-y-8">
                        <div>
                            <p class="text-[10px] font-black text-[#0F6CBD] uppercase tracking-[0.2em] mb-4">Approval Actions</p>
                            <h4 class="text-xl font-black text-white tracking-tight">Report Review</h4>
                        </div>

                        @if($report->status == 'submitted')
                            <div class="space-y-4">
                                <form action="{{ route('admin.approvals.approve', $report) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            @click.prevent="$dispatch('open-confirm', { 
                                                title: 'Final Approval', 
                                                message: 'Are you sure you want to approve this expense report? This will lock the record for billing.', 
                                                confirmText: 'Approve & Lock',
                                                action: () => $el.form.submit() 
                                            })"
                                            class="w-full py-4 bg-[#107C10] text-white text-[11px] font-black uppercase tracking-[0.1em] rounded-[4px] hover:bg-[#0E6C0E] transition shadow-lg shadow-green-500/20">
                                        Approve Report 🔒
                                    </button>
                                </form>

                                <button @click="showRejectModal = true" class="w-full py-4 border border-[#D1102A]/30 text-[#D1102A] text-[11px] font-black uppercase tracking-[0.1em] rounded-[4px] hover:bg-[#D1102A]/5 transition">
                                    Reject Report ✕
                                </button>
                            </div>
                        @elseif($report->status == 'approved')
                            <div class="bg-[#2D2D2D] p-5 rounded-[4px] border border-[#107C10]/20 space-y-4">
                                <div>
                                    <p class="text-[9px] font-black text-[#107C10] uppercase tracking-widest mb-1">Approved By</p>
                                    <p class="text-sm font-bold text-white">{{ $report->reviewer?->name }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Date Approved</p>
                                    <p class="text-[11px] font-bold text-gray-400">{{ $report->reviewed_at?->format('d M Y @ H:i') }}</p>
                                </div>
                            </div>
                            <button @click="showReverseModal = true" class="w-full py-3 text-gray-500 text-[9px] font-black uppercase tracking-widest hover:text-white transition">
                                Revert to Draft
                            </button>
                        @elseif($report->status == 'rejected')
                            <div class="bg-[#D1102A]/10 border border-[#D1102A]/20 p-5 rounded-[4px]">
                                <p class="text-[9px] font-black text-[#D1102A] uppercase tracking-widest mb-2">Rejection Reason</p>
                                <p class="text-sm text-[#EDEBE9] italic font-medium">"{{ $report->rejection_reason }}"</p>
                            </div>
                        @endif
                    </div>

                    <div class="px-8 py-6 bg-[#1A1A1A] border-t border-white/5">
                        <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-4">Version History</p>
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-[11px] font-black text-gray-400">v{{ $report->version }}</div>
                            <div>
                                <p class="text-[10px] font-bold text-[#EDEBE9]">Active Version</p>
                                <p class="text-[9px] text-gray-500 uppercase font-black">Stable Record</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rejection Modal -->
        <div x-show="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="showRejectModal = false"></div>
                <div class="bg-white rounded-[4px] overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full border border-[#E0E0E0]">
                    <form action="{{ route('admin.approvals.reject', $report) }}" method="POST">
                        @csrf
                        <div class="px-8 py-6 border-b border-[#F0F0F0]">
                            <h3 class="text-xs font-black text-[#D1102A] uppercase tracking-[0.2em]">Rejection Reason</h3>
                        </div>
                        <div class="p-8">
                            <textarea name="rejection_reason" class="fluent-input w-full h-40 resize-none py-4 text-[13px] font-semibold" placeholder="Specify why this report is being rejected..." required></textarea>
                            <p class="mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-tight italic">Note: The director will be able to edit and re-submit this report.</p>
                        </div>
                        <div class="px-8 py-6 bg-[#FAFAFA] flex justify-end space-x-4">
                            <button type="button" @click="showRejectModal = false" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Cancel</button>
                            <button type="submit" class="bg-[#D1102A] text-white px-6 py-2.5 rounded-[4px] text-[10px] font-bold uppercase tracking-widest hover:bg-[#A10D20] transition shadow-lg shadow-red-500/10">Reject Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reversal Modal -->
        <div x-show="showReverseModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="showReverseModal = false"></div>
                <div class="bg-white rounded-[4px] overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full border border-[#E0E0E0]">
                    <form action="{{ route('admin.approvals.reverse', $report) }}" method="POST">
                        @csrf
                        <div class="px-8 py-6 border-b border-[#F0F0F0]">
                            <h3 class="text-xs font-black text-[#242424] uppercase tracking-[0.2em]">Revert to Draft</h3>
                        </div>
                        <div class="p-8 space-y-4">
                            <div class="p-4 bg-amber-50 border border-amber-100 rounded-[2px]">
                                <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest">⚠️ Warning</p>
                                <p class="text-[11px] font-bold text-amber-600 mt-2 leading-relaxed">This will unlock the approved report and move it back to the director's draft folder.</p>
                            </div>
                            <textarea name="reason" class="fluent-input w-full h-32 resize-none py-4 text-[12px]" placeholder="Reason for revert..." required></textarea>
                        </div>
                        <div class="px-8 py-6 bg-[#FAFAFA] flex justify-end space-x-4">
                            <button type="button" @click="showReverseModal = false" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Cancel</button>
                            <button type="submit" class="text-[#0F6CBD] text-[10px] font-black uppercase tracking-widest hover:underline">Confirm Revert</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
