@props(['name', 'value' => null, 'placeholder' => 'Pilih tanggal & waktu', 'required' => false])
@php
    $current = old($name, $value);
    if ($current instanceof \DateTimeInterface) {
        $current = $current->format('Y-m-d\TH:i');
    }
    $date = null;
    $time = null;
    if ($current) {
        $parts = explode('T', str_replace(' ', 'T', $current));
        $date = $parts[0] ?? null;
        $time = isset($parts[1]) ? substr($parts[1], 0, 5) : null;
    }
    $cid = 'dt_' . md5($name . microtime(true));
@endphp
<div class="grid grid-cols-2 gap-2" data-form-datetime id="{{ $cid }}">
    <input type="hidden" name="{{ $name }}" value="{{ $current }}" data-fdt-input
        @if ($required) required @endif>
    <div data-fdt-date>
        <x-form.datepicker name="{{ $cid }}_date" :value="$date" placeholder="Tanggal" />
    </div>
    <div data-fdt-time>
        <x-form.timepicker name="{{ $cid }}_time" :value="$time" placeholder="Waktu" />
    </div>
</div>

@once
    @push('scripts')
        <script>
            (function() {
                function init(root) {
                    if (root.dataset.fdtInit) return;
                    root.dataset.fdtInit = '1';
                    const input = root.querySelector(':scope > [data-fdt-input]');
                    const dateInput = root.querySelector('[data-fdt-date] [data-fd-input]');
                    const timeInput = root.querySelector('[data-fdt-time] [data-ft-input]');

                    function sync() {
                        const d = dateInput.value,
                            t = timeInput.value;
                        input.value = d ? (d + (t ? 'T' + t : 'T00:00')) : '';
                        input.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                    dateInput.addEventListener('change', sync);
                    timeInput.addEventListener('change', sync);
                }

                function initAll() {
                    document.querySelectorAll('[data-form-datetime]').forEach(init);
                }
                if (document.readyState !== 'loading') setTimeout(initAll, 0);
                else document.addEventListener('DOMContentLoaded', initAll);
            })
            ();
        </script>
    @endpush
@endonce
