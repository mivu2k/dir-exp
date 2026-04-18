<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sign in — Expense Manager</title>

        <!-- Premium Typography -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: { sans: ['Inter', 'sans-serif'] }
                    }
                }
            }
        </script>

        <style>
            body { font-family: 'Inter', sans-serif; background-color: #F0F0F0; color: #242424; }
        </style>
    </head>
    <body class="antialiased min-h-screen flex items-center justify-center p-6">

        <div class="w-full max-w-[440px] bg-white border border-[#E0E0E0] rounded-[4px] shadow-xl p-10">

            <!-- Branding -->
            <div class="mb-8">
                <div class="flex items-center space-x-2 mb-6">
                    <div class="w-8 h-8 rounded-[4px] bg-[#0F6CBD] flex items-center justify-center text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="text-sm font-bold uppercase tracking-widest text-[#0F6CBD]">Expense Manager</span>
                </div>

                <h1 class="text-2xl font-semibold leading-tight mb-1">Sign in</h1>
                <p class="text-[14px] text-gray-500">to continue to Fiscal Operations</p>
            </div>

            <!-- Actions -->
            <div class="space-y-3">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="block w-full text-center bg-[#0F6CBD] text-white text-sm font-semibold rounded-[4px] px-6 py-3 hover:bg-[#115EA3] transition shadow-sm">
                        Open Dashboard
                    </a>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                           class="block w-full text-center bg-[#0F6CBD] text-white text-sm font-semibold rounded-[4px] px-6 py-3 hover:bg-[#115EA3] transition shadow-sm">
                            Sign in
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Footer -->
            <div class="mt-8 text-right">
                <p class="text-[10px] text-gray-400 flex items-center justify-end">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    Private Access — Invitation Only
                </p>
            </div>

        </div>

    </body>
</html>
