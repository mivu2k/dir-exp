<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#242424]">Personnel & Access Registry</h2>
                <p class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-tight">Enterprise Identity & Permission Management</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="px-6 py-2.5 bg-[#0F6CBD] text-white text-[10px] font-bold uppercase tracking-widest rounded-[4px] hover:bg-[#115EA3] transition shadow-lg shadow-blue-500/10">
                Register Colleague
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Search Workspace -->
        <div class="card-premium p-6 bg-white">
            <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <div class="flex-grow min-w-[300px] relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="fluent-input pl-10 block w-full text-[11px] font-semibold" placeholder="Search by name or email identity...">
                </div>
                <button type="submit" class="px-6 py-2.5 bg-[#FAFAFA] border border-[#E0E0E0] text-[#242424] text-[10px] font-bold uppercase tracking-widest rounded-[4px] hover:bg-[#F3F2F1] transition">
                    Search Registry
                </button>
            </form>
        </div>

        <!-- Global Personnel Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($users as $user)
                <div class="card-premium p-6 group hover:border-[#0F6CBD]/30 transition duration-300 relative">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-12 h-12 rounded-[4px] bg-[#F3F2F1] border border-[#E0E0E0] flex items-center justify-center text-lg font-black text-gray-400 group-hover:bg-[#0F6CBD]/5 group-hover:text-[#0F6CBD] transition">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($user->is_active)
                                <span class="w-2 h-2 rounded-full bg-[#107C10] shadow-[0_0_8px_rgba(16,124,16,0.3)]"></span>
                                <span class="text-[9px] font-black text-[#107C10] uppercase tracking-widest">Online</span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-[#D1102A]"></span>
                                <span class="text-[9px] font-black text-[#D1102A] uppercase tracking-widest">Suspended</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-sm font-black text-[#242424] truncate">{{ $user->name }}</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight mt-1 truncate">{{ $user->email }}</p>
                    </div>

                    <div class="pt-6 border-t border-[#F3F2F1] flex items-center justify-between">
                        <div class="flex flex-wrap gap-1">
                            @foreach($user->roles as $role)
                                <span class="px-2 py-0.5 rounded-[2px] bg-[#FAFAFA] border border-[#E0E0E0] text-[8px] font-black text-gray-400 uppercase tracking-widest">{{ $role->name }}</span>
                            @endforeach
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-[9px] font-black text-[#0F6CBD] uppercase tracking-widest hover:underline transition">Edit</a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[9px] font-black {{ $user->is_active ? 'text-[#D1102A]' : 'text-[#107C10]' }} uppercase tracking-widest hover:underline transition">
                                    {{ $user->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="absolute left-0 top-0 h-full w-[2px] bg-transparent group-hover:bg-[#0F6CBD] transition-all"></div>
                </div>
            @empty
                <div class="col-span-full card-premium p-20 text-center border-2 border-dashed border-[#E0E0E0]">
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">No team members located in current sync</p>
                </div>
            @endforelse
        </div>
        
        @if($users->hasPages())
            <div class="mt-8 bg-white card-premium py-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
