@php
    $editorId = $id ?? 'rich_' . \Illuminate\Support\Str::slug($name, '_') . '_' . uniqid();
    $editorValue = old($name, $value ?? '');
    $editorRows = $rows ?? 7;

    // Equation templates grouped by category. Each entry: [preview html, caption, html to insert]
    $eqGroups = [
        'Pecahan & Akar' => [
            ['preview' => '<span class="rmath rmath-frac"><span class="rmath-num">a</span><span class="rmath-den">b</span></span>',
             'caption' => 'Pecahan',
             'html'    => '<span class="rmath rmath-frac" contenteditable="false"><span class="rmath-num" contenteditable="true">a</span><span class="rmath-den" contenteditable="true">b</span></span>&nbsp;'],
            ['preview' => '<span class="rmath rmath-sqrt"><span class="rmath-sym">&radic;</span><span class="rmath-rad">x</span></span>',
             'caption' => 'Akar',
             'html'    => '<span class="rmath rmath-sqrt" contenteditable="false"><span class="rmath-sym">&radic;</span><span class="rmath-rad" contenteditable="true">x</span></span>&nbsp;'],
            ['preview' => '<span class="rmath rmath-nsqrt"><span class="rmath-idx">n</span><span class="rmath-sym">&radic;</span><span class="rmath-rad">x</span></span>',
             'caption' => 'Akar n',
             'html'    => '<span class="rmath rmath-nsqrt" contenteditable="false"><span class="rmath-idx" contenteditable="true">n</span><span class="rmath-sym">&radic;</span><span class="rmath-rad" contenteditable="true">x</span></span>&nbsp;'],
        ],
        'Pangkat & Indeks' => [
            ['preview' => '<span class="rmath">x<sup>n</sup></span>',
             'caption' => 'Pangkat',
             'html'    => '<span class="rmath" contenteditable="false"><span contenteditable="true" class="rmath-slot">x</span><sup contenteditable="true" class="rmath-slot">n</sup></span>&nbsp;'],
            ['preview' => '<span class="rmath">x<sub>n</sub></span>',
             'caption' => 'Subscript',
             'html'    => '<span class="rmath" contenteditable="false"><span contenteditable="true" class="rmath-slot">x</span><sub contenteditable="true" class="rmath-slot">n</sub></span>&nbsp;'],
            ['preview' => '<span class="rmath">x<sub>n</sub><sup>m</sup></span>',
             'caption' => 'Sub & Sup',
             'html'    => '<span class="rmath" contenteditable="false"><span contenteditable="true" class="rmath-slot">x</span><sub contenteditable="true" class="rmath-slot">n</sub><sup contenteditable="true" class="rmath-slot">m</sup></span>&nbsp;'],
        ],
        'Operator Besar' => [
            ['preview' => '<span class="rmath rmath-bigop"><span class="rmath-up">n</span><span class="rmath-op">&Sigma;</span><span class="rmath-down">i=1</span></span>',
             'caption' => 'Sigma',
             'html'    => '<span class="rmath rmath-bigop" contenteditable="false"><span class="rmath-up" contenteditable="true">n</span><span class="rmath-op">&Sigma;</span><span class="rmath-down" contenteditable="true">i=1</span></span>&nbsp;'],
            ['preview' => '<span class="rmath rmath-bigop"><span class="rmath-up">n</span><span class="rmath-op">&prod;</span><span class="rmath-down">i=1</span></span>',
             'caption' => 'Pi (produk)',
             'html'    => '<span class="rmath rmath-bigop" contenteditable="false"><span class="rmath-up" contenteditable="true">n</span><span class="rmath-op">&prod;</span><span class="rmath-down" contenteditable="true">i=1</span></span>&nbsp;'],
            ['preview' => '<span class="rmath rmath-bigop"><span class="rmath-up">b</span><span class="rmath-op rmath-int">&int;</span><span class="rmath-down">a</span></span>',
             'caption' => 'Integral',
             'html'    => '<span class="rmath rmath-bigop" contenteditable="false"><span class="rmath-up" contenteditable="true">b</span><span class="rmath-op rmath-int">&int;</span><span class="rmath-down" contenteditable="true">a</span></span>&nbsp;'],
            ['preview' => '<span class="rmath rmath-bigop"><span class="rmath-op rmath-lim">lim</span><span class="rmath-down">x&rarr;0</span></span>',
             'caption' => 'Limit',
             'html'    => '<span class="rmath rmath-bigop" contenteditable="false"><span class="rmath-op rmath-lim">lim</span><span class="rmath-down" contenteditable="true">x&rarr;0</span></span>&nbsp;'],
        ],
        'Tanda Kurung' => [
            ['preview' => '<span class="rmath">(<span>a</span>)</span>',
             'caption' => 'Kurung',
             'html'    => '<span class="rmath" contenteditable="false">(<span contenteditable="true" class="rmath-slot">a</span>)</span>&nbsp;'],
            ['preview' => '<span class="rmath">|<span>a</span>|</span>',
             'caption' => 'Absolut',
             'html'    => '<span class="rmath" contenteditable="false">|<span contenteditable="true" class="rmath-slot">a</span>|</span>&nbsp;'],
            ['preview' => '<span class="rmath rmath-matrix">[<span class="rmath-mwrap"><span>a</span><span>b</span></span>]</span>',
             'caption' => 'Matriks',
             'html'    => '<span class="rmath rmath-matrix" contenteditable="false">[<span class="rmath-mwrap"><span contenteditable="true" class="rmath-slot">a</span><span contenteditable="true" class="rmath-slot">b</span></span>]</span>&nbsp;'],
        ],
    ];

    $eqSymbolGroups = [
        'Operator' => ['+', '−', '×', '÷', '±', '∓', '·', '∗', '⊕', '⊗'],
        'Relasi'   => ['=', '≠', '≈', '≡', '≤', '≥', '<', '>', '∝', '∼'],
        'Yunani'   => ['π', 'θ', 'α', 'β', 'γ', 'δ', 'ε', 'λ', 'μ', 'σ', 'φ', 'ω', 'Δ', 'Ω'],
        'Lainnya'  => ['∞', '∂', '∇', '∈', '∉', '⊂', '⊃', '∪', '∩', '∅', '→', '↔', '⇒', '⇔', '°', '′', '″'],
    ];
