@props(['active', 'icon', 'title'])

@php
$classes = ($active ?? false)
            ? 'flex items-center justify-center w-12 h-12 rounded-[4px] bg-white border border-[#E0E0E0] text-[#0F6CBD] shadow-sm relative group transition duration-200'
            : 'flex items-center justify-center w-12 h-12 rounded-[4px] text-gray-500 hover:bg-[#EDEBE9] relative group transition duration-200';

$iconPaths = [
    'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    'ledger' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
    'users' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    'coa' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
    'approvals' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'reports' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'income' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
];
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPaths[$icon] ?? '' }}" />
    </svg>
    
    <!-- Microsoft Tooltip Style -->
    <div class="absolute left-14 px-3 py-1 bg-[#242424] text-white text-[10px] font-bold rounded-[4px] invisible group-hover:visible whitespace-nowrap shadow-xl z-50">
        {{ $title }}
        <div class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-[#242424] rotate-45 -z-10"></div>
    </div>

    @if($active ?? false)
        <div class="absolute -left-[1px] w-[3px] h-6 bg-[#0F6CBD] rounded-full"></div>
    @endif
</a>
