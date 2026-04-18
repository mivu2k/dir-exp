<nav x-data="{ open: false }" class="sidebar-rail flex flex-col items-center py-6">
    <!-- Office 'Waffle' / Branding -->
    <div class="mb-10">
        <a href="{{ route('dashboard') }}" class="w-10 h-10 bg-[#0F6CBD] rounded-[4px] flex items-center justify-center text-white shadow-sm hover:bg-[#115EA3] transition duration-200" title="Dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex-grow flex flex-col space-y-4">

        {{-- Dashboard: everyone --}}
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home" title="Dashboard" />

        {{-- Ledger + Reports: admin, director, accountant --}}
        @hasanyrole('admin|director|accountant')
            <x-sidebar-link :href="route('ledger.index')" :active="request()->routeIs('ledger.*')" icon="ledger" title="Ledger" />
            <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" icon="ledger" title="Reports" />
        @endhasanyrole

        {{-- Admin-only section --}}
        @role('admin')
            <div class="h-[1px] w-8 bg-[#E0E0E0] mx-auto my-2"></div>
            <x-sidebar-link :href="route('admin.users.index')"     :active="request()->routeIs('admin.users.*')"     icon="users"     title="Users" />
            <x-sidebar-link :href="route('admin.coa.index')"       :active="request()->routeIs('admin.coa.*')"       icon="coa"       title="Accounts" />
            <x-sidebar-link :href="route('admin.approvals.index')" :active="request()->routeIs('admin.approvals.*')" icon="approvals" title="Approvals" />
        @endrole

        {{-- Expenses & Income: admin + director (create/manage) --}}
        @hasanyrole('admin|director')
            <div class="h-[1px] w-8 bg-[#E0E0E0] mx-auto my-2"></div>
            <x-sidebar-link :href="route('director.reports.index')" :active="request()->routeIs('director.reports.*')" icon="reports" title="Expenses" />
            <x-sidebar-link :href="route('director.income.index')"  :active="request()->routeIs('director.income.*')"  icon="income"  title="Income" />
        @endhasanyrole

        {{-- Accountant sees reports in read-only (no mutate access) --}}
        @role('accountant')
            <div class="h-[1px] w-8 bg-[#E0E0E0] mx-auto my-2"></div>
            <x-sidebar-link :href="route('director.reports.index')" :active="request()->routeIs('director.reports.*')" icon="reports" title="Exp. Reports" />
            <x-sidebar-link :href="route('director.income.index')"  :active="request()->routeIs('director.income.*')"  icon="income"  title="Inc. Reports" />
        @endrole

    </div>

    <!-- User & Exit -->
    <div class="flex flex-col space-y-6 items-center border-t border-[#E0E0E0] pt-6 w-full px-2">
        <div class="w-8 h-8 rounded-full bg-[#EDEBE9] border border-[#E0E0E0] flex items-center justify-center text-[10px] font-bold text-[#242424] uppercase"
             title="{{ Auth::user()->name }}">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-gray-400 hover:text-[#D1102A] transition" title="Sign Out">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            </button>
        </form>
    </div>
</nav>
