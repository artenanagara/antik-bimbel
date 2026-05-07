@extends('layouts.admin')
@section('title', $student->full_name)
@section('page-title', $student->full_name)
@section('page-subtitle', 'Detail siswa & riwayat')
@section('back')
    <a href="{{ route('admin.students.index') }}"
        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
@endsection
@section('header-actions')
    <a href="{{ route('admin.students.edit', $student) }}"
        class="inline-flex items-center gap-2 text-sm font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2 rounded-xl transition">
        <i data-lucide="pencil" class="w-4 h-4"></i> Edit
    </a>
@endsection
@section('content')
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <span
                        class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center font-bold text-lg shadow-soft">
                        {{ strtoupper(substr($student->full_name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="font-semibold text-slate-900 truncate">{{ $student->full_name }}</p>
                        <p class="text-xs text-slate-500 font-mono">{{ $student->user->username }}</p>
                    </div>
                </div>
                <dl class="space-y-3 text-sm border-t border-slate-100 pt-4">
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Batch</dt>
                        <dd class="text-slate-800 font-medium text-right">{{ $student->batch->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Telepon</dt>
                        <dd class="text-slate-800 text-right">{{ $student->phone ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Alamat</dt>
                        <dd class="text-slate-800 text-right">{{ $student->address ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3 items-center">
                        <dt class="text-slate-500">Status</dt>
                        <dd>
                            @if ($student->user->is_active)
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 ring-1 ring-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <h2 class="text-base font-semibold text-slate-900 mb-4">Statistik</h2>
                <dl class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-slate-50 p-3">
                        <dt class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Try Out</dt>
                        <dd class="text-2xl font-bold text-slate-900 mt-1">{{ $results->count() }}</dd>
                    </div>
                    <div class="rounded-xl bg-emerald-50 p-3">
                        <dt class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider">Lulus</dt>
                        <dd class="text-2xl font-bold text-emerald-900 mt-1">{{ $passCount }}</dd>
                    </div>
                    <div class="rounded-xl bg-amber-50 p-3">
                        <dt class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider">Skor Terbaik</dt>
                        <dd class="text-2xl font-bold text-amber-900 mt-1">{{ $bestScore ?? '-' }}</dd>
                    </div>
                    <div class="rounded-xl bg-blue-50 p-3">
                        <dt class="text-[11px] font-semibold text-blue-700 uppercase tracking-wider">Terakhir</dt>
                        <dd class="text-2xl font-bold text-blue-900 mt-1">{{ $lastScore ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-semibold text-slate-900">Riwayat Try Out</h2>
                <p class="text-xs text-slate-500 mt-0.5">Semua percobaan yang pernah dilakukan</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/60">
                            <th class="text-left px-5 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                Try Out</th>
                            <th class="text-right px-3 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                TWK</th>
                            <th class="text-right px-3 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                TIU</th>
                            <th class="text-right px-3 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                TKP</th>
                            <th class="text-right px-3 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                Total</th>
                            <th class="text-left px-5 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($results as $r)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-3 text-slate-700 max-w-[200px] truncate font-medium">
                                    {{ $r->tryout->name }}</td>
                                <td
                                    class="px-3 py-3 text-right font-medium {{ $r->pass_twk ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $r->twk_score }}</td>
                                <td
                                    class="px-3 py-3 text-right font-medium {{ $r->pass_tiu ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $r->tiu_score }}</td>
                                <td
                                    class="px-3 py-3 text-right font-medium {{ $r->pass_tkp ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $r->tkp_score }}</td>
                                <td class="px-3 py-3 text-right font-bold text-slate-900">{{ $r->total_score }}</td>
                                <td class="px-5 py-3">
                                    @if ($r->pass_overall)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Lulus
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 ring-1 ring-red-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> TL
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <i data-lucide="inbox" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                                    <p class="text-sm text-slate-400">Belum ada riwayat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
