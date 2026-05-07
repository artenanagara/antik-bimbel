@extends('layouts.admin')
@section('title', $tryout->name)
@section('page-title', $tryout->name)
@section('back')
    <a href="{{ route('admin.tryouts.index') }}"
        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
@endsection
@section('header-actions')
    <a href="{{ route('admin.tryouts.edit', $tryout) }}"
        class="inline-flex items-center gap-2 text-sm font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2 rounded-xl transition">
        <i data-lucide="pencil" class="w-4 h-4"></i> Edit
    </a>
@endsection

@section('content')
@php
    $total     = $tryout->questions->count();
    $target    = $tryout->twk_count + $tryout->tiu_count + $tryout->tkp_count;
    $pct       = $target > 0 ? min(100, round($total / $target * 100)) : 0;
    $twkAdded  = $tryout->questions->where('sub_test','TWK')->count();
    $tiuAdded  = $tryout->questions->where('sub_test','TIU')->count();
    $tkpAdded  = $tryout->questions->where('sub_test','TKP')->count();
    $statusMap = ['draft'=>['bg-slate-100','text-slate-600','Draft'],'published'=>['bg-green-100','text-green-700','Published'],'closed'=>['bg-red-100','text-red-700','Closed']];
    $sc        = $statusMap[$tryout->status];
@endphp

