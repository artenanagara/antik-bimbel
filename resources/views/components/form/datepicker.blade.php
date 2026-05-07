@props([
    'name',
    'value' => null,
    'placeholder' => 'Pilih tanggal',
    'min' => null,
    'max' => null,
    'required' => false,
])
@php
    $current = old($name, $value);
    // Normalize to Y-m-d
    if ($current instanceof \DateTimeInterface) {
        $current = $current->format('Y-m-d');
    }
    $cid = 'dp_' . md5($name . microtime(true));
@endphp
<div class="relative" data-form-date id="{{ $cid }}" data-min="{{ $min }}" data-max="{{ $max }}">
    <input type="hidden" name="{{ $name }}" value="{{ $current }}" data-fd-input
        @if ($required) required @endif>
    <button type="button" data-fd-trigger data-placeholder="{{ $placeholder }}"
        class="w-full flex items-center justify-between gap-2 text-sm text-left border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition">
        <span class="flex items-center gap-2 min-w-0">
            <i data-lucide="calendar" class="w-4 h-4 text-slate-400 shrink-0"></i>
            <span data-fd-label
                class="truncate {{ $current ? 'text-slate-700' : 'text-slate-400' }}">{{ $current ? \Carbon\Carbon::parse($current)->translatedFormat('d M Y') : $placeholder }}</span>
        </span>
        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 shrink-0" data-fd-chevron></i>
    </button>
    <div data-fd-panel
        class="hidden absolute z-40 mt-2 w-72 bg-white border border-slate-200 rounded-2xl shadow-xl p-3">
        <div class="flex items-center justify-between mb-3">
            <button type="button" data-fd-prev class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500"><i
                    data-lucide="chevron-left" class="w-4 h-4"></i></button>
            <div class="flex items-center gap-1.5">
                <button type="button" data-fd-month
                    class="text-sm font-semibold text-slate-800 hover:text-primary-600 px-2 py-1 rounded-lg hover:bg-slate-50"></button>
                <button type="button" data-fd-year
                    class="text-sm font-semibold text-slate-800 hover:text-primary-600 px-2 py-1 rounded-lg hover:bg-slate-50"></button>
            </div>
            <button type="button" data-fd-next class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500"><i
                    data-lucide="chevron-right" class="w-4 h-4"></i></button>
        </div>
        <div class="grid grid-cols-7 gap-1 mb-1">
            @foreach (['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'] as $d)
                <div class="text-[10px] text-center font-semibold text-slate-400 uppercase">{{ $d }}</div>
            @endforeach
        </div>
        <div class="grid grid-cols-7 gap-1" data-fd-grid></div>
        <div data-fd-month-grid class="hidden grid-cols-3 gap-1.5"></div>
        <div data-fd-year-grid class="hidden grid-cols-4 gap-1.5"></div>
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
            <button type="button" data-fd-clear
                class="text-xs font-semibold text-slate-500 hover:text-red-600 px-2 py-1 rounded-lg">Hapus</button>
            <button type="button" data-fd-today
                class="text-xs font-semibold text-primary-600 hover:text-primary-700 px-3 py-1 rounded-lg bg-primary-50 hover:bg-primary-100">Hari
                Ini</button>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            (function() {
                const MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                    'Oktober', 'November', 'Desember'
                ];
                const MONTHS_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

                function pad(n) {
                    return String(n).padStart(2, '0');
                }

                function fmtIso(d) {
                    return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
                }

                function fmtPretty(d) {
                    return d.getDate() + ' ' + MONTHS_SHORT[d.getMonth()] + ' ' + d.getFullYear();
                }

                function parseIso(s) {
                    if (!s) return null;
                    const [y, m, d] = s.split('-').map(Number);
                    return new Date(y, m - 1, d);
                }

                function init(root) {
                    if (root.dataset.fdInit) return;
                    root.dataset.fdInit = '1';
                    const input = root.querySelector('[data-fd-input]');
                    const trigger = root.querySelector('[data-fd-trigger]');
                    const label = root.querySelector('[data-fd-label]');
                    const panel = root.querySelector('[data-fd-panel]');
                    const grid = root.querySelector('[data-fd-grid]');
                    const mGrid = root.querySelector('[data-fd-month-grid]');
                    const yGrid = root.querySelector('[data-fd-year-grid]');
                    const monthBtn = root.querySelector('[data-fd-month]');
                    const yearBtn = root.querySelector('[data-fd-year]');
                    const prev = root.querySelector('[data-fd-prev]');
                    const next = root.querySelector('[data-fd-next]');
                    const todayBtn = root.querySelector('[data-fd-today]');
                    const clearBtn = root.querySelector('[data-fd-clear]');
                    const min = parseIso(root.dataset.min);
                    const max = parseIso(root.dataset.max);

                    let view = parseIso(input.value) || new Date();
                    view.setDate(1);
                    let mode = 'days'; // days | months | years

                    function close() {
                        panel.classList.add('hidden');
                    }

                    function open() {
                        panel.classList.remove('hidden');
                        render();
                    }

                    function setValue(d) {
                        if (!d) {
                            input.value = '';
                            label.textContent = trigger.dataset.placeholder || 'Pilih tanggal';
                            label.classList.remove('text-slate-700');
                            label.classList.add('text-slate-400');
                        } else {
                            input.value = fmtIso(d);
                            label.textContent = fmtPretty(d);
                            label.classList.remove('text-slate-400');
                            label.classList.add('text-slate-700');
                        }
                        input.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }

                    function render() {
                        grid.classList.toggle('hidden', mode !== 'days');
                        mGrid.classList.toggle('grid', mode === 'months');
                        mGrid.classList.toggle('hidden', mode !== 'months');
                        yGrid.classList.toggle('grid', mode === 'years');
                        yGrid.classList.toggle('hidden', mode !== 'years');
                        monthBtn.textContent = MONTHS[view.getMonth()];
                        yearBtn.textContent = view.getFullYear();
                        if (mode === 'days') renderDays();
                        if (mode === 'months') renderMonths();
                        if (mode === 'years') renderYears();
                    }

                    function renderDays() {
                        grid.innerHTML = '';
                        const y = view.getFullYear(),
                            m = view.getMonth();
                        const first = new Date(y, m, 1).getDay();
                        const days = new Date(y, m + 1, 0).getDate();
                        const sel = parseIso(input.value);
                        const today = new Date();
                        for (let i = 0; i < first; i++) grid.appendChild(document.createElement('div'));
                        for (let d = 1; d <= days; d++) {
                            const cur = new Date(y, m, d);
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.textContent = d;
                            const isSel = sel && sel.getTime() === cur.getTime();
                            const isToday = cur.toDateString() === today.toDateString();
                            const disabled = (min && cur < min) || (max && cur > max);
                            btn.disabled = disabled;
                            btn.className = 'h-8 text-sm rounded-lg transition ' +
                                (isSel ?
                                    'bg-gradient-to-br from-primary-600 to-primary-700 text-white font-semibold shadow-soft' :
                                    disabled ? 'text-slate-300 cursor-not-allowed' :
                                    isToday ? 'bg-primary-50 text-primary-700 font-semibold hover:bg-primary-100' :
                                    'text-slate-700 hover:bg-slate-100');
                            btn.addEventListener('click', () => {
                                setValue(cur);
                                close();
                            });
                            grid.appendChild(btn);
                        }
                    }

                    function renderMonths() {
                        mGrid.innerHTML = '';
                        MONTHS_SHORT.forEach((mn, i) => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.textContent = mn;
                            btn.className = 'py-2 text-sm rounded-lg ' + (i === view.getMonth() ?
                                'bg-primary-50 text-primary-700 font-semibold' :
                                'text-slate-700 hover:bg-slate-100');
                            btn.addEventListener('click', () => {
                                view.setMonth(i);
                                mode = 'days';
                                render();
                            });
                            mGrid.appendChild(btn);
                        });
                    }

                    function renderYears() {
                        yGrid.innerHTML = '';
                        const start = view.getFullYear() - 6;
                        for (let i = 0; i < 12; i++) {
                            const y = start + i;
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.textContent = y;
                            btn.className = 'py-2 text-sm rounded-lg ' + (y === view.getFullYear() ?
                                'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-700 hover:bg-slate-100');
                            btn.addEventListener('click', () => {
                                view.setFullYear(y);
                                mode = 'months';
                                render();
                            });
                            yGrid.appendChild(btn);
                        }
                    }

                    trigger.addEventListener('click', e => {
                        e.stopPropagation();
                        panel.classList.contains('hidden') ? open() : close();
                    });
                    document.addEventListener('click', e => {
                        if (!root.contains(e.target)) close();
                    });
                    prev.addEventListener('click', () => {
                        if (mode === 'days') view.setMonth(view.getMonth() - 1);
                        else if (mode === 'months') view.setFullYear(view.getFullYear() - 1);
                        else view.setFullYear(view.getFullYear() - 12);
                        render();
                    });
                    next.addEventListener('click', () => {
                        if (mode === 'days') view.setMonth(view.getMonth() + 1);
                        else if (mode === 'months') view.setFullYear(view.getFullYear() + 1);
                        else view.setFullYear(view.getFullYear() + 12);
                        render();
                    });
                    monthBtn.addEventListener('click', () => {
                        mode = mode === 'months' ? 'days' : 'months';
                        render();
                    });
                    yearBtn.addEventListener('click', () => {
                        mode = mode === 'years' ? 'days' : 'years';
                        render();
                    });
                    todayBtn.addEventListener('click', () => {
                        setValue(new Date());
                        close();
                    });
                    clearBtn.addEventListener('click', () => {
                        setValue(null);
                        close();
                    });
                }

                function initAll() {
                    document.querySelectorAll('[data-form-date]').forEach(init);
                }
                if (document.readyState !== 'loading') initAll();
                else document.addEventListener('DOMContentLoaded', initAll);
            })
            ();
        </script>
    @endpush
@endonce
