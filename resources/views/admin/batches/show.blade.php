@extends('layouts.admin')
@section('title', $batch->name)
@section('page-title', $batch->name)
@section('page-subtitle', 'Detail batch & daftar siswa')
@section('back')
    <a href="{{ route('admin.batches.index') }}"
        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
@endsection
@section('header-actions')
    <a href="{{ route('admin.batches.edit', $batch) }}"
        class="inline-flex items-center gap-2 text-sm font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2 rounded-xl transition">
        <i data-lucide="pencil" class="w-4 h-4"></i> Edit
    </a>
@endsection
@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <dl class="grid sm:grid-cols-3 gap-6 text-sm">
                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Deskripsi</dt>
                    <dd class="text-slate-800 mt-1.5">{{ $batch->description ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Siswa</dt>
                    <dd class="text-2xl font-bold text-slate-900 mt-1">{{ $batch->students_count }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</dt>
                    <dd class="mt-1.5">
                        @if ($batch->is_active)
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 ring-1 ring-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                            </span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-semibold text-slate-900">Daftar Siswa</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ $batch->students_count }} siswa terdaftar</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/60">
                            <th class="text-left px-6 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                Nama</th>
                            <th class="text-left px-6 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                Username</th>
                            <th class="text-left px-6 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                Status</th>
                            <th class="text-right px-6 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($batch->students as $s)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <span
                                            class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white text-xs flex items-center justify-center font-semibold shrink-0">
                                            {{ strtoupper(substr($s->full_name, 0, 1)) }}
                                        </span>
                                        <span class="text-slate-800 font-medium">{{ $s->full_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-mono text-xs">{{ $s->user->username }}</td>
                                <td class="px-6 py-4">
                                    @if ($s->user->is_active)
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
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.students.show', $s) }}"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-primary-600 hover:text-primary-700">
                                        Detail <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <i data-lucide="user-x" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                                    <p class="text-sm text-slate-400">Belum ada siswa.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
