<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Premium Typography (Inter/Fluent) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Tailwind -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
            <script>
                tailwind.config = {
                    theme: {
                        extend: {
                            fontFamily: { sans: ['Inter', 'sans-serif'] },
                            colors: {
                                microsoft: {
                                    blue: '#0F6CBD',
                                    border: '#E0E0E0',
                                    text: '#242424'
                                }
                            }
                        }
                    }
                }
            </script>
        @endif

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; background-color: #FAFAFA; color: #242424; }
            .login-card { background: white; border: 1px solid #E0E0E0; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
            /* Fluent Form Styling for Guest views */
            .fluent-input { border: 1px solid #E0E0E0; border-radius: 4px; padding: 6px 12px; transition: border-color 0.2s; }
            .fluent-input:focus { border-bottom: 2px solid #0F6CBD !important; outline: none; box-shadow: none !important; }
        </style>
    </head>
    <body class="antialiased font-sans text-gray-900 flex flex-col items-center justify-center min-h-screen p-6 sm:bg-[#F0F0F0]">
        <div class="mb-8">
            <a href="/" class="flex items-center space-x-2 text-[#242424]">
                <div class="w-8 h-8 rounded-[4px] bg-[#0F6CBD] flex items-center justify-center text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="text-sm font-bold uppercase tracking-widest">DIR<span class="text-[#0F6CBD]">EXPENSE</span></span>
            </a>
        </div>

        <div class="w-full sm:max-w-[440px] login-card p-10 bg-white shadow-xl">
            {{ $slot }}
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Enterprise Identity Platform 4.2</p>
        </div>
    </body>
</html>
