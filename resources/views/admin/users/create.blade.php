<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#242424]">Register Personnel Identity</h2>
                <p class="text-xs font-semibold text-gray-400 mt-1 uppercase tracking-tight">Access Control Management • Directory Services</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="card-premium p-8 bg-white">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-8">
                    @csrf

                    <div class="flex items-center space-x-2 mb-8 pb-4 border-b border-[#F0F0F0]">
                        <div class="w-1.5 h-6 bg-[#0F6CBD] rounded-full"></div>
                        <h3 class="text-xs font-bold text-[#242424] uppercase tracking-widest">Identity Fundamental Details</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" value="Legal Designation (Name)" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                            <input id="name" name="name" type="text" value="{{ old('name') }}" class="fluent-input w-full" required autofocus>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" value="Operational Communication (Email)" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                            <input id="email" name="email" type="email" value="{{ old('email') }}" class="fluent-input w-full" required>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Department -->
                        <div>
                            <x-input-label for="department" value="Organizational Alignment" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                            <input id="department" name="department" type="text" value="{{ old('department') }}" class="fluent-input w-full" placeholder="e.g., Executive Operations">
                            <x-input-error :messages="$errors->get('department')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div>
                            <x-input-label for="role" value="Access Authorization Level" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                            <select id="role" name="role" class="fluent-input fluent-select w-full font-bold" required>
                                <option value="">Select Protocol...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ strtoupper($role->name) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div>
                            <x-input-label for="password" value="Access Credential (Password)" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                            <input id="password" name="password" type="password" class="fluent-input w-full" required>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="password_confirmation" value="Verify Credential" class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2" />
                            <input id="password_confirmation" name="password_confirmation" type="password" class="fluent-input w-full" required>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-[#F0F0F0]">
                        <a href="{{ route('admin.users.index') }}" class="px-6 py-2 border border-[#E0E0E0] text-[10px] font-bold text-[#424242] uppercase rounded-[4px] hover:bg-[#F0F0F0] transition tracking-widest">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-2 bg-[#0F6CBD] text-white text-[10px] font-bold uppercase rounded-[4px] hover:bg-[#115EA3] transition tracking-widest shadow-lg shadow-blue-500/10">
                            Register Identity
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
