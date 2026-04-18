@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'fluent-input border-[#E0E0E0] focus:border-[#0F6CBD] focus:ring-0 rounded-[4px] shadow-sm text-sm py-2 px-3 bg-white w-full placeholder-gray-400 transition-all duration-200']) !!}>
