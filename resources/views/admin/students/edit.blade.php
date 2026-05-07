@extends('layouts.admin')
@section('title', 'Edit Siswa')
@section('page-title', 'Edit Siswa')
@section('page-subtitle', $student->full_name)
@section('back')
    <a href="{{ route('admin.students.index') }}"
        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
@endsection
@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8">
            <h2 class="text-base font-semibold text-slate-900 mb-5">Data Siswa</h2>
            <form action="{{ route('admin.students.update', $student) }}" method="POST" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $student->full_name) }}" required
                            class="w-full text-sm bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition">
                        @error('full_name')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username</label>
                        <input type="text" name="username" value="{{ old('username', $student->user->username) }}"
                            required
                            class="w-full text-sm bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition">
                        @error('username')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Batch</label>
                        <x-form.select name="batch_id" placeholder="-- Tanpa Batch --" icon="layers" :options="$batches->pluck('name', 'id')->toArray()"
                            :selected="old('batch_id', $student->batch_id)" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">No. Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $student->phone) }}"
                            class="w-full text-sm bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat</label>
                        <textarea name="address" rows="2"
                            class="w-full text-sm bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition">{{ old('address', $student->address) }}</textarea>
                    </div>
                </div>
                <label
                    class="flex items-center gap-2.5 px-3.5 py-3 rounded-xl bg-slate-50 cursor-pointer hover:bg-slate-100 transition">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                        {{ old('is_active', $student->user->is_active) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    <span class="text-sm font-medium text-slate-700">Akun aktif</span>
                </label>
                <div class="flex gap-2 pt-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-soft transition">
                        <i data-lucide="save" class="w-4 h-4"></i> Update
                    </button>
                    <a href="{{ route('admin.students.index') }}"
                        class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-slate-900 px-4 py-2.5 rounded-xl hover:bg-slate-100">Batal</a>
                </div>
            </form>
        </div>

        {{-- Reset password --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8">
            <div class="flex items-start gap-3 mb-4">
                <span
                    class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center ring-4 ring-amber-100">
                    <i data-lucide="key-round" class="w-4 h-4"></i>
                </span>
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Reset Password</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Ganti password akun siswa</p>
                </div>
            </div>
            <form action="{{ route('admin.students.reset-password', $student) }}" method="POST"
                class="flex flex-col sm:flex-row gap-2">
                @csrf
                <input type="password" name="password" placeholder="Password baru" required
                    class="flex-1 text-sm bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-4 focus:ring-amber-100 focus:border-amber-500 transition">
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-soft transition">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i> Reset
                </button>
            </form>
        </div>
    </div>
@endsection
