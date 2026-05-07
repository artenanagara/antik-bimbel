@extends('layouts.admin')
@section('title', 'Try Out')
@section('page-title', 'Try Out')
@section('header-actions')
    <a href="{{ route('admin.tryouts.create') }}"
        class="inline-flex items-center gap-2 bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-soft transition">
        <i data-lucide="plus" class="w-4 h-4"></i> Buat Try Out
    </a>
@endsection

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

    {{-- Bulk action bar --}}
    <div id="bulk-bar" class="hidden px-5 py-3 border-b border-primary-100 bg-primary-50 flex items-center gap-3">
        <span class="text-sm font-semibold text-primary-700"><span id="bulk-count">0</span> dipilih</span>
        <form action="{{ route('admin.tryouts.bulk-action') }}" method="POST" id="bulk-form" class="ml-auto flex items-center gap-2">
            @csrf
            <input type="hidden" name="action" id="bulk-action-input">
            <div id="bulk-ids"></div>
            <button type="button" onclick="submitBulk('publish')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 text-xs font-semibold hover:bg-emerald-200 transition">
                <i data-lucide="send" class="w-3.5 h-3.5"></i> Publish
            </button>
            <button type="button" onclick="submitBulk('draft')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-300 transition">
                <i data-lucide="file-clock" class="w-3.5 h-3.5"></i> Draft
            </button>
            <button type="button" onclick="submitBulk('close')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-100 text-amber-700 text-xs font-semibold hover:bg-amber-200 transition">
                <i data-lucide="lock" class="w-3.5 h-3.5"></i> Tutup
            </button>
            <button type="button" onclick="submitBulk('delete')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-100 text-red-700 text-xs font-semibold hover:bg-red-200 transition">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="w-10 px-4 py-3">
                        <input type="checkbox" id="select-all" onchange="toggleAll(this)"
                            class="rounded border-slate-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                    </th>
                    <th class="text-left px-4 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">Nama</th>
                    <th class="text-left px-4 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">Tipe</th>
                    <th class="text-right px-4 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">Soal</th>
                    <th class="text-right px-4 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">Durasi</th>
                    <th class="text-left px-4 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-right px-4 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">Peserta</th>
                    <th class="text-right px-4 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($tryouts as $t)
                    <tr class="hover:bg-slate-50/50 transition row-item">
                        <td class="w-10 px-4 py-3">
                            <input type="checkbox" value="{{ $t->id }}" onchange="onRowCheck()"
                                class="row-checkbox rounded border-slate-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-800 max-w-[200px]">
                            <div class="truncate">{{ $t->name }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                {{ $t->type === 'simulation' ? 'bg-blue-100 text-blue-700' : 'bg-violet-100 text-violet-700' }}">
                                {{ $t->type === 'simulation' ? 'Simulasi' : 'Sub Test' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-slate-600">{{ $t->questions_count }}</td>
                        <td class="px-4 py-3 text-right text-slate-600">{{ $t->duration_minutes }}'</td>
                        <td class="px-4 py-3">
                            @php $statusColors = ['draft'=>'bg-slate-100 text-slate-600','published'=>'bg-green-100 text-green-700','closed'=>'bg-red-100 text-red-700'] @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$t->status] }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-slate-600">{{ $t->student_tryouts_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-0.5">
                                <a href="{{ route('admin.tryouts.show', $t) }}"
                                    class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Kelola">
                                    <i data-lucide="settings-2" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.tryouts.edit', $t) }}"
                                    class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.tryouts.destroy', $t) }}" method="POST"
                                    onsubmit="return confirm('Hapus try out ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-slate-400">Belum ada try out.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($tryouts->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $tryouts->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function toggleAll(cb) {
        document.querySelectorAll('.row-checkbox').forEach(c => c.checked = cb.checked);
        onRowCheck();
    }

    function onRowCheck() {
        const checked = [...document.querySelectorAll('.row-checkbox:checked')];
        const all = document.querySelectorAll('.row-checkbox');
        document.getElementById('select-all').indeterminate = checked.length > 0 && checked.length < all.length;
        document.getElementById('select-all').checked = checked.length === all.length && all.length > 0;
        document.getElementById('bulk-count').textContent = checked.length;
        document.getElementById('bulk-bar').classList.toggle('hidden', checked.length === 0);
    }

    function submitBulk(action) {
        const checked = [...document.querySelectorAll('.row-checkbox:checked')];
        if (!checked.length) return;
        if (action === 'delete' && !confirm('Hapus ' + checked.length + ' try out terpilih?')) return;
        document.getElementById('bulk-action-input').value = action;
        const container = document.getElementById('bulk-ids');
        container.innerHTML = '';
        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
            container.appendChild(inp);
        });
        document.getElementById('bulk-form').submit();
    }
</script>
@endpush
