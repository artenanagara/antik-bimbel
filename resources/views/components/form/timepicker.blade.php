@props(['name', 'value' => null, 'placeholder' => 'Pilih waktu', 'minuteStep' => 1, 'required' => false])
@php
    $current = old($name, $value);
    if ($current instanceof \DateTimeInterface) {
        $current = $current->format('H:i');
    }
    $cid = 'tp_' . md5($name . microtime(true));
@endphp
<div class="relative" data-form-time id="{{ $cid }}" data-step="{{ $minuteStep }}">
    <input type="hidden" name="{{ $name }}" value="{{ $current }}" data-ft-input
        @if ($required) required @endif>
    <button type="button" data-ft-trigger data-placeholder="{{ $placeholder }}"
        class="w-full flex items-center justify-between gap-2 text-sm text-left border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition">
        <span class="flex items-center gap-2 min-w-0">
            <i data-lucide="clock" class="w-4 h-4 text-slate-400 shrink-0"></i>
            <span data-ft-label
                class="truncate {{ $current ? 'text-slate-700' : 'text-slate-400' }}">{{ $current ?: $placeholder }}</span>
        </span>
        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 shrink-0" data-ft-chevron></i>
    </button>
    <div data-ft-panel
        class="hidden absolute z-40 mt-2 w-48 bg-white border border-slate-200 rounded-2xl shadow-xl p-3">
        <div class="flex items-center justify-center gap-2">
            <div class="flex-1 flex flex-col items-center">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Jam</p>
                <div class="h-32 w-14 overflow-y-auto scrollbar-thin border border-slate-200 rounded-xl" data-ft-hours>
                </div>
            </div>
            <span class="text-2xl font-bold text-slate-300 mt-5">:</span>
            <div class="flex-1 flex flex-col items-center">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Menit</p>
                <div class="h-32 w-14 overflow-y-auto scrollbar-thin border border-slate-200 rounded-xl" data-ft-mins>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
            <button type="button" data-ft-clear
                class="text-xs font-semibold text-slate-500 hover:text-red-600 px-2 py-1 rounded-lg">Hapus</button>
            <button type="button" data-ft-now
                class="text-xs font-semibold text-primary-600 hover:text-primary-700 px-3 py-1 rounded-lg bg-primary-50 hover:bg-primary-100">Sekarang</button>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            (function() {
                function pad(n) {
                    return String(n).padStart(2, '0');
                }

                function init(root) {
                    if (root.dataset.ftInit) return;
                    root.dataset.ftInit = '1';
                    const input = root.querySelector('[data-ft-input]');
                    const trigger = root.querySelector('[data-ft-trigger]');
                    const label = root.querySelector('[data-ft-label]');
                    const panel = root.querySelector('[data-ft-panel]');
                    const hoursWrap = root.querySelector('[data-ft-hours]');
                    const minsWrap = root.querySelector('[data-ft-mins]');
                    const nowBtn = root.querySelector('[data-ft-now]');
                    const clearBtn = root.querySelector('[data-ft-clear]');
                    const step = parseInt(root.dataset.step || '1', 10);
                    let [h, m] = (input.value || ':').split(':').map(v => v === '' ? null : parseInt(v, 10));

                    function buildList(wrap, count, current, onPick, fmt) {
                        wrap.innerHTML = '';
                        for (let i = 0; i < count; i++) {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.textContent = fmt(i);
                            btn.dataset.v = i;
                            btn.className = 'w-full text-sm py-1.5 transition ' +
                                (i === current ?
                                    'bg-gradient-to-br from-primary-600 to-primary-700 text-white font-semibold' :
                                    'text-slate-700 hover:bg-slate-100');
                            btn.addEventListener('click', () => onPick(i));
                            wrap.appendChild(btn);
                        }
                        // scroll selected into view
                        if (current != null) {
                            const sel = wrap.querySelector(`[data-v="${current}"]`);
                            if (sel) sel.scrollIntoView({
                                block: 'center'
                            });
                        }
                    }

                    function commit() {
                        if (h == null || m == null) {
                            input.value = '';
                            label.textContent = trigger.dataset.placeholder || 'Pilih waktu';
                            label.classList.remove('text-slate-700');
                            label.classList.add('text-slate-400');
                        } else {
                            input.value = pad(h) + ':' + pad(m);
                            label.textContent = input.value;
                            label.classList.remove('text-slate-400');
                            label.classList.add('text-slate-700');
                        }
                        input.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }

                    function renderHours() {
                        buildList(hoursWrap, 24, h, v => {
                            h = v;
                            if (m == null) m = 0;
                            commit();
                            renderAll();
                        }, pad);
                    }

                    function renderMins() {
                        minsWrap.innerHTML = '';
                        for (let i = 0; i < 60; i += step) {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.textContent = pad(i);
                            btn.dataset.v = i;
                            btn.className = 'w-full text-sm py-1.5 transition ' +
                                (i === m ? 'bg-gradient-to-br from-primary-600 to-primary-700 text-white font-semibold' :
                                    'text-slate-700 hover:bg-slate-100');
                            btn.addEventListener('click', () => {
                                m = i;
                                if (h == null) h = 0;
                                commit();
                                renderAll();
                            });
                            minsWrap.appendChild(btn);
                        }
                        if (m != null) {
                            const sel = minsWrap.querySelector(`[data-v="${m}"]`);
                            if (sel) sel.scrollIntoView({
                                block: 'center'
                            });
                        }
                    }

                    function renderAll() {
                        renderHours();
                        renderMins();
                    }

                    function close() {
                        panel.classList.add('hidden');
                    }

                    function open() {
                        panel.classList.remove('hidden');
                        renderAll();
                    }

                    trigger.addEventListener('click', e => {
                        e.stopPropagation();
                        panel.classList.contains('hidden') ? open() : close();
                    });
                    document.addEventListener('click', e => {
                        if (!root.contains(e.target)) close();
                    });
                    nowBtn.addEventListener('click', () => {
                        const d = new Date();
                        h = d.getHours();
                        m = Math.floor(d.getMinutes() / step) * step;
                        commit();
                        renderAll();
                    });
                    clearBtn.addEventListener('click', () => {
                        h = null;
                        m = null;
                        commit();
                        close();
                    });
                }

                function initAll() {
                    document.querySelectorAll('[data-form-time]').forEach(init);
                }
                if (document.readyState !== 'loading') initAll();
                else document.addEventListener('DOMContentLoaded', initAll);
            })
            ();
        </script>
    @endpush
@endonce
