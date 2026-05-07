@extends('layouts.admin')
@section('title', 'Bank Soal')
@section('page-title', 'Bank Soal')
@section('page-subtitle', 'Kelola soal TWK, TIU, dan TKP')
@section('header-actions')
    <a href="{{ route('admin.questions.import.template') }}"
        class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50">
        <i data-lucide="download" class="h-4 w-4"></i> Template
    </a>
    <a href="{{ route('admin.questions.create') }}"
        class="inline-flex h-9 items-center gap-2 rounded-xl bg-gradient-to-br from-primary-600 to-primary-700 px-4 text-sm font-semibold text-white shadow-soft transition hover:from-primary-700 hover:to-primary-800">
        <i data-lucide="plus" class="h-4 w-4"></i> Tambah Soal
    </a>
@endsection

@section('content')
<div class="space-y-5">

    {{-- Stats --}}
    <div class="grid gap-3 md:grid-cols-4">
        @php
            $summaryCards = [
                ['label'=>'Total Soal','value'=>$stats['total'],'icon'=>'library','class'=>'bg-primary-50 text-primary-700'],
                ['label'=>'Aktif','value'=>$stats['active'],'icon'=>'check-circle-2','class'=>'bg-emerald-50 text-emerald-700'],
                ['label'=>'Draft','value'=>$stats['draft'],'icon'=>'file-clock','class'=>'bg-slate-100 text-slate-700'],
                ['label'=>'Soal TKP','value'=>$stats['tkp'],'icon'=>'badge-check','class'=>'bg-amber-50 text-amber-700'],
            ];
        @endphp
        @foreach ($summaryCards as $card)
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ $card['label'] }}</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($card['value']) }}</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg {{ $card['class'] }}">
                        <i data-lucide="{{ $card['icon'] }}" class="h-5 w-5"></i>
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Import banner --}}
    <div class="rounded-lg border border-amber-200 bg-amber-50/70 p-4">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                    <i data-lucide="file-spreadsheet" class="h-5 w-5"></i>
                </span>
                <div>
                    <p class="text-sm font-semibold text-amber-950">Import massal dari Excel</p>
                    <p class="mt-0.5 text-sm text-amber-800">Gunakan template agar kolom jawaban, skor TKP, dan pembahasan terbaca rapi.</p>
                </div>
            </div>
            <form action="{{ route('admin.questions.import') }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col gap-2 sm:flex-row sm:items-center">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls" required
                    class="w-full text-sm text-slate-700 file:mr-3 file:rounded-lg file:border file:border-slate-300 file:bg-white file:px-3 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-50 sm:w-72">
                <button type="submit"
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 text-sm font-semibold text-white transition-colors hover:bg-amber-700">
                    <i data-lucide="upload" class="h-4 w-4"></i> Import
                </button>
            </form>
        </div>
    </div>

    @if (session('import_errors') && count(session('import_errors')) > 0)
        <div class="rounded-lg border border-red-200 bg-red-50 p-4">
            <div class="flex items-start gap-3">
                <i data-lucide="alert-triangle" class="mt-0.5 h-4 w-4 shrink-0 text-red-600"></i>
                <div>
                    <p class="text-sm font-semibold text-red-800">Beberapa baris tidak berhasil diimport</p>
                    <ul class="mt-2 space-y-1 text-sm text-red-700">
                        @foreach (session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">

        {{-- Filter --}}
        <div class="border-b border-slate-100 bg-slate-50/70 p-4">
            <form method="GET" id="filter-form" class="grid gap-3 lg:grid-cols-[minmax(200px,1fr)_repeat(4,160px)_auto]">
                <div class="relative">
                    <i data-lucide="search" class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari kode atau teks soal"
                        class="h-10 w-full rounded-lg border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-800 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-100"
                        oninput="debounceSubmit(this.form)">
                </div>
                <x-form.select name="sub_test" placeholder="Sub Test" icon="tag"
                    :options="['TWK'=>'TWK','TIU'=>'TIU','TKP'=>'TKP']" :selected="request('sub_test')" />
                @php
                    $catOptions = [];
                    foreach ($categories as $subTest => $items) {
                        foreach ($items as $cat) { $catOptions[$cat->id] = $subTest . ' — ' . $cat->name; }
                    }
                @endphp
                <x-form.select name="category_id" placeholder="Kategori" icon="folder"
                    :options="$catOptions" :selected="request('category_id')" />
                <x-form.select name="difficulty" placeholder="Kesulitan" icon="gauge"
                    :options="['easy'=>'Mudah','medium'=>'Sedang','hard'=>'Sulit']" :selected="request('difficulty')" />
                <x-form.select name="status" placeholder="Status" icon="flag"
                    :options="['active'=>'Aktif','draft'=>'Draft']" :selected="request('status')" />
                @if (request()->hasAny(['search','sub_test','category_id','difficulty','status']))
                    <a href="{{ route('admin.questions.index') }}"
                        class="inline-flex h-10 items-center justify-center gap-1 rounded-lg px-3 text-sm font-medium text-slate-500 hover:bg-white hover:text-slate-700 transition">
                        <i data-lucide="x" class="h-3.5 w-3.5"></i> Reset
                    </a>
                @else
                    <div></div>
                @endif
            </form>
        </div>

        {{-- Bulk action bar --}}
        <div id="bulk-bar" class="hidden px-5 py-3 border-b border-primary-100 bg-primary-50 flex items-center gap-3">
            <span class="text-sm font-semibold text-primary-700"><span id="bulk-count">0</span> dipilih</span>
            <form action="{{ route('admin.questions.bulk-action') }}" method="POST" id="bulk-form" class="ml-auto flex items-center gap-2">
                @csrf
                <input type="hidden" name="action" id="bulk-action-input">
                <div id="bulk-ids"></div>
                <button type="button" onclick="submitBulk('activate')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 text-xs font-semibold hover:bg-emerald-200 transition">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Aktifkan
                </button>
                <button type="button" onclick="submitBulk('draft')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-300 transition">
                    <i data-lucide="file-clock" class="w-3.5 h-3.5"></i> Draft
                </button>
                <button type="button" onclick="submitBulk('delete')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-100 text-red-700 text-xs font-semibold hover:bg-red-200 transition">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px] text-sm">
                <thead class="bg-slate-50/60 border-b border-slate-100">
                    <tr>
                        <th class="w-10 px-4 py-3">
                            <input type="checkbox" id="select-all" onchange="toggleAll(this)"
                                class="rounded border-slate-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Soal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Level</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($questions as $question)
                        <tr class="hover:bg-slate-50 transition row-item">
                            <td class="w-10 px-4 py-4 align-top">
                                <input type="checkbox" value="{{ $question->id }}" onchange="onRowCheck()"
                                    class="row-checkbox mt-0.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                            </td>
                            <td class="px-4 py-4 align-top">
                                <div class="flex flex-col gap-1.5">
                                    <span class="font-mono text-xs font-semibold text-slate-700">{{ $question->code }}</span>
                                    <span class="inline-flex w-fit items-center rounded-md px-2 py-0.5 text-xs font-bold
                                        {{ $question->sub_test==='TWK' ? 'bg-blue-50 text-blue-700' : ($question->sub_test==='TIU' ? 'bg-violet-50 text-violet-700' : 'bg-emerald-50 text-emerald-700') }}">
                                        {{ $question->sub_test }}
                                    </span>
                                </div>
                            </td>
                            <td class="max-w-[360px] px-4 py-4 align-top">
                                <p class="line-clamp-2 font-medium leading-6 text-slate-800">{{ strip_tags($question->question_text) }}</p>
                            </td>
                            <td class="px-4 py-4 align-top text-slate-600 text-xs">{{ $question->category->name ?? '-' }}</td>
                            <td class="px-4 py-4 align-top text-slate-600 capitalize text-xs">{{ $question->difficulty }}</td>
                            <td class="px-4 py-4 align-top">
                                @if ($question->status === 'active')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right align-top">
                                <div class="inline-flex items-center rounded-lg border border-slate-200 bg-white p-0.5">
                                    <a href="{{ route('admin.questions.show', $question) }}"
                                        class="rounded-md p-1.5 text-slate-400 transition hover:bg-blue-50 hover:text-blue-700" title="Detail">
                                        <i data-lucide="eye" class="h-4 w-4"></i>
                                    </a>
                                    <a href="{{ route('admin.questions.edit', $question) }}"
                                        class="rounded-md p-1.5 text-slate-400 transition hover:bg-amber-50 hover:text-amber-700" title="Edit">
                                        <i data-lucide="pencil" class="h-4 w-4"></i>
                                    </a>
                                    <form action="{{ route('admin.questions.destroy', $question) }}" method="POST"
                                        onsubmit="return confirm('Hapus soal ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-md p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-700" title="Hapus">
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center">
                                <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-slate-400 mx-auto">
                                    <i data-lucide="book-open" class="h-6 w-6"></i>
                                </span>
                                <p class="mt-3 text-sm font-semibold text-slate-700">Belum ada soal</p>
                                <p class="mt-1 text-sm text-slate-500">Mulai dari tambah soal manual atau import dari template Excel.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($questions->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">{{ $questions->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('#filter-form [data-fs-input]').forEach(input => {
        input.addEventListener('change', () => document.getElementById('filter-form').submit());
    });

    let _dt;
    function debounceSubmit(form) {
        clearTimeout(_dt);
        _dt = setTimeout(() => form.submit(), 450);
    }

    function toggleAll(cb) {
        document.querySelectorAll('.row-checkbox').forEach(c => c.checked = cb.checked);
        onRowCheck();
    }

    function onRowCheck() {
        const checked = [...document.querySelectorAll('.row-checkbox:checked')];
        const all = document.querySelectorAll('.row-checkbox');
        document.getElementById('select-all').indeterminate = checked.length > 0 && checked.length < all.length;
        document.getElementById('select-all').checked = checked.length === all.length && all.length > 0;
        document.getElementById('bulk-count').textContent = checked.length;
        document.getElementById('bulk-bar').classList.toggle('hidden', checked.length === 0);
    }

    function submitBulk(action) {
        const checked = [...document.querySelectorAll('.row-checkbox:checked')];
        if (!checked.length) return;
        if (action === 'delete' && !confirm('Hapus ' + checked.length + ' soal terpilih?')) return;
        document.getElementById('bulk-action-input').value = action;
        const container = document.getElementById('bulk-ids');
        container.innerHTML = '';
        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
            container.appendChild(inp);
        });
        document.getElementById('bulk-form').submit();
    }
</script>
@endpush
