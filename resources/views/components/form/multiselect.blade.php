@props([
    'name',
    'options' => [],
    'selected' => [],
    'placeholder' => 'Pilih...',
    'icon' => 'list-checks',
    'allLabel' => 'Pilih Semua',
    'noneLabel' => 'Kosongkan',
    'allText' => null,
])
@php
    $oldKey = rtrim($name, '[]');
    $current = old($oldKey, $selected);
    if (!is_array($current)) {
        $current = [];
    }
    $current = array_map('strval', $current);
    $cid = 'ms_' . md5($name . microtime(true));
@endphp
<div class="relative" data-form-multi id="{{ $cid }}">
    <button type="button" data-fm-trigger
        class="w-full flex items-center justify-between gap-2 text-sm text-left border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition">
        <span class="flex items-center gap-2 min-w-0">
            @if ($icon)
                <i data-lucide="{{ $icon }}" class="w-4 h-4 text-slate-400 shrink-0"></i>
            @endif
            <span data-fm-label class="truncate text-slate-400">{{ $placeholder }}</span>
        </span>
        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 shrink-0 transition-transform" data-fm-chevron></i>
    </button>
    <div data-fm-panel data-all-text="{{ $allText ?? 'Semua' }}"
        class="hidden absolute z-30 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
        <div class="p-2 border-b border-slate-100 flex items-center gap-2">
            <button type="button" data-fm-all
                class="text-xs font-semibold text-primary-600 hover:text-primary-700 px-2 py-1 rounded-lg hover:bg-primary-50">{{ $allLabel }}</button>
            <button type="button" data-fm-none
                class="text-xs font-semibold text-slate-500 hover:text-slate-700 px-2 py-1 rounded-lg hover:bg-slate-50">{{ $noneLabel }}</button>
        </div>
        <div class="max-h-56 overflow-y-auto p-1">
            @forelse ($options as $value => $label)
                <label
                    class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg hover:bg-slate-50 cursor-pointer text-sm">
                    <input type="checkbox" name="{{ $name }}" value="{{ $value }}"
                        {{ in_array((string) $value, $current, true) ? 'checked' : '' }} data-fm-cb
                        class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    <span class="text-slate-700">{{ $label }}</span>
                </label>
            @empty
                <p class="text-xs text-slate-400 px-2.5 py-2">Tidak ada pilihan.</p>
            @endforelse
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            (function() {
                function init(root) {
                    if (root.dataset.fmInit) return;
                    root.dataset.fmInit = '1';
                    const trigger = root.querySelector('[data-fm-trigger]');
                    const panel = root.querySelector('[data-fm-panel]');
                    const label = root.querySelector('[data-fm-label]');
                    const chevron = root.querySelector('[data-fm-chevron]');
                    const cbs = root.querySelectorAll('[data-fm-cb]');
                    const allBtn = root.querySelector('[data-fm-all]');
                    const noneBtn = root.querySelector('[data-fm-none]');
                    const allText = panel.dataset.allText || 'Semua';

                    function refresh() {
                        const checked = Array.from(cbs).filter(c => c.checked);
                        if (checked.length === 0) {
                            label.textContent = 'Tidak ada dipilih';
                            label.className = 'truncate text-slate-400';
                        } else if (checked.length === cbs.length) {
                            label.textContent = `${allText} (${cbs.length})`;
                            label.className = 'truncate text-slate-700';
                        } else if (checked.length === 1) {
                            label.textContent = checked[0].parentElement.querySelector('span').textContent;
                            label.className = 'truncate text-slate-700';
                        } else {
                            label.textContent = `${checked.length} dipilih`;
                            label.className = 'truncate text-slate-700';
                        }
                    }

                    function toggle(open) {
                        const willOpen = open ?? panel.classList.contains('hidden');
                        panel.classList.toggle('hidden', !willOpen);
                        chevron.style.transform = willOpen ? 'rotate(180deg)' : '';
                    }
                    trigger.addEventListener('click', e => {
                        e.stopPropagation();
                        toggle();
                    });
                    document.addEventListener('click', e => {
                        if (!root.contains(e.target)) toggle(false);
                    });
                    cbs.forEach(c => c.addEventListener('change', refresh));
                    allBtn?.addEventListener('click', () => {
                        cbs.forEach(c => c.checked = true);
                        refresh();
                    });
                    noneBtn?.addEventListener('click', () => {
                        cbs.forEach(c => c.checked = false);
                        refresh();
                    });
                    refresh();
                }

                function initAll() {
                    document.querySelectorAll('[data-form-multi]').forEach(init);
                }
                if (document.readyState !== 'loading') initAll();
                else document.addEventListener('DOMContentLoaded', initAll);
            })
            ();
        </script>
    @endpush
@endonce
