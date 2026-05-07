@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas dan performa siswa')
@section('content')
    <div class="space-y-6">
        {{-- Welcome banner --}}
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-600 via-primary-700 to-amber-700 text-white p-6 sm:p-8 shadow-card">
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-10 w-72 h-72 bg-amber-400/20 rounded-full blur-3xl"></div>
            <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-primary-100 font-medium">{{ now()->translatedFormat('l, d F Y') }}</p>
                    <h2 class="text-2xl sm:text-3xl font-bold mt-1">Halo, {{ auth()->user()->name }} 👋</h2>
                    <p class="text-primary-50 text-sm mt-1.5 max-w-lg">Pantau perkembangan siswa, kelola bank soal, dan
                        analisis hasil try out semua dalam satu tempat.</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('admin.tryouts.create') }}"
                        class="inline-flex items-center gap-2 bg-white text-primary-700 hover:bg-primary-50 font-semibold text-sm px-4 py-2.5 rounded-xl shadow-soft transition">
                        <i data-lucide="plus" class="w-4 h-4"></i> Buat Try Out
                    </a>
                    <a href="{{ route('admin.questions.index') }}"
                        class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold text-sm px-4 py-2.5 rounded-xl ring-1 ring-white/20 transition">
                        <i data-lucide="book-open" class="w-4 h-4"></i> Bank Soal
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $cards = [
                    [
                        'label' => 'Total Siswa',
                        'value' => $stats['total_students'],
                        'icon' => 'users',
                        'color' => 'blue',
                        'route' => 'admin.students.index',
                    ],
                    [
                        'label' => 'Batch Aktif',
                        'value' => $stats['active_batches'],
                        'icon' => 'layers',
                        'color' => 'emerald',
                        'route' => 'admin.batches.index',
                    ],
                    [
                        'label' => 'Try Out',
                        'value' => $stats['total_tryouts'],
                        'icon' => 'clipboard-list',
                        'color' => 'rose',
                        'route' => 'admin.tryouts.index',
                    ],
                    [
                        'label' => 'Bank Soal',
                        'value' => $stats['total_questions'],
                        'icon' => 'book-open',
                        'color' => 'amber',
                        'route' => 'admin.questions.index',
                    ],
                ];
                $colorMap = [
                    'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'ring' => 'ring-blue-100'],
                    'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'ring' => 'ring-emerald-100'],
                    'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'ring' => 'ring-rose-100'],
                    'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'ring' => 'ring-amber-100'],
                ];
            @endphp
            @foreach ($cards as $card)
                @php $c = $colorMap[$card['color']]; @endphp
                <a href="{{ route($card['route']) }}"
                    class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-primary-200 hover:shadow-card transition-all">
                    <div class="flex items-start justify-between">
                        <div
                            class="w-11 h-11 rounded-xl {{ $c['bg'] }} {{ $c['text'] }} flex items-center justify-center ring-4 {{ $c['ring'] }}">
                            <i data-lucide="{{ $card['icon'] }}" class="w-5 h-5"></i>
                        </div>
                        <i data-lucide="arrow-up-right"
                            class="w-4 h-4 text-slate-300 group-hover:text-primary-500 transition-colors"></i>
                    </div>
                    <p class="text-3xl font-bold text-slate-900 mt-4 tracking-tight">{{ number_format($card['value']) }}</p>
                    <p class="text-xs font-medium text-slate-500 mt-1">{{ $card['label'] }}</p>
                </a>
            @endforeach
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Pass rate --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Statistik Performa</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Ringkasan hasil seluruh try out</p>
                    </div>
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-semibold">
                        <i data-lucide="trending-up" class="w-3 h-3"></i> Live
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div
                        class="relative overflow-hidden rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100/50 border border-emerald-100 p-5">
                        <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Tingkat Kelulusan</p>
                        <p class="text-4xl font-bold text-emerald-900 mt-2 tracking-tight">{{ $stats['pass_rate'] }}<span
                                class="text-2xl">%</span></p>
                        <div class="mt-3 w-full bg-emerald-200/50 rounded-full h-2 overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full transition-all"
                                style="width: {{ min(100, $stats['pass_rate']) }}%"></div>
                        </div>
                        <i data-lucide="trophy" class="w-16 h-16 text-emerald-200 absolute -bottom-2 -right-2"></i>
                    </div>
                    <div
                        class="relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-50 to-blue-100/50 border border-blue-100 p-5">
                        <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Rata-rata Skor</p>
                        <p class="text-4xl font-bold text-blue-900 mt-2 tracking-tight">{{ $stats['avg_score'] }}</p>
                        <p class="text-xs text-blue-700 mt-2">dari semua percobaan</p>
                        <i data-lucide="target" class="w-16 h-16 text-blue-200 absolute -bottom-2 -right-2"></i>
                    </div>
                </div>
            </div>

            {{-- Top students --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-slate-900">Top 5 Siswa</h2>
                    <i data-lucide="award" class="w-4 h-4 text-amber-500"></i>
                </div>
                @if (count($topStudents) > 0)
                    <div class="space-y-3">
                        @foreach ($topStudents as $i => $s)
                            @php
                                $medals = [
                                    'bg-amber-100 text-amber-700',
                                    'bg-slate-100 text-slate-600',
                                    'bg-orange-100 text-orange-700',
                                ];
                                $cls = $medals[$i] ?? 'bg-slate-50 text-slate-500';
                            @endphp
                            <div class="flex items-center gap-3">
                                <span
                                    class="w-7 h-7 rounded-full {{ $cls }} text-xs flex items-center justify-center font-bold shrink-0">{{ $i + 1 }}</span>
                                <span class="flex-1 text-sm text-slate-700 truncate">{{ $s->student->full_name }}</span>
                                <span class="text-sm font-bold text-slate-900">{{ $s->best_score ?? '-' }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-lucide="users" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-sm text-slate-400">Belum ada data.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent activity --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Aktivitas Terkini</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Hasil try out terbaru dari siswa</p>
                </div>
                <a href="{{ route('admin.results.index') }}"
                    class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-700">
                    Lihat semua <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/60">
                            <th class="text-left px-6 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                Siswa</th>
                            <th class="text-left px-6 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                Try Out</th>
                            <th class="text-right px-6 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                Skor</th>
                            <th class="text-left px-6 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                Status</th>
                            <th class="text-left px-6 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentActivity as $r)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <span
                                            class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white text-xs flex items-center justify-center font-semibold shrink-0">
                                            {{ strtoupper(substr($r->student->full_name, 0, 1)) }}
                                        </span>
                                        <span
                                            class="text-slate-800 font-medium truncate">{{ $r->student->full_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 truncate max-w-[200px]">{{ $r->tryout->name }}</td>
                                <td class="px-6 py-4 text-right font-bold text-slate-900">{{ $r->total_score }}</td>
                                <td class="px-6 py-4">
                                    @if ($r->pass_overall)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Lulus
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 ring-1 ring-red-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Tidak Lulus
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500 text-xs">{{ $r->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <i data-lucide="inbox" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                                    <p class="text-sm text-slate-400">Belum ada aktivitas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
