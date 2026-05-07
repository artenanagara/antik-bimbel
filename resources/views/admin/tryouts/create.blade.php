@extends('layouts.admin')
@section('title', 'Buat Try Out')
@section('page-title', 'Buat Try Out')

@section('content')
    <div class="max-w-3xl">
        {{-- Back link (kiri atas konten) --}}
        <a href="{{ route('admin.tryouts.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 mb-4 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
        </a>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8">
            <form action="{{ route('admin.tryouts.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Section: Informasi Dasar --}}
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 mb-1">Informasi Dasar</h3>
                    <p class="text-xs text-slate-500 mb-4">Nama dan deskripsi try out yang akan ditampilkan ke siswa.</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Try Out</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                placeholder="Contoh: Try Out SKD #1"
                                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                            <textarea name="description" rows="2"
                                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Section: Sumber Soal (radio cards) --}}
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 mb-1">Sumber Soal</h3>
                    <p class="text-xs text-slate-500 mb-4">Pilih bagaimana Anda ingin mengisi soal pada try out ini.</p>

                    <div class="grid sm:grid-cols-3 gap-3" id="source-cards">
                        @php
                            $sources = [
                                [
                                    'value' => 'manual',
                                    'icon' => 'pencil-line',
                                    'title' => 'Buat Manual',
                                    'desc' => 'Tulis soal satu per satu langsung di try out ini.',
                                ],
                                [
                                    'value' => 'bank',
                                    'icon' => 'database',
                                    'title' => 'Bank Soal',
                                    'desc' => 'Ambil soal yang sudah ada di bank soal.',
                                ],
                                [
                                    'value' => 'import',
                                    'icon' => 'upload-cloud',
                                    'title' => 'Import',
                                    'desc' => 'Upload file Excel/CSV berisi soal sekaligus.',
                                ],
                            ];
                        @endphp
                        @foreach ($sources as $i => $s)
                            <label
                                class="source-card group cursor-pointer block rounded-2xl border border-slate-200 p-4 transition-all hover:border-primary-300 hover:bg-primary-50/30">
                                <input type="radio" name="question_source" value="{{ $s['value'] }}"
                                    {{ old('question_source', 'manual') === $s['value'] ? 'checked' : '' }}
                                    class="peer sr-only" onchange="updateSourceCards()">
                                <div class="flex items-start gap-3">
                                    <span
                                        class="mt-0.5 w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all peer-checked-ring">
                                        <span
                                            class="w-2.5 h-2.5 rounded-full bg-primary-600 opacity-0 peer-checked-dot transition-opacity"></span>
                                    </span>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <i data-lucide="{{ $s['icon'] }}"
                                                class="w-4 h-4 text-slate-500 peer-checked-icon"></i>
                                            <p class="font-semibold text-slate-900 text-sm">{{ $s['title'] }}</p>
                                        </div>
                                        <p class="text-xs text-slate-500 leading-relaxed">{{ $s['desc'] }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Section: Konfigurasi Tipe & Durasi --}}
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 mb-1">Konfigurasi</h3>
                    <p class="text-xs text-slate-500 mb-4">Tipe try out, durasi, dan aturan pengulangan.</p>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tipe</label>
                            <x-form.select name="type" id="type_select" icon="layers" :options="['simulation' => 'Simulasi SKD', 'sub_test' => 'Sub Test']"
                                :selected="old('type', 'simulation')" />
                        </div>
                        <div id="sub_test_wrap" class="{{ old('type') !== 'sub_test' ? 'hidden' : '' }}">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Sub Test</label>
                            <x-form.select name="sub_test" icon="tag" placeholder="-- Pilih --" :options="['TWK' => 'TWK', 'TIU' => 'TIU', 'TKP' => 'TKP']"
                                :selected="old('sub_test')" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Durasi (menit)</label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 100) }}"
                                min="1" required
                                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Pengulangan</label>
                            <x-form.select name="repeat_allowed" icon="repeat" :options="[
                                'unlimited' => 'Tidak terbatas',
                                '1' => '1x saja',
                                '3' => '3x',
                                'none' => 'Tidak boleh ulang',
                            ]" :selected="old('repeat_allowed', 'unlimited')" />
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Section: Jumlah Soal & Passing Grade --}}
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 mb-1">Jumlah Soal & Passing Grade</h3>
                    <p class="text-xs text-slate-500 mb-4">Konfigurasi per sub-test (TWK/TIU/TKP).</p>

                    <div class="space-y-4">
                        <div class="grid sm:grid-cols-3 gap-4">
                            @foreach (['twk_count' => ['TWK', 30], 'tiu_count' => ['TIU', 35], 'tkp_count' => ['TKP', 45]] as $f => $cfg)
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah
                                        {{ $cfg[0] }}</label>
                                    <input type="number" name="{{ $f }}" value="{{ old($f, $cfg[1]) }}"
                                        min="0"
                                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500">
                                </div>
                            @endforeach
                        </div>
                        <div class="grid sm:grid-cols-3 gap-4">
                            @foreach (['pg_twk' => ['TWK', 65], 'pg_tiu' => ['TIU', 80], 'pg_tkp' => ['TKP', 166]] as $f => $cfg)
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">PG
                                        {{ $cfg[0] }}</label>
                                    <input type="number" name="{{ $f }}" value="{{ old($f, $cfg[1]) }}"
                                        min="0"
                                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Section: Jadwal --}}
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 mb-1">Jadwal <span
                            class="text-slate-400 font-normal text-xs">(opsional)</span></h3>
                    <p class="text-xs text-slate-500 mb-4">Kosongkan jika try out tidak terjadwal.</p>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Mulai</label>
                            <x-form.datetimepicker name="start_at" :value="old('start_at')" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Selesai</label>
                            <x-form.datetimepicker name="end_at" :value="old('end_at')" />
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Section: Batch & Status --}}
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 mb-1">Distribusi & Status</h3>
                    <p class="text-xs text-slate-500 mb-4">Pilih batch peserta dan status publikasi try out.</p>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Batch</label>
                            <x-form.multiselect name="batch_ids[]" icon="layers" placeholder="Pilih batch"
                                allText="Semua batch" :options="$batches->pluck('name', 'id')->toArray()" :selected="old('batch_ids', $batches->pluck('id')->toArray())" />
                            <p class="text-[11px] text-slate-400 mt-1.5">Default semua batch terpilih.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                            <x-form.select name="status" icon="flag" :options="['draft' => 'Draft', 'published' => 'Publish', 'closed' => 'Tutup']" :selected="old('status', 'draft')" />
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.tryouts.index') }}"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition">Batal</a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-semibold px-6 py-2.5 rounded-xl shadow-soft transition">
                        <i data-lucide="check" class="w-4 h-4"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .source-card:has(input:checked) {
            border-color: rgb(234 88 12);
            background-color: rgb(255 247 237);
            box-shadow: 0 0 0 4px rgb(255 237 213);
        }

        .source-card:has(input:checked) .peer-checked-ring {
            border-color: rgb(234 88 12);
        }

        .source-card:has(input:checked) .peer-checked-dot {
            opacity: 1;
        }

        .source-card:has(input:checked) .peer-checked-icon {
            color: rgb(234 88 12);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const root = document.getElementById('type_select');
            const wrap = document.getElementById('sub_test_wrap');
            if (!root || !wrap) return;
            const input = root.querySelector('[data-fs-input]');
            input?.addEventListener('change', () => {
                wrap.classList.toggle('hidden', input.value !== 'sub_test');
            });
        });
    </script>
@endpush
