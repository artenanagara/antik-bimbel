@extends('layouts.admin')
@section('title', 'Batch')
@section('page-title', 'Batch Siswa')
@section('page-subtitle', 'Kelola batch & kelompok siswa')
@section('header-actions')
    <a href="{{ route('admin.batches.create') }}"
        class="inline-flex items-center gap-2 bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-soft transition">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Batch
    </a>
@endsection
@section('content')
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($batches as $batch)
            <div
                class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-primary-200 hover:shadow-card transition-all">
                <div class="flex items-start gap-3">
                    <span
                        class="w-11 h-11 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center ring-4 ring-primary-100 shrink-0">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-slate-900 truncate">{{ $batch->name }}</h3>
                        @if ($batch->description)
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $batch->description }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-4">
                    <span
                        class="inline-flex items-center gap-1 text-xs text-slate-600 bg-slate-50 px-2.5 py-1 rounded-full">
                        <i data-lucide="users" class="w-3 h-3"></i>
                        <span class="font-semibold text-slate-800">{{ $batch->students_count }}</span> siswa
                    </span>
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
                </div>
                <div class="flex items-center gap-1 mt-4 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.batches.show', $batch) }}"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-primary-600 hover:text-primary-700 px-2 py-1 rounded-md hover:bg-primary-50">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                    </a>
                    <a href="{{ route('admin.batches.edit', $batch) }}"
                        class="inline-flex items-center gap-1 text-xs font-medium text-slate-600 hover:text-slate-900 px-2 py-1 rounded-md hover:bg-slate-50">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                    </a>
                    <form action="{{ route('admin.batches.destroy', $batch) }}" method="POST"
                        onsubmit="return confirm('Hapus batch ini?')" class="inline ml-auto">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-1 text-xs font-medium text-red-500 hover:text-red-700 px-2 py-1 rounded-md hover:bg-red-50">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div
                class="sm:col-span-2 lg:col-span-3 bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center">
                <i data-lucide="layers" class="w-12 h-12 text-slate-300 mx-auto mb-2"></i>
                <p class="text-sm text-slate-500">Belum ada batch.</p>
                <a href="{{ route('admin.batches.create') }}"
                    class="inline-flex items-center gap-1 mt-3 text-sm font-medium text-primary-600 hover:text-primary-700">
                    Tambah batch pertama <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        @endforelse
    </div>
@endsection
