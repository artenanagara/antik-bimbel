@extends('layouts.admin')
@section('title', 'Manajemen Siswa')
@section('page-title', 'Siswa')
@section('page-subtitle', 'Kelola data siswa & akun')
@section('header-actions')
    <a href="{{ route('admin.students.create') }}"
        class="inline-flex items-center gap-2 bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-soft transition">
        <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Siswa
    </a>
@endsection

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

    {{-- Filter --}}
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/40">
        <form method="GET" id="filter-form" class="flex flex-wrap gap-2">
            <div class="relative flex-1 min-w-[200px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama / username..."
                    class="w-full text-sm bg-white border border-slate-200 rounded-xl pl-9 pr-3 py-2 focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition"
                    oninput="debounceSubmit(this.form)">
            </div>
            <div class="w-44">
                <x-form.select name="batch_id" placeholder="Semua Batch" icon="layers"
                    :options="$batches->pluck('name', 'id')->toArray()" :selected="request('batch_id')" />
            </div>
            <div class="w-44">
                <x-form.select name="status" placeholder="Semua Status" icon="flag"
                    :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" :selected="request('status')" />
            </div>
            @if (request()->hasAny(['search', 'batch_id', 'status']))
                <a href="{{ route('admin.students.index') }}"
                    class="inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-slate-800 px-3 py-2 rounded-xl hover:bg-slate-100 transition">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i> Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Bulk action bar --}}
    <div id="bulk-bar" class="hidden px-5 py-3 border-b border-primary-100 bg-primary-50 flex items-center gap-3">
        <span class="text-sm font-semibold text-primary-700">
            <span id="bulk-count">0</span> dipilih
        </span>
        <div class="flex items-center gap-2 ml-auto">
            <form action="{{ route('admin.students.bulk-action') }}" method="POST" id="bulk-form">
                @csrf
                <input type="hidden" name="action" id="bulk-action-input">
                <div id="bulk-ids"></div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="submitBulk('activate')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 text-xs font-semibold hover:bg-emerald-200 transition">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Aktifkan
                    </button>
                    <button type="button" onclick="submitBulk('deactivate')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-300 transition">
                        <i data-lucide="circle-slash" class="w-3.5 h-3.5"></i> Nonaktifkan
                    </button>
                    <button type="button" onclick="submitBulk('delete')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-100 text-red-700 text-xs font-semibold hover:bg-red-200 transition">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                    </button>
                </div>
            </form>
        </div>
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
                    <th class="text-left px-4 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">Username</th>
                    <th class="text-left px-4 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">Batch</th>
                    <th class="text-left px-4 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-right px-4 py-3 font-semibold text-xs text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($students as $student)
                    <tr class="hover:bg-slate-50/50 transition-colors row-item">
                        <td class="w-10 px-4 py-3">
                            <input type="checkbox" value="{{ $student->id }}" onchange="onRowCheck()"
                                class="row-checkbox rounded border-slate-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white text-xs flex items-center justify-center font-semibold shrink-0">
                                    {{ strtoupper(substr($student->full_name, 0, 1)) }}
                                </span>
                                <span class="font-medium text-slate-800">{{ $student->full_name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-600 font-mono text-xs">{{ $student->user->username }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $student->batch->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if ($student->user->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 ring-1 ring-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-0.5">
                                <a href="{{ route('admin.students.show', $student) }}"
                                    class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Detail">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.students.edit', $student) }}"
                                    class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.students.destroy', $student) }}" method="POST"
                                    onsubmit="return confirm('Hapus siswa ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i data-lucide="users" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                            <p class="text-sm text-slate-400">Tidak ada data siswa.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($students->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $students->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Auto-filter: submit form when custom select changes
    document.querySelectorAll('#filter-form [data-fs-input]').forEach(input => {
        input.addEventListener('change', () => document.getElementById('filter-form').submit());
    });

    // Debounce for text search
    let _dt;
    function debounceSubmit(form) {
        clearTimeout(_dt);
        _dt = setTimeout(() => form.submit(), 450);
    }

    // Bulk selection
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
        if (action === 'delete' && !confirm('Hapus ' + checked.length + ' siswa terpilih?')) return;
        const form = document.getElementById('bulk-form');
        document.getElementById('bulk-action-input').value = action;
        const container = document.getElementById('bulk-ids');
        container.innerHTML = '';
        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
            container.appendChild(inp);
        });
        form.submit();
    }
</script>
@endpush
