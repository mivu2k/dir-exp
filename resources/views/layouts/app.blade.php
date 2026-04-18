<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Expense Manager') }}</title>

        <!-- Premium Typography (Microsoft 365 Professional) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Fluent UI Styles -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
            <script>
                tailwind.config = {
                    theme: {
                        extend: {
                            fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
                            colors: {
                                microsoft: {
                                    blue: '#0F6CBD',    /* Fluent UI Brand */
                                    deep: '#115EA3',
                                    bg: '#FFFFFF',
                                    surface: '#FAFAFA',
                                    border: '#E0E0E0',
                                    text: '#242424',
                                    secondary: '#424242'
                                }
                            },
                            borderRadius: {
                                'fluent': '4px'
                            },
                            boxShadow: {
                                'fluent': '0 2px 4px rgba(0,0,0,0.14)'
                            }
                        }
                    }
                }
            </script>
        @endif

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; background-color: #FAFAFA; color: #242424; }
            .sidebar-rail { width: 72px; background-color: #FAFAFA; border-right: 1px solid #E0E0E0; position: fixed; height: 100vh; z-index: 50; }
            .content-workspace { margin-left: 72px; flex-grow: 1; min-height: 100vh; background-color: #FFFFFF; }
            .card-premium { background: white; border: 1px solid #E0E0E0; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); padding: 2rem; }
            /* Fluent Form Styling - Premium Microsoft 365 Aesthetic */
            .fluent-input { 
                border: 1px solid #E0E0E0; 
                border-radius: 4px; 
                padding: 10px 16px; 
                font-size: 14px;
                background-color: #FFFFFF;
                transition: border-bottom-color 0.1s ease, background-color 0.2s;
                height: 40px;
                display: flex;
                align-items: center;
            }
            .fluent-input:hover { background-color: #F3F2F1; border-color: #C8C6C4; }
            .fluent-input:focus { border-bottom: 2px solid #0F6CBD !important; outline: none; background-color: #FFFFFF; }

            .fluent-select {
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23605e5d' d='M2.22 4.47a.75.75 0 0 1 1.06 0L6 7.19l2.72-2.72a.75.75 0 1 1 1.06 1.06L6.53 8.78a.75.75 0 0 1-1.06 0L2.22 5.53a.75.75 0 0 1 0-1.06z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 12px center;
                padding-right: 32px !important;
                line-height: 1;
            }
            .sticky-header th { position: sticky; top: 0; z-index: 10; scroll-margin-top: 0; }
        </style>
    </head>
    <body class="antialiased">
        <!-- Global Confirmation Modal -->
        <div x-data="{ 
                open: false, 
                title: '', 
                message: '', 
                confirmText: 'Confirm',
                type: 'primary',
                action: null,
                trigger(detail) {
                    this.title = detail.title || 'Are you sure?';
                    this.message = detail.message || 'Please confirm this action.';
                    this.confirmText = detail.confirmText || 'Confirm';
                    this.type = detail.type || 'primary';
                    this.action = detail.action;
                    this.open = true;
                },
                proceed() {
                    if (this.action) this.action();
                    this.open = false;
                }
            }"
            @open-confirm.window="trigger($event.detail)"
            class="relative z-[9999]"
            x-cloak>
            
            <!-- Backdrop -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/40 backdrop-blur-[2px]"></div>

            <!-- Modal Content -->
            <div x-show="open"
                 class="fixed inset-0 flex items-center justify-center p-4">
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                     @click.away="open = false"
                     class="bg-white rounded-[4px] shadow-2xl border border-[#E0E0E0] max-w-sm w-full overflow-hidden">
                    
                    <div class="p-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-1.5 h-6 rounded-full" :class="type === 'danger' ? 'bg-[#D1102A]' : 'bg-[#0F6CBD]'"></div>
                            <h3 class="text-xs font-black uppercase tracking-widest text-[#242424]" x-text="title">Confirm Action</h3>
                        </div>
                        <p class="text-sm font-medium text-[#424242] leading-relaxed" x-text="message">Are you sure you want to proceed?</p>
                    </div>

                    <div class="bg-[#FAFAFA] px-6 py-4 flex items-center justify-end space-x-3 border-t border-[#E0E0E0]">
                        <button @click="open = false" class="px-4 py-2 text-[10px] font-bold text-[#424242] uppercase tracking-wider hover:bg-[#F0F0F0] transition rounded-[4px]">
                            Cancel
                        </button>
                        <button @click="proceed()" 
                                :class="type === 'danger' ? 'bg-[#D1102A] hover:bg-[#A80000]' : 'bg-[#0F6CBD] hover:bg-[#115EA3]'"
                                class="px-6 py-2 text-[10px] font-black text-white uppercase tracking-wider transition rounded-[4px] shadow-lg shadow-blue-500/10">
                            <span x-text="confirmText">Confirm</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="min-h-screen flex text-[13px]">
            <!-- Navigation Sidebar (Microsoft 365 Style Rail) -->
            @include('layouts.navigation')

            <!-- App Workspace -->
            <div class="content-workspace flex flex-col">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white border-b border-[#E0E0E0]">
                        <div class="max-w-7xl mx-auto py-8 px-8 sm:px-12">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-grow py-10 px-8 sm:px-12 bg-[#FAFAFA]">
                    <div class="max-w-7xl mx-auto space-y-8">
                        <!-- Alerts & Notifications -->
                        @if (session('success'))
                            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-[4px] font-bold text-xs flex items-center space-x-2 animate-in fade-in slide-in-from-top-4 duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-[4px] font-bold text-xs flex items-center space-x-2 animate-in fade-in slide-in-from-top-4 duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-[4px] text-[11px] animate-in fade-in slide-in-from-top-4 duration-300">
                                <div class="font-black uppercase tracking-widest mb-2 flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    <span>Validation Error Details</span>
                                </div>
                                <ul class="list-disc list-inside font-bold space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </main>
                
                <footer class="mt-auto border-t border-[#EDEBE9] bg-white">
                    <div class="max-w-7xl mx-auto px-10 py-3 flex flex-col sm:flex-row items-center justify-between gap-4">
                        {{-- Left: Record Status & App Identity --}}
                        <div class="flex items-center gap-4 text-[10px] uppercase font-bold tracking-widest text-[#616161]">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 bg-[#0F6CBD] rounded-[2px] flex items-center justify-center">
                                    <div class="w-1 h-1 bg-white rounded-full transition-all"></div>
                                </div>
                                <span class="text-[#242424]">DIR-EXPENSE</span>
                                <span class="text-[#0F6CBD] opacity-50 px-1 border-l border-[#EDEBE9]">v4.2</span>
                            </div>
                            <span class="hidden sm:inline opacity-30">|</span>
                            <span class="opacity-80">© {{ date('Y') }} Protocol Secured</span>
                        </div>

                        {{-- Right: Context & Active Session --}}
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-r border-[#EDEBE9] pr-4">
                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,124,16,0.5)]"></div>
                                <span>Session Active</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <p class="text-[11px] font-black text-[#242424]">{{ Auth::user()->name }}</p>
                                @php $role = Auth::user()->getRoleNames()->first() ?? 'user'; @endphp
                                <span class="px-2 py-0.5 rounded-[2px] text-[8.5px] font-black uppercase tracking-[0.1em] border
                                    @if($role === 'admin') bg-amber-50 text-amber-700 border-amber-200
                                    @elseif($role === 'director') bg-blue-50 text-blue-700 border-blue-200
                                    @else bg-emerald-50 text-emerald-700 border-emerald-200 @endif leading-none">
                                    {{ $role }}
                                </span>
                            </div>
                        </div>
                    </div>
                </footer>

            </div>
        </div>
    </body>
</html>
