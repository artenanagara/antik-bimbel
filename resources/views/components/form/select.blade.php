@props([
    'name',
    'options' => [],
    'selected' => null,
    'placeholder' => 'Pilih...',
    'icon' => null,
    'required' => false,
    'id' => null,
    'disabled' => false,
])
@php
    $cid = $id ?? 'sel_' . md5($name . microtime(true));
    $current = old($name, $selected);
    $currentLabel =
        $current !== null && $current !== '' && array_key_exists($current, $options) ? $options[$current] : null;
@endphp
<div class="relative" data-form-select id="{{ $cid }}">
    <input type="hidden" name="{{ $name }}" value="{{ $current }}" data-fs-input
        @if ($required) required @endif>
    <button type="button" data-fs-trigger @if ($disabled) disabled @endif
        class="w-full flex items-center justify-between gap-2 text-sm text-left border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition disabled:opacity-60 disabled:cursor-not-allowed">
        <span class="flex items-center gap-2 min-w-0">
            @if ($icon)
                <i data-lucide="{{ $icon }}" class="w-4 h-4 text-slate-400 shrink-0"></i>
            @endif
            <span data-fs-label
                class="truncate {{ $currentLabel ? 'text-slate-700' : 'text-slate-400' }}">{{ $currentLabel ?? $placeholder }}</span>
        </span>
        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 shrink-0 transition-transform" data-fs-chevron></i>
    </button>
    <div data-fs-panel
        class="hidden absolute z-30 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
        <div class="max-h-64 overflow-y-auto p-1" role="listbox">
            @foreach ($options as $value => $label)
                <button type="button" data-fs-opt data-value="{{ $value }}"
                    class="w-full flex items-center justify-between gap-2 text-sm text-left px-3 py-2 rounded-lg hover:bg-slate-50 transition {{ (string) $current === (string) $value ? 'bg-primary-50 text-primary-700 font-medium' : 'text-slate-700' }}">
                    <span>{{ $label }}</span>
                    @if ((string) $current === (string) $value)
                        <i data-lucide="check" class="w-4 h-4 text-primary-600"></i>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            (function() {
                function initSelect(root) {
                    if (root.dataset.fsInit) return;
                    root.dataset.fsInit = '1';
                    const trigger = root.querySelector('[data-fs-trigger]');
                    const panel = root.querySelector('[data-fs-panel]');
                    const label = root.querySelector('[data-fs-label]');
                    const chevron = root.querySelector('[data-fs-chevron]');
                    const input = root.querySelector('[data-fs-input]');
                    const opts = root.querySelectorAll('[data-fs-opt]');

                    function close() {
                        panel.classList.add('hidden');
                        chevron.style.transform = '';
                    }

                    function open() {
                        panel.classList.remove('hidden');
                        chevron.style.transform = 'rotate(180deg)';
                    }

                    trigger.addEventListener('click', e => {
                        e.stopPropagation();
                        panel.classList.contains('hidden') ? open() : close();
                    });
                    document.addEventListener('click', e => {
                        if (!root.contains(e.target)) close();
                    });

                    opts.forEach(opt => {
                        opt.addEventListener('click', () => {
                            const v = opt.dataset.value;
                            input.value = v;
                            input.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                            label.textContent = opt.querySelector('span').textContent;
                            label.classList.remove('text-slate-400');
                            label.classList.add('text-slate-700');
                            opts.forEach(o => {
                                o.classList.remove('bg-primary-50', 'text-primary-700',
                                    'font-medium');
                                o.classList.add('text-slate-700');
                                const chk = o.querySelector('[data-lucide="check"]');
                                if (chk) chk.remove();
                            });
                            opt.classList.remove('text-slate-700');
                            opt.classList.add('bg-primary-50', 'text-primary-700', 'font-medium');
                            if (!opt.querySelector('[data-lucide="check"]')) {
                                const i = document.createElement('i');
                                i.setAttribute('data-lucide', 'check');
                                i.className = 'w-4 h-4 text-primary-600';
                                opt.appendChild(i);
                                if (window.lucide) lucide.createIcons();
                            }
                            close();
                        });
                    });
                }

                function initAll() {
                    document.querySelectorAll('[data-form-select]').forEach(initSelect);
                }
                if (document.readyState !== 'loading') initAll();
                else document.addEventListener('DOMContentLoaded', initAll);
            })
            ();
        </script>
    @endpush
@endonce
