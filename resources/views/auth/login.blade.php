<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TO SKD Bimbel</title>
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
                            50: '#FFF7ED',
                            100: '#FFEDD5',
                            500: '#F97316',
                            600: '#EA580C',
                            700: '#C2410C',
                            800: '#9A3412',
                            900: '#7C2D12'
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .gradient-bg {
            background: radial-gradient(ellipse at top left, #F97316 0%, #C2410C 35%, #7C2D12 100%);
        }

        .glow {
            position: absolute;
            border-radius: 9999px;
            filter: blur(80px);
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
</head>

<body class="h-full bg-slate-50 text-slate-800">
    <div class="min-h-full lg:grid lg:grid-cols-2">
        {{-- Left: Brand panel (desktop only) --}}
        <div class="hidden lg:flex relative overflow-hidden gradient-bg text-white p-12 flex-col justify-between">
            <span class="glow w-96 h-96 bg-amber-400" style="top:-100px; left:-100px;"></span>
            <span class="glow w-80 h-80 bg-amber-500" style="bottom:-80px; right:-80px;"></span>

            <div class="relative">
                <div class="flex items-center gap-3">
                    <span
                        class="w-11 h-11 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center ring-1 ring-white/25">
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                    </span>
                    <div>
                        <p class="font-bold text-lg leading-tight">TO SKD Bimbel</p>
                        <p class="text-xs text-primary-100">Platform Try Out CPNS</p>
                    </div>
                </div>
            </div>

            <div class="relative space-y-6">
                <h2 class="text-4xl font-bold leading-tight">
                    Persiapkan dirimu, <br>
                    <span class="text-primary-100">raih impian</span> menjadi ASN.
                </h2>
                <p class="text-primary-50 text-base max-w-md">
                    Latihan soal Try Out SKD lengkap dengan pembahasan, simulasi waktu, dan analisis hasil per sub-test.
                </p>
                <div class="grid grid-cols-3 gap-3 pt-4 max-w-md">
                    @foreach ([['TWK', 'Wawasan Kebangsaan'], ['TIU', 'Intelegensi Umum'], ['TKP', 'Karakteristik Pribadi']] as $f)
                        <div class="bg-white/10 backdrop-blur rounded-xl p-3 ring-1 ring-white/15">
                            <p class="font-bold text-lg">{{ $f[0] }}</p>
                            <p class="text-[11px] text-primary-100 leading-tight mt-0.5">{{ $f[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="relative text-xs text-primary-100">
                © {{ date('Y') }} TO SKD Bimbel. All rights reserved.
            </div>
        </div>

        {{-- Right: Form --}}
        <div class="flex items-center justify-center p-6 sm:p-12 min-h-screen lg:min-h-0">
            <div class="w-full max-w-sm">
                {{-- Mobile brand --}}
                <div class="lg:hidden text-center mb-8">
                    <div
                        class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 shadow-lg shadow-primary-600/25 mb-4">
                        <i data-lucide="graduation-cap" class="w-7 h-7 text-white"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900">TO SKD Bimbel</h1>
                    <p class="text-sm text-slate-500 mt-1">Platform Try Out CPNS</p>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Selamat datang 👋</h2>
                    <p class="text-sm text-slate-500 mt-1.5">Masuk dengan akun yang diberikan admin bimbel.</p>
                </div>

                <form action="{{ route('login.post') }}" method="POST" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="username">Username</label>
                        <div class="relative">
                            <i data-lucide="user"
                                class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input type="text" id="username" name="username" value="{{ old('username') }}"
                                autocomplete="username" required placeholder="Masukkan username"
                                class="w-full pl-10 pr-3 py-2.5 text-sm bg-white border rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition
                                  {{ $errors->has('username') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}">
                        </div>
                        @error('username')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="password">Password</label>
                        <div class="relative">
                            <i data-lucide="lock"
                                class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input type="password" id="password" name="password" autocomplete="current-password"
                                required placeholder="••••••••"
                                class="w-full pl-10 pr-10 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition">
                            <button type="button" onclick="togglePw()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i id="pw-icon" data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="remember" name="remember"
                                class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm text-slate-600">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-br from-primary-500 to-primary-700 hover:from-primary-600 hover:to-primary-800 text-white font-semibold py-2.5 px-4 rounded-xl shadow-lg shadow-primary-600/20 transition text-sm">
                        Masuk
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>

                <p class="text-center text-xs text-slate-500 mt-8">Akun dibuat oleh admin bimbel.</p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function togglePw() {
            const pw = document.getElementById('password');
            const icon = document.getElementById('pw-icon');
            const showing = pw.type === 'text';
            pw.type = showing ? 'password' : 'text';
            icon.setAttribute('data-lucide', showing ? 'eye' : 'eye-off');
            lucide.createIcons();
        }
    </script>
</body>

</html>
