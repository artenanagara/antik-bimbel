@extends('layouts.admin')
@section('title', 'Pilih dari Bank Soal')
@section('page-title', 'Pilih Soal dari Bank')
@section('page-subtitle', $tryout->name)
@section('back')
    <a href="{{ route('admin.tryouts.show', $tryout) }}"
        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
@endsection

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1fr_300px]">
        {{-- Bank Soal --}}
        <form action="{{ route('admin.tryouts.questions.bank', $tryout) }}" method="POST" id="bank-form">
            @csrf
            <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                <div class="border-b border-slate-100 px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-800">Bank Soal</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Centang soal yang ingin dimasukkan ke try out ini.</p>
                    </div>
                    {{-- Filter sub test --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" data-filter="all" onclick="filterSub('all')"
                            class="filter-btn active px-3 py-1 rounded-full text-xs font-semibold transition">Semua</button>
                        @foreach (['TWK', 'TIU', 'TKP'] as $st)
                            <button type="button" data-filter="{{ $st }}" onclick="filterSub('{{ $st }}')"
                                class="filter-btn px-3 py-1 rounded-full text-xs font-semibold transition">{{ $st }}</button>
                        @endforeach
                    </div>
                </div>

                {{-- Search --}}
                <div class="px-5 py-3 border-b border-slate-100">
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" id="search-input" placeholder="Cari soal..."
                            class="w-full pl-9 pr-3 h-9 rounded-lg border border-slate-200 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100 transition"
                            oninput="filterRows()">
                    </div>
                </div>

                @if ($bankQuestions->flatten()->count())
                    <div class="max-h-[560px] overflow-y-auto">
                        <table class="w-full text-sm" id="bank-table">
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($bankQuestions as $sub => $group)
                                    <tr class="sub-header" data-sub="{{ $sub }}">
                                        <td colspan="4" class="px-5 py-2 bg-slate-50 text-xs font-bold text-slate-500 tracking-wide uppercase">
                                            {{ $sub }} <span class="text-slate-400 font-normal normal-case">({{ $group->count() }} soal)</span>
                                        </td>
                                    </tr>
                                    @foreach ($group as $q)
                                        <tr class="bank-row hover:bg-slate-50/60 transition" data-sub="{{ $sub }}" data-text="{{ strtolower(strip_tags($q->question_text)) }}">
                                            <td class="w-10 px-5 py-3">
                                                <input type="checkbox" name="question_ids[]" value="{{ $q->id }}"
                                                    class="bank-checkbox rounded border-slate-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
                                                    onchange="updatePreview(this, {{ $q->id }}, '{{ $q->sub_test }}', {{ json_encode(substr(strip_tags($q->question_text), 0, 80)) }})">
                                            </td>
                                            <td class="px-2 py-3">
                                                <span class="inline-flex px-1.5 py-0.5 rounded text-[11px] font-bold
                                                    {{ $q->sub_test === 'TWK' ? 'bg-blue-100 text-blue-700' : ($q->sub_test === 'TIU' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700') }}">
                                                    {{ $q->sub_test }}
                                                </span>
                                            </td>
                                            <td class="px-2 py-3 text-slate-700 text-xs max-w-0 w-full">
                                                <div class="line-clamp-2">{{ strip_tags($q->question_text) }}</div>
                                                @if ($q->category)
                                                    <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $q->category->name }}</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3 text-right text-[11px] text-slate-400 whitespace-nowrap">
                                                {{ ucfirst($q->difficulty ?? '-') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4">
                        <p class="text-sm text-slate-500">
                            <span id="selected-count" class="font-semibold text-slate-800">0</span> soal dipilih
                        </p>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.tryouts.show', $tryout) }}"
                                class="inline-flex h-9 items-center gap-1.5 px-4 rounded-lg border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                                Lewati
                            </a>
                            <button type="submit" id="submit-btn" disabled
                                class="inline-flex h-9 items-center gap-1.5 px-5 rounded-lg bg-gradient-to-br from-primary-600 to-primary-700 text-sm font-semibold text-white shadow-soft transition hover:from-primary-700 hover:to-primary-800 disabled:opacity-40 disabled:cursor-not-allowed">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                Tambahkan & Lihat Preview
                            </button>
                        </div>
                    </div>
                @else
                    <div class="py-16 text-center">
                        <i data-lucide="database" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                        <p class="text-sm text-slate-500 font-medium">Semua soal sudah ada di try out ini.</p>
                        <a href="{{ route('admin.tryouts.show', $tryout) }}"
                            class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700 transition">
                            Lihat Try Out <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                @endif
            </div>
        </form>

        {{-- Preview Sidebar --}}
        <aside class="space-y-4">
            {{-- Ringkasan Try Out --}}
            <div class="rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Ringkasan Try Out</h3>
                </div>
                <div class="p-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Total soal saat ini</span>
                        <span class="font-semibold text-slate-800">{{ $tryout->questions->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Target soal</span>
                        <span class="font-semibold text-slate-800">{{ $tryout->twk_count + $tryout->tiu_count + $tryout->tkp_count }}</span>
                    </div>
                </div>
            </div>

            {{-- Preview Soal Dipilih --}}
            <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                <div class="border-b border-slate-100 px-4 py-3 flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Soal Dipilih</h3>
                    <span id="preview-badge" class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">0</span>
                </div>
                <div id="preview-list" class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                    <p id="preview-empty" class="px-4 py-8 text-center text-xs text-slate-400">Belum ada soal dipilih.</p>
                </div>
            </div>

            {{-- Sudah di Try Out --}}
            @if ($tryout->questions->count())
                <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                    <div class="border-b border-slate-100 px-4 py-3">
                        <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Sudah di Try Out</h3>
                    </div>
                    <div class="divide-y divide-slate-100 max-h-48 overflow-y-auto">
                        @foreach ($tryout->questions as $q)
                            <div class="px-4 py-2.5 flex items-center gap-2">
                                <span class="shrink-0 inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold
                                    {{ $q->sub_test === 'TWK' ? 'bg-blue-100 text-blue-700' : ($q->sub_test === 'TIU' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700') }}">
                                    {{ $q->sub_test }}
                                </span>
                                <p class="text-xs text-slate-600 line-clamp-1">{{ strip_tags($q->question_text) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </aside>
    </div>
@endsection

@push('styles')
<style>
    .filter-btn { background: rgb(241 245 249); color: rgb(100 116 139); }
    .filter-btn.active { background: rgb(255 247 237); color: rgb(234 88 12); box-shadow: 0 0 0 2px rgb(254 215 170); }
    .bank-row.hidden { display: none; }
    .sub-header.hidden { display: none; }
</style>
@endpush

@push('scripts')
<script>
    let selectedIds = new Set();

    function updatePreview(cb, id, sub, text) {
        if (cb.checked) {
            selectedIds.add(id);
            addPreviewItem(id, sub, text);
        } else {
            selectedIds.delete(id);
            removePreviewItem(id);
        }
        updateCount();
    }

    function addPreviewItem(id, sub, text) {
        const empty = document.getElementById('preview-empty');
        if (empty) empty.remove();

        const colors = { TWK: 'bg-blue-100 text-blue-700', TIU: 'bg-purple-100 text-purple-700', TKP: 'bg-emerald-100 text-emerald-700' };
        const div = document.createElement('div');
        div.id = 'prev-' + id;
        div.className = 'px-4 py-2.5 flex items-center gap-2';
        div.innerHTML = `<span class="shrink-0 inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold ${colors[sub] || 'bg-slate-100 text-slate-600'}">${sub}</span><p class="text-xs text-slate-600 line-clamp-1">${text}</p>`;
        document.getElementById('preview-list').appendChild(div);
    }

    function removePreviewItem(id) {
        const el = document.getElementById('prev-' + id);
        if (el) el.remove();
        if (document.getElementById('preview-list').children.length === 0) {
            const p = document.createElement('p');
            p.id = 'preview-empty';
            p.className = 'px-4 py-8 text-center text-xs text-slate-400';
            p.textContent = 'Belum ada soal dipilih.';
            document.getElementById('preview-list').appendChild(p);
        }
    }

    function updateCount() {
        const n = selectedIds.size;
        document.getElementById('selected-count').textContent = n;
        document.getElementById('preview-badge').textContent = n;
        document.getElementById('submit-btn').disabled = n === 0;
    }

    function filterSub(sub) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.toggle('active', b.dataset.filter === sub));
        document.querySelectorAll('.bank-row').forEach(row => {
            row.classList.toggle('hidden', sub !== 'all' && row.dataset.sub !== sub);
        });
        document.querySelectorAll('.sub-header').forEach(h => {
            if (sub === 'all') { h.classList.remove('hidden'); return; }
            h.classList.toggle('hidden', h.dataset.sub !== sub);
        });
        filterRows();
    }

    function filterRows() {
        const q = document.getElementById('search-input').value.toLowerCase();
        const activeSub = document.querySelector('.filter-btn.active')?.dataset.filter || 'all';
        document.querySelectorAll('.bank-row').forEach(row => {
            const subOk = activeSub === 'all' || row.dataset.sub === activeSub;
            const textOk = !q || row.dataset.text.includes(q);
            row.classList.toggle('hidden', !subOk || !textOk);
        });
    }
</script>
@endpush
