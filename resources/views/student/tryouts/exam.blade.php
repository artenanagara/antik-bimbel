<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $tryout->name }} - TO SKD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#EA580C',
                            50: '#FFF7ED', 100: '#FFEDD5', 200: '#FED7AA', 300: '#FDBA74',
                            400: '#FB923C', 500: '#F97316', 600: '#EA580C', 700: '#C2410C',
                            800: '#9A3412', 900: '#7C2D12'
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [
                    ['\\(', '\\)'],
                    ['$', '$']
                ],
                displayMath: [
                    ['\\[', '\\]'],
                    ['$$', '$$']
                ]
            },
            svg: {
                fontCache: 'global'
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>
    <style>
        .font-kecil {
            font-size: 13px;
        }

        .font-normal-size {
            font-size: 15px;
        }

        .font-besar {
            font-size: 17px;
        }

        .font-sangat-besar {
            font-size: 20px;
        }

        .q-text {
            transition: font-size 0.15s ease;
        }

        .rich-content p {
            margin: 0.35rem 0;
        }

        .rich-content ul,
        .rich-content ol {
            margin: 0.5rem 0;
            padding-left: 1.25rem;
        }

        .rich-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.75rem 0;
        }

        .rich-content td,
        .rich-content th {
            border: 1px solid rgb(203 213 225);
            padding: 0.5rem;
            vertical-align: top;
        }

        /* Custom radio */
        .c-radio {
            appearance: none;
            -webkit-appearance: none;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid rgb(203 213 225);
            border-radius: 9999px;
            background: #fff;
            cursor: pointer;
            display: inline-grid;
            place-content: center;
            transition: border-color .15s ease, background-color .15s ease, box-shadow .15s ease;
            flex-shrink: 0;
            margin: 0;
            position: relative;
        }
        .c-radio::before {
            content: "";
            width: 0.6rem;
            height: 0.6rem;
            border-radius: 9999px;
            background: #EA580C;
            transform: scale(0);
            transition: transform .15s ease;
        }
        .c-radio:hover:not(:disabled) { border-color: #FDBA74; }
        .c-radio:checked {
            border-color: #EA580C;
            background: #FFF7ED;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.10);
        }
        .c-radio:checked::before { transform: scale(1); }
        .c-radio:focus-visible { outline: 2px solid #FB923C; outline-offset: 2px; }
        .c-radio:disabled { opacity: .5; cursor: not-allowed; }
        .c-radio-lg { width: 1.5rem; height: 1.5rem; }
        .c-radio-lg::before { width: 0.75rem; height: 0.75rem; }
    </style>
</head>

<body class="h-full bg-slate-100 flex flex-col">

    {{-- Top Bar --}}
    <header
        class="bg-white border-b border-slate-200 px-3 sm:px-4 py-2 flex items-center justify-between gap-2 sm:gap-4 sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-2 min-w-0 flex-1">
            <button onclick="toggleNav()"
                class="lg:hidden shrink-0 p-2 -ml-1 rounded-lg text-slate-600 hover:bg-slate-100" aria-label="Navigasi soal">
                <i data-lucide="layout-grid" class="w-5 h-5"></i>
            </button>
            <h1 class="font-semibold text-slate-800 text-sm truncate">{{ $tryout->name }}</h1>
        </div>

        {{-- Timer --}}
        <div id="timer" class="shrink-0 flex items-center gap-1.5 sm:gap-2 bg-primary-50 border border-primary-200 px-2.5 sm:px-3 py-1.5 rounded-lg">
            <i data-lucide="timer" class="w-4 h-4 text-primary-600"></i>
            <span id="timer-display"
                class="font-mono font-bold text-primary-700 text-xs sm:text-sm">{{ gmdate('H:i:s', $remainingSeconds) }}</span>
        </div>

        {{-- Font size: hidden on mobile, popover toggle --}}
        <div class="relative hidden sm:block">
            <div class="flex items-center gap-1">
                <span class="text-xs text-slate-500 hidden md:block">Ukuran:</span>
                @foreach (['kecil' => 'A-', 'normal-size' => 'A', 'besar' => 'A+', 'sangat-besar' => 'A++'] as $k => $v)
                    <button onclick="setFont('{{ $k }}')" data-font="{{ $k }}"
                        class="font-btn px-2 py-1 text-xs rounded border border-slate-300 hover:bg-slate-100 text-slate-600 transition-colors">{{ $v }}</button>
                @endforeach
            </div>
        </div>
        <div class="sm:hidden relative">
            <button onclick="document.getElementById('font-pop').classList.toggle('hidden')"
                class="shrink-0 p-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100" aria-label="Ukuran font">
                <i data-lucide="type" class="w-4 h-4"></i>
            </button>
            <div id="font-pop" class="hidden absolute right-0 mt-2 bg-white border border-slate-200 rounded-xl shadow-lg p-2 flex gap-1 z-50">
                @foreach (['kecil' => 'A-', 'normal-size' => 'A', 'besar' => 'A+', 'sangat-besar' => 'A++'] as $k => $v)
                    <button onclick="setFont('{{ $k }}'); document.getElementById('font-pop').classList.add('hidden')" data-font="{{ $k }}"
                        class="font-btn px-2.5 py-1.5 text-xs rounded border border-slate-300 hover:bg-slate-100 text-slate-600 transition-colors">{{ $v }}</button>
                @endforeach
            </div>
        </div>

        {{-- Submit --}}
        <button onclick="openSubmitModal()"
            class="shrink-0 inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-xs sm:text-sm font-semibold px-2.5 sm:px-4 py-2 rounded-lg transition-colors">
            <i data-lucide="send" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Kumpulkan</span>
        </button>
    </header>

    <div class="flex flex-1 overflow-hidden relative">
        {{-- Question area --}}
        <main class="flex-1 overflow-y-auto p-3 sm:p-6">
            @foreach ($questions as $i => $q)
                <div id="q-{{ $q->id }}" class="question-block hidden" data-qid="{{ $q->id }}"
                    data-subtest="{{ $q->sub_test }}">
                    <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="flex flex-wrap items-center gap-2 min-w-0">
                                <span class="text-sm font-semibold text-slate-500">Soal {{ $i + 1 }} /
                                    {{ $questions->count() }}</span>
                                <span
                                    class="inline-flex px-2 py-0.5 rounded text-xs font-semibold
                            {{ $q->sub_test === 'TWK' ? 'bg-primary-50 text-primary-700' : ($q->sub_test === 'TIU' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                    {{ $q->sub_test }}
                                </span>
                            </div>
                            <button onclick="toggleFlag({{ $q->id }})" id="flag-btn-{{ $q->id }}"
                                class="flex items-center gap-1.5 text-xs border rounded-lg px-2.5 py-1.5 transition-colors
                                   {{ isset($answers[$q->id]) && $answers[$q->id]->is_flagged ? 'border-amber-400 bg-amber-50 text-amber-700' : 'border-slate-300 text-slate-500 hover:border-amber-300 hover:text-amber-600' }}"
                                title="Ragu-ragu">
                                <i data-lucide="flag" class="w-3.5 h-3.5"></i>
                                <span class="hidden sm:inline">Ragu-ragu</span>
                            </button>
                        </div>

                        <div class="rich-content q-text font-normal-size text-slate-800 leading-relaxed mb-4">
                            {!! $q->question_text !!}
                        </div>
                        @if ($q->question_image)
                            <div class="mb-6 rounded-lg border border-slate-200 bg-slate-50 p-3">
                                <img src="{{ $q->question_image }}" alt="Gambar soal {{ $i + 1 }}"
                                    class="max-h-80 rounded-lg object-contain">
                            </div>
                        @endif

                        <div class="space-y-2.5" id="options-{{ $q->id }}">
                            @foreach ($q->options as $opt)
                                @php $selected = isset($answers[$q->id]) && $answers[$q->id]->option_id == $opt->id; @endphp
                                <label id="opt-label-{{ $opt->id }}"
                                    class="option-label flex items-start gap-3 p-3 sm:p-4 rounded-xl border-2 cursor-pointer transition-all
                                  {{ $selected ? 'border-primary-500 bg-primary-50' : 'border-slate-200 hover:border-slate-300 bg-white' }}"
                                    data-qid="{{ $q->id }}" data-optid="{{ $opt->id }}">
                                    <input type="radio" name="opt-{{ $q->id }}" value="{{ $opt->id }}"
                                        {{ $selected ? 'checked' : '' }}
                                        class="mt-0.5 text-primary-600 focus:ring-primary-500 shrink-0"
                                        onchange="saveAnswer({{ $q->id }}, {{ $opt->id }})">
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        <span
                                            class="w-7 h-7 shrink-0 flex items-center justify-center rounded-full text-xs font-bold
                                {{ $selected ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $opt->label }}</span>
                                        <span
                                            class="q-text font-normal-size text-slate-700 leading-relaxed break-words">{{ $opt->text }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        {{-- Nav buttons --}}
                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-100">
                            <button onclick="goToQuestion({{ $i - 1 }})" {{ $i === 0 ? 'disabled' : '' }}
                                class="inline-flex items-center gap-1.5 text-sm text-slate-600 hover:text-slate-800 disabled:opacity-30 disabled:cursor-not-allowed">
                                <i data-lucide="chevron-left" class="w-4 h-4"></i> Sebelumnya
                            </button>
                            <span class="text-xs text-slate-400">{{ $i + 1 }} /
                                {{ $questions->count() }}</span>
                            @if ($i < $questions->count() - 1)
                                <button onclick="goToQuestion({{ $i + 1 }})"
                                    class="inline-flex items-center gap-1.5 text-sm text-slate-600 hover:text-slate-800">
                                    Berikutnya <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                </button>
                            @else
                                <button onclick="openSubmitModal()"
                                    class="inline-flex items-center gap-1.5 text-sm bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-1.5 rounded-lg transition-colors">
                                    Kumpulkan <i data-lucide="send" class="w-4 h-4"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </main>

        {{-- Backdrop for mobile drawer --}}
        <div id="nav-backdrop" onclick="toggleNav()"
            class="hidden lg:hidden fixed inset-0 bg-black/40 z-40"></div>

        {{-- Navigation Panel: drawer on mobile, fixed sidebar on desktop --}}
        <aside id="nav-panel"
            class="hidden lg:flex w-72 lg:w-56 xl:w-64 bg-white border-l border-slate-200 flex-col shrink-0 overflow-hidden
                   fixed lg:relative inset-y-0 right-0 z-50 lg:z-auto shadow-2xl lg:shadow-none">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-600">Navigasi Soal</p>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-xs text-slate-500">
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-primary-600 inline-block"></span>
                            Dijawab</span>
                        <span class="flex items-center gap-1"><span
                                class="w-3 h-3 rounded border-2 border-amber-400 inline-block"></span> Ragu</span>
                        <span class="flex items-center gap-1"><span
                                class="w-3 h-3 rounded border border-slate-300 inline-block"></span> Belum</span>
                    </div>
                </div>
                <button onclick="toggleNav()" class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-100" aria-label="Tutup">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-3">
                @php $subTests = ['TWK','TIU','TKP']; @endphp
                @foreach ($subTests as $st)
                    @php $stQ = $questions->where('sub_test', $st); @endphp
                    @if ($stQ->count())
                        <p class="text-xs font-semibold text-slate-400 mb-2 mt-1 {{ !$loop->first ? 'mt-3' : '' }}">
                            {{ $st }}</p>
                        <div class="flex flex-wrap gap-1.5 mb-2">
                            @foreach ($stQ as $j => $q)
                                @php
                                    $ans = $answers[$q->id] ?? null;
                                    $answered = $ans && $ans->option_id;
                                    $flagged = $ans && $ans->is_flagged;
                                    $qIndex = $questions->search(fn($x) => $x->id === $q->id);
                                @endphp
                                <button onclick="goToQuestion({{ $qIndex }}); if(window.innerWidth<1024) toggleNav()" id="nav-{{ $q->id }}"
                                    class="nav-btn w-9 h-9 lg:w-8 lg:h-8 text-xs font-semibold rounded transition-all border-2
                            {{ $answered && $flagged ? 'bg-amber-400 border-amber-500 text-white' : ($answered ? 'bg-primary-600 border-primary-600 text-white' : ($flagged ? 'bg-white border-amber-400 text-amber-600' : 'bg-white border-slate-300 text-slate-600 hover:border-slate-400')) }}"
                                    title="Soal {{ $qIndex + 1 }}">
                                    {{ $qIndex + 1 }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="px-4 py-3 border-t border-slate-100 text-xs text-slate-500">
                <p>Terjawab: <span id="answered-count"
                        class="font-semibold text-slate-700">{{ $answers->where('option_id', '!=', null)->count() }}</span>
                    / {{ $questions->count() }}</p>
                <p>Ragu-ragu: <span id="flagged-count"
                        class="font-semibold text-slate-700">{{ $answers->where('is_flagged', true)->count() }}</span>
                </p>
            </div>
        </aside>
    </div>

    {{-- Submit Modal --}}
    <div id="submit-modal" class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center p-4"
        style="display:none">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <i data-lucide="send" class="w-5 h-5 text-green-600"></i>
                </div>
                <h3 class="font-bold text-slate-800">Kumpulkan Jawaban?</h3>
            </div>
            <p class="text-sm text-slate-600 mb-2">
                Anda sudah menjawab <strong id="modal-answered">0</strong> dari
                <strong>{{ $questions->count() }}</strong> soal.
            </p>
            <p id="modal-unanswered-warn"
                class="text-sm text-amber-700 bg-amber-50 border border-amber-200 px-3 py-2 rounded-lg mb-4 hidden">
                Masih ada soal yang belum dijawab. Soal yang kosong tidak mendapat skor.
            </p>
            <div class="flex gap-3">
                <button onclick="closeSubmitModal()"
                    class="flex-1 text-sm border border-slate-300 hover:bg-slate-50 py-2.5 rounded-lg font-medium transition-colors">Batal</button>
                <form action="{{ route('student.tryouts.submit', [$tryout, $studentTryout]) }}" method="POST"
                    class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full text-sm bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-lg transition-colors">
                        Ya, Kumpulkan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const ANSWER_URL = "{{ route('student.tryouts.answer', [$tryout, $studentTryout]) }}";
        const FLAG_URL = "{{ route('student.tryouts.flag', [$tryout, $studentTryout]) }}";
        let currentIndex = 0;
        let totalQuestions = {{ $questions->count() }};
        let answeredMap = {}; // qid -> optId
        let flaggedMap = {}; // qid -> bool

        // Init state from server
        @foreach ($answers as $qid => $ans)
            @if ($ans->option_id)
                answeredMap[{{ $qid }}] = {{ $ans->option_id }};
            @endif
            @if ($ans->is_flagged)
                flaggedMap[{{ $qid }}] = true;
            @endif
        @endforeach

        function goToQuestion(index) {
            if (index < 0 || index >= totalQuestions) return;
            document.querySelectorAll('.question-block').forEach(el => el.classList.add('hidden'));
            const blocks = document.querySelectorAll('.question-block');
            blocks[index].classList.remove('hidden');

            // Highlight current nav
            document.querySelectorAll('.nav-btn').forEach((btn, i) => {
                btn.classList.toggle('ring-2', i === index);
                btn.classList.toggle('ring-primary-400', i === index);
            });

            currentIndex = index;
            window.scrollTo({
                top: 0
            });
        }

        function saveAnswer(qid, optId) {
            // Update UI
            document.querySelectorAll(`[id^="opt-label-"][data-qid="${qid}"]`).forEach(() => {});
            document.querySelectorAll(`label[data-qid="${qid}"]`).forEach(el => {
                const oid = parseInt(el.dataset.optid);
                const picked = oid === optId;
                el.classList.toggle('border-primary-500', picked);
                el.classList.toggle('bg-primary-50', picked);
                el.classList.toggle('border-slate-200', !picked);
                el.classList.toggle('bg-white', !picked);
                const circle = el.querySelector('span.w-7');
                if (circle) {
                    circle.classList.toggle('bg-primary-600', picked);
                    circle.classList.toggle('text-white', picked);
                    circle.classList.toggle('bg-slate-100', !picked);
                    circle.classList.toggle('text-slate-600', !picked);
                }
            });

            answeredMap[qid] = optId;
            updateNavBtn(qid);
            updateCounts();

            fetch(ANSWER_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    question_id: qid,
                    option_id: optId
                })
            });
        }

        function toggleFlag(qid) {
            const current = !!flaggedMap[qid];
            flaggedMap[qid] = !current;
            const btn = document.getElementById(`flag-btn-${qid}`);
            if (flaggedMap[qid]) {
                btn.classList.add('border-amber-400', 'bg-amber-50', 'text-amber-700');
                btn.classList.remove('border-slate-300', 'text-slate-500');
            } else {
                btn.classList.remove('border-amber-400', 'bg-amber-50', 'text-amber-700');
                btn.classList.add('border-slate-300', 'text-slate-500');
            }
            updateNavBtn(qid);
            updateCounts();

            fetch(FLAG_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    question_id: qid
                })
            });
        }

        function updateNavBtn(qid) {
            const block = document.querySelector(`.question-block[data-qid="${qid}"]`);
            if (!block) return;
            const blockIndex = Array.from(document.querySelectorAll('.question-block')).indexOf(block);
            const btn = document.querySelectorAll('.nav-btn')[blockIndex];
            if (!btn) return;

            const answered = !!answeredMap[qid];
            const flagged = !!flaggedMap[qid];

            btn.className = btn.className.replace(/bg-\S+|border-\S+|text-\S+/g, '').trim();
            btn.classList.add('w-9', 'h-9', 'lg:w-8', 'lg:h-8', 'text-xs', 'font-semibold', 'rounded', 'transition-all', 'border-2', 'nav-btn');
            if (answered && flagged) {
                btn.classList.add('bg-amber-400', 'border-amber-500', 'text-white');
            } else if (answered) {
                btn.classList.add('bg-primary-600', 'border-primary-600', 'text-white');
            } else if (flagged) {
                btn.classList.add('bg-white', 'border-amber-400', 'text-amber-600');
            } else {
                btn.classList.add('bg-white', 'border-slate-300', 'text-slate-600', 'hover:border-slate-400');
            }
        }

        function updateCounts() {
            const answeredCount = Object.keys(answeredMap).length;
            const flaggedCount = Object.values(flaggedMap).filter(Boolean).length;
            document.getElementById('answered-count').textContent = answeredCount;
            document.getElementById('flagged-count').textContent = flaggedCount;
        }

        function setFont(size) {
            document.querySelectorAll('.q-text').forEach(el => {
                el.className = el.className.replace(/font-kecil|font-normal-size|font-besar|font-sangat-besar/g, '')
                    .trim();
                el.classList.add('font-' + size);
            });
            document.querySelectorAll('.font-btn').forEach(btn => {
                const active = btn.dataset.font === size;
                btn.classList.toggle('bg-primary-600', active);
                btn.classList.toggle('text-white', active);
                btn.classList.toggle('border-primary-600', active);
                btn.classList.toggle('border-slate-300', !active);
            });
        }

        function toggleNav() {
            const panel = document.getElementById('nav-panel');
            const backdrop = document.getElementById('nav-backdrop');
            const opening = panel.classList.contains('hidden');
            panel.classList.toggle('hidden');
            panel.classList.toggle('flex', opening);
            backdrop.classList.toggle('hidden');
        }

        // Timer
        let remaining = {{ $remainingSeconds }};

        function updateTimer() {
            if (remaining <= 0) {
                document.getElementById('timer-display').textContent = '00:00:00';
                document.getElementById('timer').classList.replace('bg-primary-50', 'bg-red-50');
                document.getElementById('timer').classList.replace('border-primary-200', 'border-red-200');
                document.getElementById('timer-display').classList.replace('text-primary-700', 'text-red-700');
                // Auto-submit
                document.querySelector('#submit-modal form').submit();
                return;
            }
            remaining--;
            const h = String(Math.floor(remaining / 3600)).padStart(2, '0');
            const m = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
            const s = String(remaining % 60).padStart(2, '0');
            document.getElementById('timer-display').textContent = `${h}:${m}:${s}`;
            if (remaining <= 300) {
                document.getElementById('timer').classList.replace('bg-primary-50', 'bg-red-50');
                document.getElementById('timer').classList.replace('border-primary-200', 'border-red-200');
                document.getElementById('timer-display').classList.replace('text-primary-700', 'text-red-700');
            }
        }
        setInterval(updateTimer, 1000);

        function openSubmitModal() {
            const answered = Object.keys(answeredMap).length;
            document.getElementById('modal-answered').textContent = answered;
            const warn = document.getElementById('modal-unanswered-warn');
            if (answered < totalQuestions) warn.classList.remove('hidden');
            else warn.classList.add('hidden');
            document.getElementById('submit-modal').style.display = 'flex';
        }

        function closeSubmitModal() {
            document.getElementById('submit-modal').style.display = 'none';
        }

        // Init
        goToQuestion(0);
        lucide.createIcons();
    </script>
</body>

</html>
