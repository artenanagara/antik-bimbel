@extends('layouts.admin')
@section('title', 'Tambah Batch')
@section('page-title', 'Tambah Batch')
@section('page-subtitle', 'Buat kelompok baru untuk siswa')
@section('back')
    <a href="{{ route('admin.batches.index') }}"
        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
@endsection
@section('content')
    <div class="max-w-xl">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8">
            <form action="{{ route('admin.batches.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Batch</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Contoh: CPNS 2026 Gel. 1"
                        class="w-full text-sm bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition">
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i data-lucide="alert-circle"
                                class="w-3 h-3"></i> {{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Catatan tambahan..."
                        class="w-full text-sm bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition">{{ old('description') }}</textarea>
                </div>
                <label
                    class="flex items-center gap-2.5 px-3.5 py-3 rounded-xl bg-slate-50 cursor-pointer hover:bg-slate-100 transition">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked
                        class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    <span class="text-sm font-medium text-slate-700">Batch aktif</span>
                </label>
                <div class="flex gap-2 pt-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-soft transition">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan
                    </button>
                    <a href="{{ route('admin.batches.index') }}"
                        class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-slate-900 px-4 py-2.5 rounded-xl hover:bg-slate-100">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