@endphp

<div class="rich-editor" data-rich-editor>
    <label class="mb-1.5 block text-sm font-medium text-slate-700">{{ $label }}</label>

    <div class="relative rounded-xl border border-slate-200 bg-white shadow-sm transition focus-within:border-primary-500 focus-within:ring-4 focus-within:ring-primary-100">
        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-1 rounded-t-xl border-b border-slate-200 bg-slate-50/70 px-2 py-1.5">
            <button type="button" data-rich-command="bold" class="rich-btn font-bold" title="Bold">B</button>
            <button type="button" data-rich-command="italic" class="rich-btn italic font-serif" title="Italic">I</button>
            <button type="button" data-rich-command="underline" class="rich-btn underline" title="Underline">U</button>

            <span class="mx-1 h-5 w-px bg-slate-200"></span>

            <button type="button" data-rich-command="insertUnorderedList" class="rich-btn" title="Bullet list">
                <i data-lucide="list" class="h-4 w-4"></i>
            </button>
            <button type="button" data-rich-command="insertOrderedList" class="rich-btn" title="Numbered list">
                <i data-lucide="list-ordered" class="h-4 w-4"></i>
            </button>

            {{-- Table picker --}}
            <div class="relative" data-rich-pop-wrap data-pop="table">
                <button type="button" data-pop-toggle class="rich-btn" title="Sisipkan tabel">
                    <i data-lucide="table-2" class="h-4 w-4"></i>
                </button>
                <div data-pop-body
                    class="rich-pop absolute left-0 top-full z-50 mt-1 hidden w-max rounded-lg border border-slate-200 bg-white p-2 shadow-xl">
                    <div data-rich-table-grid class="select-none"></div>
                    <div class="mt-1.5 flex items-center justify-between gap-3 text-[11px] text-slate-500">
                        <span data-rich-table-label>0 × 0</span>
                        <button type="button" data-rich-table-custom class="text-primary-600 hover:underline">Custom…</button>
                    </div>
                </div>
            </div>

            <span class="mx-1 h-5 w-px bg-slate-200"></span>

            {{-- Equation popover --}}
            <div class="relative" data-rich-pop-wrap data-pop="eq">
                <button type="button" data-pop-toggle class="rich-btn px-2.5 text-xs"
                    title="Sisipkan rumus"><i data-lucide="sigma" class="mr-1 h-3.5 w-3.5"></i>Rumus</button>
                <div data-pop-body
                    class="rich-pop rich-eq-pop absolute right-0 top-full z-50 mt-1 hidden w-[360px] max-h-[420px] overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-2xl">
                    @foreach ($eqGroups as $groupName => $items)
                        <div class="border-b border-slate-100 px-3 py-2.5 last:border-b-0">
                            <div class="mb-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ $groupName }}</div>
                            <div class="grid grid-cols-3 gap-1.5">
                                @foreach ($items as $tpl)
                                    <button type="button" class="rich-eq-tpl"
                                        data-rich-insert-html="{{ $tpl['html'] }}">
                                        <span class="rich-eq-preview">{!! $tpl['preview'] !!}</span>
                                        <span class="rich-eq-caption">{{ $tpl['caption'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    @foreach ($eqSymbolGroups as $groupName => $syms)
                        <div class="border-b border-slate-100 px-3 py-2.5 last:border-b-0">
                            <div class="mb-1.5 text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ $groupName }}</div>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($syms as $sym)
                                    <button type="button" class="rich-eq-sym" data-rich-insert-html="{{ $sym }}">{{ $sym }}</button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <textarea id="{{ $editorId }}_input" name="{{ $name }}" class="hidden" {{ !empty($required) ? 'required' : '' }}>{{ $editorValue }}</textarea>
        <div id="{{ $editorId }}" contenteditable="true" data-rich-surface
            data-target="{{ $editorId }}_input"
            class="rich-content w-full rounded-b-xl px-4 py-3 text-base leading-8 text-slate-800 outline-none"
            style="min-height: {{ $editorRows * 32 }}px;"
            data-placeholder="{{ $placeholder ?? '' }}"></div>

        {{-- Floating mini toolbar for table cell --}}
        <div data-rich-table-tools
            class="absolute z-40 hidden -translate-y-full -mt-1 items-center gap-0.5 rounded-lg border border-slate-200 bg-white p-1 shadow-lg">
            <button type="button" data-tt="row-above" class="rich-tt-btn" title="Tambah baris di atas">
                <span class="rich-tt-ico">↑</span><span class="rich-tt-lbl">Baris</span>
            </button>
            <button type="button" data-tt="row-below" class="rich-tt-btn" title="Tambah baris di bawah">
                <span class="rich-tt-ico">↓</span><span class="rich-tt-lbl">Baris</span>
            </button>
            <button type="button" data-tt="col-left" class="rich-tt-btn" title="Tambah kolom di kiri">
                <span class="rich-tt-ico">←</span><span class="rich-tt-lbl">Kolom</span>
            </button>
            <button type="button" data-tt="col-right" class="rich-tt-btn" title="Tambah kolom di kanan">
                <span class="rich-tt-ico">→</span><span class="rich-tt-lbl">Kolom</span>
            </button>
            <span class="mx-0.5 h-5 w-px bg-slate-200"></span>
            <button type="button" data-tt="del-row" class="rich-tt-btn rich-tt-danger" title="Hapus baris ini">
                <span class="rich-tt-ico">−</span><span class="rich-tt-lbl">Baris</span>
            </button>
            <button type="button" data-tt="del-col" class="rich-tt-btn rich-tt-danger" title="Hapus kolom ini">
                <span class="rich-tt-ico">−</span><span class="rich-tt-lbl">Kolom</span>
            </button>
            <button type="button" data-tt="del-table" class="rich-tt-btn rich-tt-danger" title="Hapus seluruh tabel">
                <span class="rich-tt-ico">×</span><span class="rich-tt-lbl">Tabel</span>
            </button>
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .rich-btn {
                display: inline-flex; min-height: 1.875rem; min-width: 1.875rem;
                align-items: center; justify-content: center;
                border-radius: 0.5rem; padding: 0.25rem 0.5rem;
                font-size: 0.8125rem; font-weight: 600;
                color: rgb(71 85 105); background: transparent;
                transition: background-color .15s, color .15s; cursor: pointer;
            }
            .rich-btn:hover { background: white; color: rgb(15 23 42); box-shadow: 0 1px 2px rgba(15,23,42,.04); }

            [data-rich-surface][data-empty="1"]::before {
                content: attr(data-placeholder); color: rgb(148 163 184); pointer-events: none;
            }

            .rich-content p { margin: .35rem 0; }
            .rich-content ul, .rich-content ol { margin: .5rem 0; padding-left: 1.5rem; }
            .rich-content ul { list-style: disc; }
            .rich-content ol { list-style: decimal; }

            .rich-content table {
                width: 100%; border-collapse: separate; border-spacing: 0;
                margin: .75rem 0; border: 1px solid rgb(226 232 240);
                border-radius: .5rem; overflow: hidden; font-size: .875rem;
            }
            .rich-content table th { background: rgb(248 250 252); font-weight: 600; color: rgb(15 23 42); }
            .rich-content table td, .rich-content table th {
                border-right: 1px solid rgb(226 232 240); border-bottom: 1px solid rgb(226 232 240);
                padding: .5rem .625rem; vertical-align: top; text-align: left; min-width: 60px;
            }
            .rich-content table td:last-child, .rich-content table th:last-child { border-right: 0; }
            .rich-content table tr:last-child td { border-bottom: 0; }
            .rich-content a { color: rgb(234 88 12); text-decoration: underline; }

            /* Table grid picker */
            .rich-tg-cell { width: 18px; height: 18px; border: 1px solid rgb(203 213 225); background: white; border-radius: 2px; }
            .rich-tg-row { display: flex; gap: 2px; margin-bottom: 2px; }
            .rich-tg-cell.is-on { background: rgb(254 215 170); border-color: rgb(234 88 12); }

            /* Floating table cell toolbar */
            [data-rich-table-tools] { display: none; }
            [data-rich-table-tools].is-open { display: inline-flex; }
            .rich-tt-btn {
                display: inline-flex; align-items: center; gap: .15rem;
                height: 1.875rem; padding: 0 .5rem;
                border-radius: .375rem; color: rgb(71 85 105); background: transparent;
                font-size: .75rem; font-weight: 600; cursor: pointer;
                transition: background-color .15s, color .15s;
            }
            .rich-tt-btn:hover { background: rgb(255 247 237); color: rgb(194 65 12); }
            .rich-tt-btn.rich-tt-danger:hover { background: rgb(254 226 226); color: rgb(185 28 28); }
            .rich-tt-ico { font-size: .95rem; line-height: 1; font-weight: 700; }
            .rich-tt-lbl { font-size: .6875rem; }

            .rich-content td.is-active-cell, .rich-content th.is-active-cell {
                box-shadow: inset 0 0 0 2px rgb(234 88 12);
            }

            /* Equation popover tiles */
            .rich-eq-pop::-webkit-scrollbar { width: 6px; }
            .rich-eq-pop::-webkit-scrollbar-thumb { background: rgb(226 232 240); border-radius: 3px; }

            .rich-eq-tpl {
                display: flex; flex-direction: column; align-items: center; justify-content: space-between;
                gap: .25rem; height: 64px; padding: .5rem .25rem;
                border: 1px solid rgb(226 232 240); border-radius: .5rem; background: white;
                color: rgb(15 23 42); cursor: pointer;
                transition: border-color .15s, background-color .15s;
            }
            .rich-eq-tpl:hover { border-color: rgb(234 88 12); background: rgb(255 247 237); }
            .rich-eq-preview {
                display: inline-flex; align-items: center; justify-content: center;
                flex: 1; font-size: 1rem; color: rgb(15 23 42); min-height: 28px;
            }
            .rich-eq-caption {
                font-size: 10px; font-weight: 600; color: rgb(100 116 139);
                text-transform: uppercase; letter-spacing: .04em;
            }
            .rich-eq-sym {
                min-width: 2rem; height: 2rem; padding: 0 .375rem;
                border: 1px solid rgb(226 232 240); border-radius: .375rem; background: white;
                font-size: .9375rem; color: rgb(15 23 42); cursor: pointer;
                transition: border-color .15s, background-color .15s;
            }
            .rich-eq-sym:hover { border-color: rgb(234 88 12); background: rgb(255 247 237); }

            /* === Equation rendering === */
            .rmath, .rich-content .rmath {
                display: inline-flex; align-items: center; vertical-align: middle;
                margin: 0 2px; font-family: 'Cambria Math','STIX Two Math','Times New Roman',serif;
                font-style: italic; line-height: 1.1;
            }
            .rich-content .rmath { font-size: 1.25em; }
            .rmath sub, .rmath sup { font-style: italic; }

            /* Simple editable slots (sub/sup/parens/abs/matrix) — orange only when focused */
            .rich-content .rmath-slot {
                display: inline-block; min-width: .9em; padding: 0 .2em;
                background: transparent;
                border: 1px dashed transparent;
                border-radius: 4px;
                color: inherit;
                transition: background-color .12s, border-color .12s;
            }
            .rich-content .rmath-slot:empty::before {
                content: '\\25A1'; color: rgb(203 213 225); font-style: normal;
            }
            .rich-content .rmath-slot:focus {
                outline: none;
                background: rgb(255 247 237);
                border: 1px dashed rgb(234 88 12);
                color: rgb(154 52 18);
            }
            /* Structural slots (fraction num/den, root rad/idx, sigma up/down) — only highlighted on focus */
            .rich-content .rmath-num,
            .rich-content .rmath-den,
            .rich-content .rmath-rad,
            .rich-content .rmath-idx,
            .rich-content .rmath-up,
            .rich-content .rmath-down {
                background: transparent;
                border-radius: 3px;
                transition: background-color .12s, box-shadow .12s;
            }
            .rich-content .rmath-num:focus,
            .rich-content .rmath-den:focus,
            .rich-content .rmath-rad:focus,
            .rich-content .rmath-idx:focus,
            .rich-content .rmath-up:focus,
            .rich-content .rmath-down:focus {
                outline: none;
                background: rgb(255 247 237);
                color: rgb(154 52 18);
                box-shadow: inset 0 0 0 1px rgb(234 88 12);
            }

            /* Fraction */
            .rmath-frac {
                display: inline-flex !important; flex-direction: column; align-items: center;
                vertical-align: middle; margin: 0 2px; line-height: 1.05;
            }
            .rmath-frac .rmath-num {
                border-bottom: 1px solid currentColor; padding: 0 .25em .05em;
                min-width: .8em; text-align: center;
            }
            .rmath-frac .rmath-den {
                padding: .05em .25em 0; min-width: .8em; text-align: center;
            }

            /* Square root */
            .rmath-sqrt, .rmath-nsqrt {
                display: inline-flex !important; align-items: flex-start;
            }
            .rmath-sqrt .rmath-sym, .rmath-nsqrt .rmath-sym {
                font-size: 1.15em; line-height: 1; margin-right: -2px;
            }
            .rmath-sqrt .rmath-rad, .rmath-nsqrt .rmath-rad {
                border-top: 1px solid currentColor; padding: 1px .25em 0; min-width: .8em;
            }
            .rmath-nsqrt .rmath-idx {
                font-size: .65em; align-self: flex-start; margin-right: -3px; margin-top: -2px;
                min-width: .5em;
            }

            /* Big operators */
            .rmath-bigop {
                display: inline-flex !important; flex-direction: column; align-items: center;
                vertical-align: middle; line-height: 1; margin: 0 2px;
            }
            .rmath-bigop .rmath-op { font-size: 1.4em; line-height: 1; font-style: normal; }
            .rmath-bigop .rmath-op.rmath-int { font-size: 1.6em; font-style: italic; }
            .rmath-bigop .rmath-op.rmath-lim { font-size: .95em; }
            .rmath-bigop .rmath-up, .rmath-bigop .rmath-down {
                font-size: .6em; min-width: 1em; text-align: center;
            }

            .rmath-matrix { font-size: 1.05em; }
            .rmath-matrix .rmath-mwrap { display: inline-flex; gap: .5em; padding: 0 .25em; }
        </style>
    @endpush

    @push('scripts')
        <script>
            (function () {
                const MAX_R = 8, MAX_C = 8;

                function syncRichEditor(editor) {
                    const surface = editor.querySelector('[data-rich-surface]');
                    const input = document.getElementById(surface.dataset.target);
                    input.value = surface.innerHTML.trim();
                    const isEmpty = !surface.textContent.trim() && !surface.querySelector('img,table,ul,ol,.rmath');
                    surface.dataset.empty = isEmpty ? '1' : '0';
                }

                function insertHtmlAtCursor(surface, html) {
                    surface.focus();
                    const sel = window.getSelection();
                    if (!sel.rangeCount || !surface.contains(sel.anchorNode)) {
                        const range = document.createRange();
                        range.selectNodeContents(surface);
                        range.collapse(false);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }
                    document.execCommand('insertHTML', false, html);
                }

                function buildTableHtml(rows, cols, withHeader = true) {
                    let html = '<table><tbody>';
                    for (let r = 0; r < rows; r++) {
                        html += '<tr>';
                        for (let c = 0; c < cols; c++) {
                            const tag = (withHeader && r === 0) ? 'th' : 'td';
                            const content = (withHeader && r === 0) ? `Kolom ${c + 1}` : '<br>';
                            html += `<${tag}>${content}</${tag}>`;
                        }
                        html += '</tr>';
                    }
                    html += '</tbody></table><p><br></p>';
                    return html;
                }

                function buildPickerGrid(wrap, label, onPick) {
                    wrap.innerHTML = '';
                    for (let r = 0; r < MAX_R; r++) {
                        const row = document.createElement('div');
                        row.className = 'rich-tg-row';
                        for (let c = 0; c < MAX_C; c++) {
                            const cell = document.createElement('div');
                            cell.className = 'rich-tg-cell';
                            cell.dataset.r = r; cell.dataset.c = c;
                            cell.addEventListener('mouseenter', () => {
                                wrap.querySelectorAll('.rich-tg-cell').forEach(el => {
                                    const on = (+el.dataset.r <= r) && (+el.dataset.c <= c);
                                    el.classList.toggle('is-on', on);
                                });
                                label.textContent = `${r + 1} × ${c + 1}`;
                            });
                            cell.addEventListener('click', () => onPick(r + 1, c + 1));
                            row.appendChild(cell);
                        }
                        wrap.appendChild(row);
                    }
                    wrap.addEventListener('mouseleave', () => {
                        wrap.querySelectorAll('.rich-tg-cell').forEach(el => el.classList.remove('is-on'));
                        label.textContent = '0 × 0';
                    });
                }

                function closeAllPops(except) {
                    document.querySelectorAll('[data-rich-pop-wrap] [data-pop-body]').forEach(b => {
                        if (b !== except) b.classList.add('hidden');
                    });
                }

                document.querySelectorAll('[data-rich-editor]').forEach((editor) => {
                    const surface = editor.querySelector('[data-rich-surface]');
                    const input = document.getElementById(surface.dataset.target);
                    surface.innerHTML = input.value || '';

                    let savedRange = null;
                    const saveSel = () => {
                        const sel = window.getSelection();
                        if (sel.rangeCount && surface.contains(sel.anchorNode)) {
                            savedRange = sel.getRangeAt(0).cloneRange();
                        }
                    };
                    const restoreSel = () => {
                        if (!savedRange) return;
                        const sel = window.getSelection();
                        sel.removeAllRanges();
                        sel.addRange(savedRange);
                    };
                    surface.addEventListener('mouseup', saveSel);
                    surface.addEventListener('keyup', saveSel);
                    surface.addEventListener('blur', saveSel);

                    editor.querySelectorAll('[data-rich-command]').forEach((button) => {
                        button.addEventListener('mousedown', (e) => e.preventDefault());
                        button.addEventListener('click', () => {
                            surface.focus(); restoreSel();
                            document.execCommand(button.dataset.richCommand, false, null);
                            syncRichEditor(editor);
                        });
                    });

                    editor.querySelectorAll('[data-rich-pop-wrap]').forEach((wrap) => {
                        const toggle = wrap.querySelector('[data-pop-toggle]');
                        const body = wrap.querySelector('[data-pop-body]');
                        toggle.addEventListener('mousedown', (e) => e.preventDefault());
                        toggle.addEventListener('click', (e) => {
                            e.stopPropagation();
                            const willOpen = body.classList.contains('hidden');
                            closeAllPops(willOpen ? body : null);
                            body.classList.toggle('hidden', !willOpen);
                        });
                    });
                    document.addEventListener('click', (e) => {
                        editor.querySelectorAll('[data-rich-pop-wrap]').forEach((wrap) => {
                            if (!wrap.contains(e.target)) wrap.querySelector('[data-pop-body]').classList.add('hidden');
                        });
                    });

                    editor.querySelectorAll('[data-rich-insert-html]').forEach((btn) => {
                        btn.addEventListener('mousedown', (e) => e.preventDefault());
                        btn.addEventListener('click', (e) => {
                            e.preventDefault();
                            restoreSel();
                            insertHtmlAtCursor(surface, btn.dataset.richInsertHtml);
                            syncRichEditor(editor);
                            saveSel();
                            closeAllPops(null);
                        });
                    });

                    const tableWrap = editor.querySelector('[data-pop="table"]');
                    if (tableWrap) {
                        const grid = tableWrap.querySelector('[data-rich-table-grid]');
                        const label = tableWrap.querySelector('[data-rich-table-label]');
                        const customBtn = tableWrap.querySelector('[data-rich-table-custom]');
                        const body = tableWrap.querySelector('[data-pop-body]');

                        const insertTable = (r, c) => {
                            restoreSel();
                            insertHtmlAtCursor(surface, buildTableHtml(r, c, true));
                            syncRichEditor(editor);
                            body.classList.add('hidden');
                        };
                        buildPickerGrid(grid, label, insertTable);
                        customBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            const r = parseInt(prompt('Jumlah baris (termasuk header)?', '3'), 10);
                            const c = parseInt(prompt('Jumlah kolom?', '3'), 10);
                            if (r > 0 && c > 0) insertTable(r, c);
                        });
                    }

                    surface.addEventListener('input', () => syncRichEditor(editor));
                    surface.addEventListener('blur', () => syncRichEditor(editor));
                    surface.closest('form')?.addEventListener('submit', () => syncRichEditor(editor));

                    // === Table cell floating toolbar ===
                    const cellTools = editor.querySelector('[data-rich-table-tools]');
                    let activeCell = null;

                    function clearActiveCell() {
                        if (activeCell) activeCell.classList.remove('is-active-cell');
                        activeCell = null;
                        cellTools.classList.remove('is-open');
                    }

                    function positionCellTools(cell) {
                        const wrapRect = editor.querySelector('.rich-content').parentElement.getBoundingClientRect();
                        const cellRect = cell.getBoundingClientRect();
                        // Position above the cell, relative to the editor wrapper
                        const left = cellRect.left - wrapRect.left;
                        const top = cellRect.top - wrapRect.top;
                        cellTools.style.left = left + 'px';
                        cellTools.style.top = top + 'px';
                    }

                    function setActiveCell(cell) {
                        if (activeCell === cell) return;
                        if (activeCell) activeCell.classList.remove('is-active-cell');
                        activeCell = cell;
                        cell.classList.add('is-active-cell');
                        positionCellTools(cell);
                        cellTools.classList.add('is-open');
                        if (window.lucide?.createIcons) window.lucide.createIcons();
                    }

                    surface.addEventListener('click', (e) => {
                        const cell = e.target.closest('td, th');
                        if (cell && surface.contains(cell)) setActiveCell(cell);
                        else clearActiveCell();
                    });
                    surface.addEventListener('keyup', () => {
                        const sel = window.getSelection();
                        if (!sel.rangeCount) return;
                        let node = sel.anchorNode;
                        while (node && node !== surface && !(node.nodeType === 1 && (node.tagName === 'TD' || node.tagName === 'TH'))) {
                            node = node.parentNode;
                        }
                        if (node && node !== surface) setActiveCell(node);
                        else clearActiveCell();
                    });
                    document.addEventListener('mousedown', (e) => {
                        if (!editor.contains(e.target)) clearActiveCell();
                    });
                    window.addEventListener('scroll', () => { if (activeCell) positionCellTools(activeCell); }, true);
                    window.addEventListener('resize', () => { if (activeCell) positionCellTools(activeCell); });

                    function newCell(asHeader = false) {
                        const td = document.createElement(asHeader ? 'th' : 'td');
                        td.innerHTML = '<br>';
                        return td;
                    }

                    cellTools.addEventListener('mousedown', (e) => e.preventDefault());
                    cellTools.addEventListener('click', (e) => {
                        const btn = e.target.closest('[data-tt]');
                        if (!btn || !activeCell) return;
                        e.preventDefault();
                        const action = btn.dataset.tt;
                        const row = activeCell.parentElement;
                        const table = activeCell.closest('table');
                        const cellIndex = Array.from(row.children).indexOf(activeCell);
                        const colCount = row.children.length;
                        const rows = Array.from(table.querySelectorAll('tr'));
                        const rowIndex = rows.indexOf(row);

                        switch (action) {
                            case 'row-above':
                            case 'row-below': {
                                const newRow = document.createElement('tr');
                                for (let i = 0; i < colCount; i++) newRow.appendChild(newCell(false));
                                if (action === 'row-above') row.parentNode.insertBefore(newRow, row);
                                else row.parentNode.insertBefore(newRow, row.nextSibling);
                                break;
                            }
                            case 'col-left':
                            case 'col-right': {
                                rows.forEach((r, idx) => {
                                    const ref = r.children[cellIndex];
                                    const isHeaderRow = idx === 0 && r.querySelector('th');
                                    const cell = newCell(isHeaderRow);
                                    if (action === 'col-left') r.insertBefore(cell, ref);
                                    else r.insertBefore(cell, ref ? ref.nextSibling : null);
                                });
                                break;
                            }
                            case 'del-row': {
                                if (rows.length <= 1) { table.remove(); clearActiveCell(); break; }
                                row.remove();
                                clearActiveCell();
                                break;
                            }
                            case 'del-col': {
                                if (colCount <= 1) { table.remove(); clearActiveCell(); break; }
                                rows.forEach((r) => { if (r.children[cellIndex]) r.children[cellIndex].remove(); });
                                clearActiveCell();
                                break;
                            }
                            case 'del-table': {
                                table.remove();
                                clearActiveCell();
                                break;
                            }
                        }
                        syncRichEditor(editor);
                        if (activeCell && document.body.contains(activeCell)) positionCellTools(activeCell);
                    });

                    syncRichEditor(editor);
                });
            })();
        </script>
    @endpush
@endonce
