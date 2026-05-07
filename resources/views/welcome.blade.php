<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'TO SKD Bimbel') }} — Persiapan Lulus CPNS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    },
                    colors: {
                        primary: {
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
                        soft: '0 6px 24px -8px rgba(234,88,12,0.30)'
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
        }

        .grid-bg {
            background-image: radial-gradient(circle at 1px 1px, rgba(249, 115, 22, 0.14) 1px, transparent 0);
            background-size: 24px 24px;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased">
    {{-- Nav --}}
    <header class="sticky top-0 z-40 bg-white/70 backdrop-blur border-b border-slate-200/70">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5">
                <span
                    class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-soft">
                    <i data-lucide="graduation-cap" class="w-5 h-5 text-white"></i>
                </span>
                <span class="font-bold text-slate-900">TO SKD Bimbel</span>
            </a>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="inline-flex items-center gap-2 bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-semibold px-4 py-2 rounded-xl shadow-soft transition">
                        Dashboard <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-semibold px-4 py-2 rounded-xl shadow-soft transition">
                        Masuk <i data-lucide="log-in" class="w-4 h-4"></i>
                    </a>
                @endauth
            </div>
        </nav>
    </header>

    {{-- Hero --}}
    <section class="relative overflow-hidden grid-bg">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary-200/40 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-amber-200/40 rounded-full blur-3xl"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
            <div class="max-w-3xl mx-auto text-center">
                <span
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-primary-50 text-primary-700 ring-1 ring-primary-200 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary-500 animate-pulse"></span>
                    Platform Try Out CPNS
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-tight">
                    Persiapan SKD CPNS, <span
                        class="bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">jadi lebih
                        terarah.</span>
                </h1>
                <p class="mt-6 text-lg text-slate-600 leading-relaxed">
                    Latihan TWK, TIU, dan TKP dengan simulasi CAT real-time, pembahasan lengkap, dan analisis hasil per
                    sub-test.
                </p>
                <div class="mt-8 flex items-center justify-center gap-3">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-semibold px-6 py-3 rounded-xl shadow-soft transition">
                        Mulai Try Out <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                    <a href="#fitur"
                        class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold px-6 py-3 rounded-xl transition">
                        Pelajari Fitur
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Sub-test cards --}}
    <section id="fitur" class="py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-sm font-semibold text-primary-600 uppercase tracking-wider">Tiga Sub Test SKD</p>
                <h2 class="mt-2 text-3xl sm:text-4xl font-bold text-slate-900">Latihan terstruktur per kategori</h2>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ([['icon' => 'flag', 'title' => 'TWK', 'desc' => 'Tes Wawasan Kebangsaan — Pancasila, UUD 1945, NKRI, Bhinneka Tunggal Ika, dan integritas.', 'gradient' => 'from-sky-500 to-blue-600', 'bg' => 'bg-blue-50'], ['icon' => 'brain', 'title' => 'TIU', 'desc' => 'Tes Intelegensi Umum — verbal, numerik, dan figural untuk mengukur kemampuan berpikir.', 'gradient' => 'from-amber-500 to-orange-600', 'bg' => 'bg-amber-50'], ['icon' => 'heart-handshake', 'title' => 'TKP', 'desc' => 'Tes Karakteristik Pribadi — pelayanan publik, jejaring, sosial budaya, profesionalisme.', 'gradient' => 'from-emerald-500 to-teal-600', 'bg' => 'bg-emerald-50']] as $c)
                    <div
                        class="group relative bg-white rounded-2xl border border-slate-200 p-6 hover:shadow-soft hover:-translate-y-0.5 transition-all">
                        <span
                            class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $c['gradient'] }} text-white flex items-center justify-center mb-4 shadow-soft">
                            <i data-lucide="{{ $c['icon'] }}" class="w-6 h-6"></i>
                        </span>
                        <h3 class="text-xl font-bold text-slate-900">{{ $c['title'] }}</h3>
                        <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $c['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="py-16 sm:py-20 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([['icon' => 'timer', 'title' => 'Simulasi CAT', 'desc' => 'Antarmuka mirip ujian asli + timer otomatis.'], ['icon' => 'book-open-check', 'title' => 'Pembahasan', 'desc' => 'Setiap soal dilengkapi pembahasan jelas.'], ['icon' => 'bar-chart-3', 'title' => 'Analisis Hasil', 'desc' => 'Skor per sub-test & status passing grade.'], ['icon' => 'history', 'title' => 'Riwayat Lengkap', 'desc' => 'Pantau progres dari setiap percobaan.']] as $f)
                    <div class="text-center">
                        <span
                            class="inline-flex w-12 h-12 rounded-2xl bg-primary-50 text-primary-700 ring-4 ring-primary-100 items-center justify-center mb-3">
                            <i data-lucide="{{ $f['icon'] }}" class="w-5 h-5"></i>
                        </span>
                        <h4 class="font-semibold text-slate-900">{{ $f['title'] }}</h4>
                        <p class="mt-1 text-sm text-slate-500">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 sm:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 px-8 py-12 sm:px-12 sm:py-16 text-center shadow-soft">
                <div class="absolute inset-0 grid-bg opacity-20"></div>
                <div class="relative">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white">Siap menaklukkan SKD?</h2>
                    <p class="mt-3 text-primary-100 max-w-xl mx-auto">Login dengan akun bimbel Anda dan mulai latihan
                        sekarang.</p>
                    <a href="{{ route('login') }}"
                        class="mt-6 inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-primary-700 font-semibold px-6 py-3 rounded-xl shadow-soft transition">
                        Login ke Akun <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-8 border-t border-slate-200 bg-white">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-slate-500">
            <p>© {{ date('Y') }} {{ config('app.name', 'TO SKD Bimbel') }}. All rights reserved.</p>
            <p class="text-xs">Built with Laravel & TailwindCSS.</p>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>