<div class="space-y-6">

    {{-- ── Top info strip ─────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
            <div class="px-6 py-5">
                <p class="text-xs text-slate-400 font-medium mb-1">Tipe</p>
                <p class="text-sm font-semibold text-slate-800">
                    {{ $tryout->type === 'simulation' ? 'Simulasi SKD' : 'Sub Test ' . $tryout->sub_test }}
                </p>
            </div>
            <div class="px-6 py-5">
                <p class="text-xs text-slate-400 font-medium mb-1">Durasi</p>
                <p class="text-sm font-semibold text-slate-800">{{ $tryout->duration_minutes }} menit</p>
            </div>
            <div class="px-6 py-5">
                <p class="text-xs text-slate-400 font-medium mb-2">Progres Soal</p>
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500
                            {{ $pct >= 100 ? 'bg-emerald-500' : 'bg-primary-500' }}"
                            style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-xs font-semibold {{ $pct >= 100 ? 'text-emerald-600' : 'text-slate-700' }} shrink-0">
                        {{ $total }}/{{ $target }}
                    </span>
                </div>
                <div class="flex gap-3 mt-2">
                    @foreach ([['TWK','blue',$twkAdded,$tryout->twk_count],['TIU','purple',$tiuAdded,$tryout->tiu_count],['TKP','emerald',$tkpAdded,$tryout->tkp_count]] as [$s,$c,$added,$cap])
                        <span class="text-[11px] font-medium text-{{ $c }}-600">
                            {{ $s }} <span class="font-bold">{{ $added }}</span><span class="text-slate-400">/{{ $cap }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="px-6 py-5">
                <p class="text-xs text-slate-400 font-medium mb-1">Status</p>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc[0] }} {{ $sc[1] }}">
                    {{ $sc[2] }}
                </span>
                @if ($tryout->start_at)
                    <p class="text-[11px] text-slate-400 mt-1.5">
                        {{ \Carbon\Carbon::parse($tryout->start_at)->format('d M Y, H:i') }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Passing grade strip --}}
        <div class="border-t border-slate-100 px-6 py-3 bg-slate-50/60 flex flex-wrap gap-x-8 gap-y-1 text-xs text-slate-500">
            <span>Passing grade TWK: <strong class="text-slate-700">{{ $tryout->pg_twk ?? '–' }}</strong></span>
            <span>TIU: <strong class="text-slate-700">{{ $tryout->pg_tiu ?? '–' }}</strong></span>
            <span>TKP: <strong class="text-slate-700">{{ $tryout->pg_tkp ?? '–' }}</strong></span>
            @if ($tryout->description)
                <span class="text-slate-400">· {{ Str::limit($tryout->description, 80) }}</span>
            @endif
        </div>
    </div>

    {{-- ── Main grid ───────────────────────────────────────────────── --}}
    <div class="grid gap-6 xl:grid-cols-[1fr_260px]">

        {{-- Question list --}}
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800">Daftar Soal</h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $total }} soal · target {{ $target }}</p>
                </div>
                {{-- Filter tabs --}}
                <div class="flex items-center gap-1.5">
                    <button onclick="filterQ('all')" data-q="all"
                        class="q-tab active px-3 py-1 rounded-full text-xs font-semibold transition">Semua</button>
                    @foreach (['TWK','TIU','TKP'] as $st)
                        <button onclick="filterQ('{{ $st }}')" data-q="{{ $st }}"
                            class="q-tab px-3 py-1 rounded-full text-xs font-semibold transition">{{ $st }}</button>
                    @endforeach
                </div>
            </div>

            @if ($total)
                <div class="overflow-y-auto" style="max-height: 560px;">
                    <table class="w-full text-sm" id="q-table">
                        <thead class="sticky top-0 bg-white border-b border-slate-100 z-10">
                            <tr>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-slate-400 uppercase tracking-wide w-10">#</th>
                                <th class="px-2 py-2.5 text-left text-[11px] font-semibold text-slate-400 uppercase tracking-wide w-16">Sub</th>
                                <th class="px-2 py-2.5 text-left text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Soal</th>
                                <th class="px-2 py-2.5 text-left text-[11px] font-semibold text-slate-400 uppercase tracking-wide hidden sm:table-cell">Kesulitan</th>
                                <th class="px-4 py-2.5 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($tryout->questions->sortBy('pivot.order') as $q)
                                @php
                                    $diff = ['easy'=>['text-emerald-600','Mudah'],'medium'=>['text-amber-600','Sedang'],'hard'=>['text-red-600','Sulit']];
                                    $dc   = $diff[$q->difficulty ?? 'medium'] ?? ['text-slate-400','–'];
                                @endphp
                                <tr class="q-row hover:bg-slate-50/60 transition" data-sub="{{ $q->sub_test }}">
                                    <td class="px-5 py-3 text-xs text-slate-400 tabular-nums">{{ $q->pivot->order }}</td>
                                    <td class="px-2 py-3">
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[11px] font-bold
                                            {{ $q->sub_test==='TWK' ? 'bg-blue-100 text-blue-700' : ($q->sub_test==='TIU' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700') }}">
                                            {{ $q->sub_test }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-3 text-slate-700 text-xs max-w-0 w-full">
                                        <div class="line-clamp-2">{{ strip_tags($q->question_text) }}</div>
                                        @if ($q->category)
                                            <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $q->category->name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-3 text-xs {{ $dc[0] }} font-medium hidden sm:table-cell whitespace-nowrap">{{ $dc[1] }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form action="{{ route('admin.tryouts.questions.remove', [$tryout, $q]) }}" method="POST"
                                            onsubmit="return confirm('Hapus soal ini dari try out?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-20 text-center">
                    <i data-lucide="file-question" class="w-12 h-12 text-slate-200 mx-auto mb-3"></i>
                    <p class="text-sm font-medium text-slate-500">Belum ada soal ditambahkan</p>
                    <p class="text-xs text-slate-400 mt-1">Gunakan tombol di bawah untuk menambah soal.</p>
                </div>
            @endif
        </div>

        {{-- Action sidebar --}}
        <aside class="space-y-4">
            {{-- Add questions CTA --}}
            <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Tambah Soal</h3>
                </div>
                <div class="p-4 flex flex-col gap-2.5">
                    <a href="{{ route('admin.tryouts.questions.create', $tryout) }}"
                        class="inline-flex items-center gap-2.5 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 hover:border-primary-300 hover:bg-primary-50/40 transition group">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-primary-100 flex items-center justify-center shrink-0 transition">
                            <i data-lucide="pencil-line" class="w-4 h-4 text-slate-500 group-hover:text-primary-600 transition"></i>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-800 text-xs">Buat Manual</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Tulis soal satu per satu</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.tryouts.questions.bank-select', $tryout) }}"
                        class="inline-flex items-center gap-2.5 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 hover:border-primary-300 hover:bg-primary-50/40 transition group">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-primary-100 flex items-center justify-center shrink-0 transition">
                            <i data-lucide="database" class="w-4 h-4 text-slate-500 group-hover:text-primary-600 transition"></i>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-800 text-xs">Bank Soal</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Pilih dari soal yang ada</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.tryouts.questions.import-page', $tryout) }}"
                        class="inline-flex items-center gap-2.5 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 hover:border-primary-300 hover:bg-primary-50/40 transition group">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-primary-100 flex items-center justify-center shrink-0 transition">
                            <i data-lucide="upload-cloud" class="w-4 h-4 text-slate-500 group-hover:text-primary-600 transition"></i>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-800 text-xs">Import Excel</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Upload file .xlsx / .xls</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Sub-test breakdown --}}
            <div class="rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Distribusi Soal</h3>
                </div>
                <div class="p-4 space-y-3">
                    @foreach ([['TWK','blue',$twkAdded,$tryout->twk_count],['TIU','purple',$tiuAdded,$tryout->tiu_count],['TKP','emerald',$tkpAdded,$tryout->tkp_count]] as [$sub,$color,$added,$cap])
                        @php $p = $cap > 0 ? min(100, round($added/$cap*100)) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-semibold text-slate-600">{{ $sub }}</span>
                                <span class="text-xs text-slate-500 tabular-nums">
                                    <span class="{{ $added >= $cap && $cap > 0 ? 'text-'.$color.'-600 font-bold' : 'font-medium text-slate-700' }}">{{ $added }}</span>
                                    / {{ $cap }}
                                </span>
                            </div>
                            <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full bg-{{ $color }}-400 transition-all duration-500"
                                    style="width: {{ $p }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Passing grade --}}
            <div class="rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Passing Grade</h3>
                </div>
                <div class="p-4 space-y-1.5 text-xs">
                    @foreach (['TWK' => $tryout->pg_twk, 'TIU' => $tryout->pg_tiu, 'TKP' => $tryout->pg_tkp] as $s => $pg)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">{{ $s }}</span>
                            <span class="font-semibold text-slate-800">{{ $pg ?? '–' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('styles')
<style>
    .q-tab { background: rgb(241 245 249); color: rgb(100 116 139); }
    .q-tab.active { background: rgb(255 247 237); color: rgb(234 88 12); box-shadow: 0 0 0 2px rgb(254 215 170); }
    .q-row.hidden { display: none; }
</style>
@endpush

@push('scripts')
<script>
    function filterQ(sub) {
        document.querySelectorAll('.q-tab').forEach(b => b.classList.toggle('active', b.dataset.q === sub));
        document.querySelectorAll('.q-row').forEach(r => r.classList.toggle('hidden', sub !== 'all' && r.dataset.sub !== sub));
    }
</script>
@endpush
