@extends('layouts.admin')
@section('title', 'Hasil Try Out')
@section('page-title', 'Hasil Try Out')
@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <form method="GET" class="flex flex-wrap gap-2">
                <div class="w-48">
                    <x-form.select name="tryout_id" placeholder="Semua Try Out" icon="file-text" :options="$tryouts->pluck('name', 'id')->toArray()"
                        :selected="request('tryout_id')" />
                </div>
                <div class="w-44">
                    <x-form.select name="batch_id" placeholder="Semua Batch" icon="layers" :options="$batches->pluck('name', 'id')->toArray()"
                        :selected="request('batch_id')" />
                </div>
                <div class="w-44">
                    <x-form.select name="pass" placeholder="Semua Status" icon="flag" :options="['1' => 'Lulus', '0' => 'Tidak Lulus']"
                        :selected="request('pass')" />
                </div>
                <button type="submit" class="text-sm bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-lg">Filter</button>
            </form>
            @if (request('tryout_id'))
                <a href="{{ route('admin.results.export', ['tryout_id' => request('tryout_id')]) }}"
                    class="inline-flex items-center gap-2 text-sm border border-slate-300 hover:bg-slate-50 px-3 py-2 rounded-lg">
                    <i data-lucide="download" class="w-4 h-4"></i> Export Excel
                </a>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="text-left px-5 py-3 font-medium text-slate-500">Siswa</th>
                        <th class="text-left px-5 py-3 font-medium text-slate-500">Try Out</th>
                        <th class="text-right px-5 py-3 font-medium text-slate-500">TWK</th>
                        <th class="text-right px-5 py-3 font-medium text-slate-500">TIU</th>
                        <th class="text-right px-5 py-3 font-medium text-slate-500">TKP</th>
                        <th class="text-right px-5 py-3 font-medium text-slate-500">Total</th>
                        <th class="text-left px-5 py-3 font-medium text-slate-500">Status</th>
                        <th class="text-right px-5 py-3 font-medium text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($results as $r)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $r->student->full_name }}</td>
                            <td class="px-5 py-3 text-slate-600 max-w-[200px] truncate">{{ $r->tryout->name }}</td>
                            <td class="px-5 py-3 text-right {{ $r->pass_twk ? 'text-green-700' : 'text-red-600' }}">
                                {{ $r->twk_score }}</td>
                            <td class="px-5 py-3 text-right {{ $r->pass_tiu ? 'text-green-700' : 'text-red-600' }}">
                                {{ $r->tiu_score }}</td>
                            <td class="px-5 py-3 text-right {{ $r->pass_tkp ? 'text-green-700' : 'text-red-600' }}">
                                {{ $r->tkp_score }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-slate-800">{{ $r->total_score }}</td>
                            <td class="px-5 py-3">
                                @if ($r->pass_overall)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Lulus</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Tidak
                                        Lulus</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.results.show', $r->studentTryout) }}"
                                    class="text-xs text-primary-600 font-medium hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-slate-400">Belum ada hasil.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($results->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $results->withQueryString()->links() }}</div>
        @endif
    </div>
@endsection
