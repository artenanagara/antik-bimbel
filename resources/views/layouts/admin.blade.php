<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - TO SKD Bimbel</title>
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

        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: rgb(203 213 225);
            border-radius: 3px;
        }

        .rich-btn {
            display: inline-flex;
            min-height: 2rem;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgb(71 85 105);
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .rich-btn:hover {
            background: white;
            color: rgb(30 41 59);
        }

        [data-rich-surface]:empty::before {
            content: attr(data-placeholder);
            color: rgb(148 163 184);
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

        [data-rich-surface] p {
            margin: 0.35rem 0;
        }

        [data-rich-surface] ul,
        [data-rich-surface] ol {
            margin: 0.5rem 0;
            padding-left: 1.25rem;
        }

        [data-rich-surface] table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.75rem 0;
        }

        [data-rich-surface] td,
        [data-rich-surface] th {
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
    <div class="flex h-full">
        {{-- Mobile backdrop --}}
        <div id="sidebar-backdrop" onclick="toggleSidebar(false)"
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 hidden lg:hidden"></div>

        {{-- Sidebar --}}
        <aside id="sidebar"
            class="fixed lg:static inset-y-0 left-0 z-40 w-72 bg-white border-r border-slate-200 shrink-0 flex flex-col -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-out">
            <div class="h-16 flex items-center px-5 border-b border-slate-100">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <span
                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-sm">
                        <i data-lucide="graduation-cap" class="w-5 h-5 text-white"></i>
                    </span>
                    <div class="leading-tight">
                        <p class="font-bold text-slate-900 text-sm">TO SKD Bimbel</p>
                        <p class="text-[11px] text-slate-500 font-medium">Admin Panel</p>
                    </div>
                </a>
            </div>
            <nav class="flex-1 px-3 py-5 space-y-0.5 overflow-y-auto scrollbar-thin">
                <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Menu</p>
                @php
                    $menu = [
                        ['route' => 'admin.dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
                        ['route' => 'admin.students.index', 'icon' => 'users', 'label' => 'Siswa'],
                        ['route' => 'admin.batches.index', 'icon' => 'layers', 'label' => 'Batch'],
                        ['route' => 'admin.questions.index', 'icon' => 'book-open', 'label' => 'Bank Soal'],
                        ['route' => 'admin.tryouts.index', 'icon' => 'clipboard-list', 'label' => 'Try Out'],
                        ['route' => 'admin.results.index', 'icon' => 'bar-chart-3', 'label' => 'Hasil'],
                    ];
                @endphp
                @foreach ($menu as $item)
                    @php $active = request()->routeIs($item['route'] . '*') @endphp
                    <a href="{{ route($item['route']) }}"
                        class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                          {{ $active ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        @if ($active)
                            <span
                                class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-r-full"></span>
                        @endif
                        <i data-lucide="{{ $item['icon'] }}"
                            class="w-[18px] h-[18px] shrink-0 {{ $active ? 'text-primary-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- Navbar: logo (mobile) + profile --}}
            <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 shrink-0 sticky top-0 z-20">
                <button onclick="toggleSidebar(true)"
                    class="lg:hidden p-2 -ml-2 rounded-lg text-slate-500 hover:bg-slate-100">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                {{-- Desktop: kosong di kiri karena sidebar tampil --}}
                <div class="hidden lg:block"></div>

                {{-- Profile dropdown --}}
                <div class="relative" id="profile-dropdown">
                    <button type="button" onclick="toggleProfileMenu()"
                        class="flex items-center gap-2.5 pl-2 pr-3 py-1.5 rounded-xl hover:bg-slate-50 transition select-none">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-sm font-bold shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="text-left hidden sm:block">
                            <p class="text-sm font-semibold text-slate-800 leading-tight">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-slate-400">Administrator</p>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200" id="profile-chevron"></i>
                    </button>

                    {{-- Dropdown menu --}}
                    <div id="profile-menu"
                        class="hidden absolute right-0 top-full mt-2 w-52 bg-white border border-slate-200 rounded-2xl shadow-lg overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ auth()->user()->username ?? 'Administrator' }}</p>
                        </div>
                        <div class="p-1.5">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-red-600 font-medium hover:bg-red-50 transition">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page header: back + title + actions --}}
            @if (View::hasSection('page-title') || View::hasSection('back') || View::hasSection('header-actions'))
            <div class="px-4 sm:px-6 lg:px-8 pt-6 pb-2 flex items-center justify-between gap-4 shrink-0">
                <div class="flex items-center gap-2.5 min-w-0">
                    @hasSection('back')
                        @yield('back')
                    @endif
                    <div class="min-w-0">
                        <h1 class="text-base font-semibold text-slate-900 truncate leading-tight">@yield('page-title', 'Dashboard')</h1>
                        @hasSection('page-subtitle')
                            <p class="text-xs text-slate-400 truncate mt-0.5">@yield('page-subtitle')</p>
                        @endif
                    </div>
                </div>
                @hasSection('header-actions')
                    <div class="flex items-center gap-2 shrink-0">
                        @yield('header-actions')
                    </div>
                @endif
            </div>
            @endif

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                {{-- Flash messages --}}
                @if (session('success'))
                    <div
                        class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm shadow-soft">
                        <span class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        </span>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error') || $errors->any())
                    <div
                        class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm shadow-soft">
                        <span class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600"></i>
                        </span>
                        <div class="flex-1">
                            @if (session('error'))
                                <p>{{ session('error') }}</p>
                            @endif
                            @if ($errors->any())
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    <script>
        lucide.createIcons();

        function toggleProfileMenu() {
            const menu = document.getElementById('profile-menu');
            const chevron = document.getElementById('profile-chevron');
            const isHidden = menu.classList.contains('hidden');
            menu.classList.toggle('hidden', !isHidden);
            chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('profile-dropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                document.getElementById('profile-menu').classList.add('hidden');
                document.getElementById('profile-chevron').style.transform = '';
            }
        });

        function toggleSidebar(open) {
            const sb = document.getElementById('sidebar');
            const bd = document.getElementById('sidebar-backdrop');
            if (open) {
                sb.classList.remove('-translate-x-full');
                bd.classList.remove('hidden');
            } else {
                sb.classList.add('-translate-x-full');
                bd.classList.add('hidden');
            }
        }
    </script>
    @stack('scripts')
</body>

</html>
