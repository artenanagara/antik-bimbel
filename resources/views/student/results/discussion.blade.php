@extends('layouts.student')
@section('title', 'Pembahasan')
@section('content')
    @php
        $answersByQuestion = $studentTryout->answers->keyBy('question_id');
        $questions = $studentTryout->tryout->questions->sortBy(fn($question) => $question->pivot->order ?? $question->id)->values();

        // Build filter data
        $subTests = $questions->pluck('sub_test')->unique()->filter()->values();
        $categories = $questions->filter(fn($q) => $q->category)->map(fn($q) => ['id' => $q->category_id, 'name' => $q->category->name, 'sub' => $q->sub_test])->unique('id')->values();
    @endphp

    <div class="max-w-3xl space-y-4">
        <div class="flex items-start gap-2">
            <a href="{{ route('student.results.show', $studentTryout) }}"
                class="shrink-0 -ml-1 mt-0.5 w-9 h-9 rounded-full inline-flex items-center justify-center text-slate-500 hover:bg-white hover:text-slate-800 hover:shadow-soft transition"
                title="Kembali ke Nilai">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div class="min-w-0 flex-1 pt-1">
                <h2 class="text-lg sm:text-xl font-bold text-slate-800 leading-tight wrap-break-word">Pembahasan: {{ $studentTryout->tryout->name }}</h2>
                <p class="mt-0.5 text-xs sm:text-sm text-slate-500">{{ $answersByQuestion->whereNotNull('option_id')->count() }}/{{ $questions->count() }} soal dijawab</p>
            </div>
        </div>

        {{-- Filter chips --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-3 flex flex-wrap gap-2 items-center">
            <button onclick="setFilter('all')" data-filter="all"
                class="filter-btn active px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                Semua
            </button>
            @foreach ($subTests as $st)
                <button onclick="setFilter('sub:{{ $st }}')" data-filter="sub:{{ $st }}"
                    class="filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                    {{ $st }}
                </button>
            @endforeach
            @if ($categories->count())
                <span class="w-px h-4 bg-slate-200"></span>
                @foreach ($categories as $cat)
                    <button onclick="setFilter('cat:{{ $cat['id'] }}')" data-filter="cat:{{ $cat['id'] }}"
                        class="filter-btn px-3 py-1.5 rounded-lg text-xs font-medium transition">
                        {{ $cat['name'] }}
                    </button>
                @endforeach
            @endif
        </div>

        @forelse ($questions as $i => $q)
            @php
                $ans = $answersByQuestion->get($q->id);
                $myOpt = $ans?->option;
            @endphp
            <div class="q-card bg-white rounded-2xl border border-slate-200 p-4 sm:p-5"
                data-sub="{{ $q->sub_test }}" data-cat="{{ $q->category_id ?? '' }}">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="text-sm font-semibold text-slate-500">{{ $i + 1 }}</span>
                    <span
                        class="inline-flex px-2 py-0.5 rounded text-xs font-semibold
                {{ $q->sub_test === 'TWK' ? 'bg-primary-50 text-primary-700' : ($q->sub_test === 'TIU' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                        {{ $q->sub_test }}
                    </span>
                    @if ($q->category)
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600">{{ $q->category->name }}</span>
                    @endif
                    @if ($q->sub_test !== 'TKP')
                        @if ($myOpt && $myOpt->is_correct)
                            <span class="inline-flex items-center gap-1 text-xs text-green-700 font-medium"><i
                                    data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> Benar</span>
                        @elseif($myOpt)
                            <span class="inline-flex items-center gap-1 text-xs text-red-600 font-medium"><i
                                    data-lucide="x-circle" class="w-3.5 h-3.5"></i> Salah</span>
                        @else
                            <span class="text-xs text-slate-400">Tidak dijawab</span>
                        @endif
                    @else
                        <span class="text-xs text-slate-600 font-medium">Skor: {{ $myOpt ? $myOpt->score : 0 }}</span>
                    @endif
                </div>

                <div class="rich-content text-sm text-slate-800 leading-relaxed mb-4">{!! $q->question_text !!}</div>
                @if ($q->question_image)
                    <div class="mb-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
                        <img src="{{ $q->question_image }}" alt="Gambar soal {{ $i + 1 }}"
                            class="max-h-72 rounded-lg object-contain">
                    </div>
                @endif

                <div class="space-y-2">
                    @foreach ($q->options as $opt)
                        @php
                            $isMyAnswer = $myOpt && $myOpt->id === $opt->id;
                            $isCorrect = $opt->is_correct;
                        @endphp
                        <div
                            class="flex items-start gap-3 px-3 py-2.5 rounded-lg border
                {{ $isCorrect && $q->sub_test !== 'TKP' ? 'border-green-300 bg-green-50' : ($isMyAnswer && !$isCorrect ? 'border-red-200 bg-red-50' : 'border-slate-100') }}">
                            <span
                                class="w-6 h-6 shrink-0 flex items-center justify-center rounded-full text-xs font-bold
                    {{ $isMyAnswer ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $opt->label }}</span>
                            <span class="text-sm text-slate-700 flex-1">{{ $opt->text }}</span>
                            @if ($q->sub_test === 'TKP')
                                <span class="text-xs text-slate-400 shrink-0">Skor: {{ $opt->score }}</span>
                            @endif
                            @if ($isCorrect && $q->sub_test !== 'TKP')
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-green-600 shrink-0"></i>
                            @elseif($isMyAnswer && !$isCorrect && $q->sub_test !== 'TKP')
                                <i data-lucide="x-circle" class="w-4 h-4 text-red-500 shrink-0"></i>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($q->explanation)
                    <div class="mt-4 bg-amber-50 border border-amber-200 rounded-lg p-3">
                        <p class="text-xs font-semibold text-amber-800 mb-1">Pembahasan:</p>
                        <div class="rich-content text-sm text-amber-900 leading-relaxed">{!! $q->explanation !!}</div>
                    </div>
                @else
                    <div class="mt-4 bg-slate-50 border border-slate-200 rounded-lg p-3">
                        <p class="text-sm text-slate-500">Belum ada pembahasan untuk soal ini.</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-10 text-center text-sm text-slate-400">
                Belum ada soal dalam try out ini.
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
<script>
    let activeFilter = 'all';

    function setFilter(filter) {
        activeFilter = filter;

        document.querySelectorAll('.filter-btn').forEach(btn => {
            const isActive = btn.dataset.filter === filter;
            btn.classList.toggle('active', isActive);
        });

        document.querySelectorAll('.q-card').forEach(card => {
            let show = true;
            if (filter.startsWith('sub:')) {
                show = card.dataset.sub === filter.slice(4);
            } else if (filter.startsWith('cat:')) {
                show = card.dataset.cat === filter.slice(4);
            }
            card.style.display = show ? '' : 'none';
        });
    }
</script>
<style>
    .filter-btn { background: #f1f5f9; color: #64748b; }
    .filter-btn:hover { background: #e2e8f0; color: #334155; }
    .filter-btn.active { background: #fff7ed; color: #c2410c; font-weight: 700; box-shadow: inset 0 0 0 1.5px #f97316; }
</style>
@endpush
