@extends('layouts.admin')
@section('title', 'Import Soal')
@section('page-title', 'Import Soal')
@section('page-subtitle', $tryout->name)
@section('back')
    <a href="{{ route('admin.tryouts.show', $tryout) }}"
        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
@endsection

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1fr_300px]">
        {{-- Import Panel --}}
        <div class="space-y-5">
            {{-- Upload Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-sm font-semibold text-slate-800">Upload File Soal</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Format Excel (.xlsx / .xls). Maks. 10 MB.</p>
                </div>
                <div class="p-5">
                    <form action="{{ route('admin.tryouts.questions.import', $tryout) }}" method="POST"
                        enctype="multipart/form-data" id="import-form">
                        @csrf
                        {{-- Drop zone --}}
                        <label for="file-input"
                            class="flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 py-10 cursor-pointer hover:border-primary-400 hover:bg-primary-50/30 transition"
                            id="drop-zone">
                            <i data-lucide="upload-cloud" class="w-10 h-10 text-slate-400" id="drop-icon"></i>
                            <div class="text-center">
                                <p class="text-sm font-medium text-slate-700" id="drop-label">Klik atau seret file ke sini</p>
                                <p class="text-xs text-slate-400 mt-0.5">.xlsx atau .xls, maksimal 10 MB</p>
                            </div>
                            <input type="file" name="file" id="file-input" accept=".xlsx,.xls" required class="sr-only"
                                onchange="handleFile(this)">
                        </label>

                        <div class="mt-4 flex items-center justify-between gap-4">
                            <a href="{{ route('admin.questions.import.template') }}" target="_blank"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-primary-600 hover:text-primary-700 transition">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i> Download template
                            </a>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.tryouts.show', $tryout) }}"
                                    class="inline-flex h-9 items-center gap-1.5 px-4 rounded-lg border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                                    Lewati
                                </a>
                                <button type="submit"
                                    class="inline-flex h-9 items-center gap-1.5 px-5 rounded-lg bg-gradient-to-br from-primary-600 to-primary-700 text-sm font-semibold text-white shadow-soft transition hover:from-primary-700 hover:to-primary-800">
                                    <i data-lucide="upload" class="w-4 h-4"></i>
                                    Import & Lihat Preview
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Panduan --}}
            <div class="rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-sm font-semibold text-slate-800">Panduan Format File</h2>
                </div>
                <div class="p-5">
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    @foreach (['sub_test', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'correct_option', 'difficulty'] as $col)
                                        <th class="px-3 py-2 text-left font-semibold text-slate-600 whitespace-nowrap">{{ $col }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-slate-100">
                                    <td class="px-3 py-2 text-slate-500">TWK / TIU / TKP</td>
                                    <td class="px-3 py-2 text-slate-500">Teks soal</td>
                                    <td class="px-3 py-2 text-slate-500">Pilihan A</td>
                                    <td class="px-3 py-2 text-slate-500">Pilihan B</td>
                                    <td class="px-3 py-2 text-slate-500">Pilihan C</td>
                                    <td class="px-3 py-2 text-slate-500">Pilihan D</td>
                                    <td class="px-3 py-2 text-slate-500">Pilihan E</td>
                                    <td class="px-3 py-2 text-slate-500">A / B / C / D / E</td>
                                    <td class="px-3 py-2 text-slate-500">easy / medium / hard</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-3 text-xs text-slate-400">Kolom <code class="bg-slate-100 px-1 rounded">correct_option</code> kosong untuk soal TKP (gunakan skor 1–5 di kolom <code class="bg-slate-100 px-1 rounded">score_a</code> dst.).</p>
                </div>
            </div>
        </div>

        {{-- Preview Sidebar --}}
        <aside class="space-y-4">
            {{-- Ringkasan --}}
            <div class="rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Ringkasan Try Out</h3>
                </div>
                <div class="p-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Soal saat ini</span>
                        <span class="font-semibold text-slate-800">{{ $tryout->questions->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Target</span>
                        <span class="font-semibold text-slate-800">{{ $tryout->twk_count + $tryout->tiu_count + $tryout->tkp_count }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Durasi</span>
                        <span class="font-semibold text-slate-800">{{ $tryout->duration_minutes }} menit</span>
                    </div>
                </div>
            </div>

            {{-- Preview soal yang sudah ada --}}
            <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                <div class="border-b border-slate-100 px-4 py-3 flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Soal dalam Try Out</h3>
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                        {{ $tryout->questions->count() }}
                    </span>
                </div>
                @if ($tryout->questions->count())
                    <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto">
                        @foreach ($tryout->questions as $q)
                            <div class="px-4 py-2.5 flex items-start gap-2">
                                <span class="shrink-0 mt-0.5 inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold
                                    {{ $q->sub_test === 'TWK' ? 'bg-blue-100 text-blue-700' : ($q->sub_test === 'TIU' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700') }}">
                                    {{ $q->sub_test }}
                                </span>
                                <p class="text-xs text-slate-600 line-clamp-2">{{ strip_tags($q->question_text) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="px-4 py-8 text-center text-xs text-slate-400">Belum ada soal. Import untuk mengisi.</p>
                @endif
            </div>

            {{-- File info --}}
            <div id="file-info" class="hidden rounded-2xl border border-primary-200 bg-primary-50 px-4 py-3">
                <p class="text-xs font-semibold text-primary-700" id="file-name"></p>
                <p class="text-[11px] text-primary-500 mt-0.5" id="file-size"></p>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
<script>
    function handleFile(input) {
        const file = input.files[0];
        if (!file) return;

        const label = document.getElementById('drop-label');
        const icon = document.getElementById('drop-icon');
        const zone = document.getElementById('drop-zone');
        const info = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');

        label.textContent = file.name;
        zone.classList.add('border-primary-400', 'bg-primary-50');
        icon.setAttribute('data-lucide', 'file-spreadsheet');
        lucide.createIcons();

        const sizeKb = (file.size / 1024).toFixed(1);
        const sizeMb = (file.size / 1024 / 1024).toFixed(2);
        info.classList.remove('hidden');
        fileName.textContent = file.name;
        fileSize.textContent = file.size > 1024 * 1024 ? `${sizeMb} MB` : `${sizeKb} KB`;
    }
</script>
@endpush
