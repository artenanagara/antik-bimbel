<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - TO SKD</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#EA580C',
                            50: '#FFF7ED',
                            100: '#FFEDD5',
                            200: '#FED7AA',
                            300: '#FDBA74',
                            400: '#FB923C',
                            500: '#F97316',
                            600: '#EA580C',
                            700: '#C2410C',
                            800: '#9A3412',
                            900: '#7C2D12',
                        },
                    },
                    boxShadow: {
                        'soft': '0 1px 2px 0 rgb(15 23 42 / 0.04), 0 1px 3px 0 rgb(15 23 42 / 0.06)',
                        'card': '0 1px 3px 0 rgb(15 23 42 / 0.05), 0 4px 12px -2px rgb(15 23 42 / 0.04)'
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [
                    ['\\(', '\\)'],
                    ['$', '$']
                ],
                displayMath: [
                    ['\\[', '\\]'],
                    ['$$', '$$']
                ]
            },
            svg: {
                fontCache: 'global'
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>
    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .rich-content p {
            margin: 0.35rem 0;
        }

        .rich-content ul,
        .rich-content ol {
            margin: 0.5rem 0;
            padding-left: 1.25rem;
        }

        .rich-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.75rem 0;
        }

        .rich-content td,
        .rich-content th {
            border: 1px solid rgb(203 213 225);
            padding: 0.5rem;
            vertical-align: top;
        }

        /* Custom radio */
        .c-radio {
            appearance: none;
            -webkit-appearance: none;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid rgb(203 213 225);
            border-radius: 9999px;
            background: #fff;
            cursor: pointer;
            display: inline-grid;
            place-content: center;
            transition: border-color .15s ease, background-color .15s ease, box-shadow .15s ease;
            flex-shrink: 0;
            margin: 0;
            position: relative;
        }
        .c-radio::before {
            content: "";
            width: 0.6rem;
            height: 0.6rem;
            border-radius: 9999px;
            background: #EA580C;
            transform: scale(0);
            transition: transform .15s ease;
        }
        .c-radio:hover:not(:disabled) { border-color: #FDBA74; }
        .c-radio:checked {
            border-color: #EA580C;
            background: #FFF7ED;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.10);
        }
        .c-radio:checked::before { transform: scale(1); }
        .c-radio:focus-visible { outline: 2px solid #FB923C; outline-offset: 2px; }
        .c-radio:disabled { opacity: .5; cursor: not-allowed; }
        .c-radio-lg { width: 1.5rem; height: 1.5rem; }
        .c-radio-lg::before { width: 0.75rem; height: 0.75rem; }
    </style>
    @stack('styles')
</head>

<body class="h-full text-slate-800">
    <div class="min-h-full">
        {{-- Top nav --}}
        <nav class="bg-white/80 backdrop-blur border-b border-slate-200 sticky top-0 z-40">
            <div class="max-w-6xl mx-auto px-3 sm:px-6">
                <div class="flex items-center justify-between h-14 sm:h-16 gap-2">
                    <a href="{{ route('student.dashboard') }}" class="flex items-center gap-2 sm:gap-2.5 min-w-0">
                        <span
                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-sm shrink-0">
                            <i data-lucide="graduation-cap" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                        </span>
                        <span class="font-bold text-slate-900 text-sm sm:text-base">TO SKD</span>
                    </a>

                    {{-- Desktop nav --}}
                    <div class="hidden md:flex items-center gap-1 bg-slate-100/70 rounded-full p-1">
                        @php
                            $navItems = [
                                [
                                    'route' => 'student.dashboard',
                                    'pattern' => 'student.dashboard',
                                    'label' => 'Dashboard',
                                ],
                                [
                                    'route' => 'student.tryouts.index',
                                    'pattern' => 'student.tryouts.*',
                                    'label' => 'Try Out',
                                ],
                                [
                                    'route' => 'student.history.index',
                                    'pattern' => 'student.history.*',
                                    'label' => 'Riwayat',
                                ],
                            ];
                        @endphp
                        @foreach ($navItems as $n)
                            @php $active = request()->routeIs($n['pattern']) @endphp
                            <a href="{{ route($n['route']) }}"
                                class="px-4 py-1.5 rounded-full text-sm font-medium transition-all
                                    {{ $active ? 'bg-white text-primary-700 shadow-soft' : 'text-slate-600 hover:text-slate-900' }}">
                                {{ $n['label'] }}
                            </a>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-1.5 sm:gap-3">
                        <div class="hidden md:flex items-center gap-2.5">
                            <div
                                class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-slate-700 max-w-[140px] truncate">{{ auth()->user()->name }}</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="hidden md:block">
                            @csrf
                            <button type="submit" title="Keluar"
                                class="p-2 rounded-lg text-slate-500 hover:text-red-600 hover:bg-slate-100 transition-colors">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                            </button>
                        </form>
                        <button onclick="document.getElementById('mob-menu').classList.toggle('hidden')"
                            class="md:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100" aria-label="Menu">
                            <i data-lucide="menu" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                {{-- Mobile menu --}}
                <div id="mob-menu" class="md:hidden hidden pb-3 space-y-1 border-t border-slate-100 pt-3">
                    <div class="flex items-center gap-2.5 px-3 py-2 mb-1">
                        <div
                            class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-sm font-semibold shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500 truncate">Siswa</p>
                        </div>
                    </div>
                    @foreach ($navItems as $n)
                        @php $active = request()->routeIs($n['pattern']) @endphp
                        <a href="{{ route($n['route']) }}"
                            class="block px-3 py-2.5 rounded-lg text-sm font-medium {{ $active ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50' }}">
                            {{ $n['label'] }}
                        </a>
                    @endforeach
                    <form action="{{ route('logout') }}" method="POST" class="pt-1 border-t border-slate-100 mt-2">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50">
                            <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <main class="max-w-6xl mx-auto px-3 sm:px-6 py-4 sm:py-8">
            @if (session('success'))
                <div
                    class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm shadow-soft">
                    <span class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                    </span>
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div
                    class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm shadow-soft">
                    <span class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600"></i>
                    </span>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>

</html>
