@extends('layouts.student')
@section('title', 'Dashboard Siswa')
@section('content')
    @php $student = auth()->user()->student; @endphp
    <div class="space-y-6 sm:space-y-8">
        {{-- Hero greeting --}}
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-600 via-primary-700 to-amber-700 text-white p-5 sm:p-8 shadow-card">
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-10 w-72 h-72 bg-amber-400/20 rounded-full blur-3xl"></div>
            <div class="relative">
                <p class="text-xs sm:text-sm text-primary-100 font-medium">{{ now()->translatedFormat('l, d F Y') }}</p>
                <h2 class="text-xl sm:text-3xl font-bold mt-1 leading-tight">Halo, {{ $student?->full_name ?? auth()->user()->name }} 👋
                </h2>
                <p class="text-primary-50 text-sm mt-2">
                    @if ($student?->batch?->name)
                        Batch <span class="font-semibold text-white">{{ $student->batch->name }}</span> · Semangat berlatih
                        hari ini!
                    @else
                        Selamat datang di TO SKD Bimbel. Yuk mulai latihan!
                    @endif
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <a href="{{ route('student.tryouts.index') }}"
                        class="inline-flex items-center gap-2 bg-white text-primary-700 hover:bg-primary-50 font-semibold text-sm px-4 py-2.5 rounded-xl shadow-soft transition">
                        <i data-lucide="play" class="w-4 h-4"></i> Mulai Try Out
                    </a>
                    <a href="{{ route('student.history.index') }}"
                        class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold text-sm px-4 py-2.5 rounded-xl ring-1 ring-white/20 transition">
                        <i data-lucide="history" class="w-4 h-4"></i> Riwayat
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            @php
                $statCards = [
                    [
                        'label' => 'Try Out Tersedia',
                        'value' => $availableTryouts->count(),
                        'icon' => 'clipboard-list',
                        'color' => 'blue',
                    ],
                    [
                        'label' => 'Total Percobaan',
                        'value' => ($recentResults ?? collect())->count(),
                        'icon' => 'repeat',
                        'color' => 'rose',
                    ],
                    ['label' => 'Skor Terbaik', 'value' => $bestScore ?? '-', 'icon' => 'trophy', 'color' => 'emerald'],
                    [
                        'label' => 'Skor Terakhir',
                        'value' => $lastScore ?? '-',
                        'icon' => 'activity',
                        'color' => 'amber',
                    ],
                ];
                $colorMap = [
                    'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'ring' => 'ring-blue-100'],
                    'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'ring' => 'ring-emerald-100'],
                    'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'ring' => 'ring-rose-100'],
                    'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'ring' => 'ring-amber-100'],
                ];
            @endphp
            @foreach ($statCards as $s)
                @php $c = $colorMap[$s['color']]; @endphp
                <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5">
                    <div
                        class="w-10 h-10 rounded-xl {{ $c['bg'] }} {{ $c['text'] }} flex items-center justify-center ring-4 {{ $c['ring'] }}">
                        <i data-lucide="{{ $s['icon'] }}" class="w-5 h-5"></i>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-3 tracking-tight">{{ $s['value'] }}</p>
                    <p class="text-xs font-medium text-slate-500 mt-0.5">{{ $s['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Available Try Outs --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-slate-900 text-base">Try Out Tersedia</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih try out untuk mulai berlatih</p>
                </div>
                <a href="{{ route('student.tryouts.index') }}"
                    class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-700">
                    Lihat semua <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
            @if ($availableTryouts->count())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($availableTryouts as $to)
                        @php
                            $isSim = $to->type === 'simulation';
                            $totalQ = $to->total_questions ?? $to->twk_count + $to->tiu_count + $to->tkp_count;
                        @endphp
                        <a href="{{ route('student.tryouts.show', $to) }}"
                            class="group relative bg-white rounded-2xl border border-slate-200 p-5 hover:border-primary-300 hover:shadow-card transition-all flex flex-col">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <span
                                    class="w-10 h-10 rounded-xl flex items-center justify-center
                                    {{ $isSim ? 'bg-primary-50 text-primary-600 ring-4 ring-primary-100' : 'bg-amber-50 text-amber-600 ring-4 ring-amber-100' }}">
                                    <i data-lucide="{{ $isSim ? 'layout-grid' : 'file-text' }}" class="w-5 h-5"></i>
                                </span>
                                <span
                                    class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold
                                    {{ $isSim ? 'bg-primary-50 text-primary-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $isSim ? 'Simulasi' : 'Sub Test' }}
                                </span>
                            </div>
                            <h4 class="font-semibold text-slate-900 text-sm leading-snug line-clamp-2">{{ $to->name }}
                            </h4>
                            <div class="flex items-center gap-3 text-xs text-slate-500 mt-3">
                                <span class="inline-flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                    {{ $to->duration_minutes }} mnt</span>
                                <span class="inline-flex items-center gap-1"><i data-lucide="list-checks"
                                        class="w-3.5 h-3.5"></i> {{ $totalQ }} soal</span>
                            </div>
                            <div
                                class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-sm font-semibold text-primary-600 group-hover:text-primary-700">
                                <span>Mulai sekarang</span>
                                <i data-lucide="arrow-right"
                                    class="w-4 h-4 group-hover:translate-x-0.5 transition-transform"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center">
                    <i data-lucide="clipboard-x" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                    <p class="text-sm text-slate-500">Belum ada try out yang tersedia.</p>
                </div>
            @endif
        </div>

        {{-- Recent results --}}
        @if (($recentResults ?? collect())->count())
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-slate-900 text-base">Hasil Terakhir</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Performa try out terkini kamu</p>
                    </div>
                    <a href="{{ route('student.history.index') }}"
                        class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-700">
                        Riwayat lengkap <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50/60">
                                <th
                                    class="text-left px-5 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                    Try Out</th>
                                <th
                                    class="text-right px-5 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                    Skor</th>
                                <th
                                    class="text-left px-5 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                    Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($recentResults as $r)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-5 py-4 text-slate-800 font-medium">{{ $r->tryout->name }}</td>
                                    <td class="px-5 py-4 text-right font-bold text-slate-900">{{ $r->total_score ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-500">{{ $r->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
