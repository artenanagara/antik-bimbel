@extends('layouts.student')
@section('title', 'Riwayat Try Out')
@section('content')
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-slate-800">Riwayat Try Out</h2>

        {{-- Mobile: card list --}}
        <div class="md:hidden space-y-3">
            @forelse($history as $h)
                <div class="bg-white rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-800 text-sm leading-snug wrap-break-word">{{ $h->tryout->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $h->tryout->type === 'simulation' ? 'Simulasi' : 'Sub Test' }}
                                · {{ $h->submitted_at?->diffForHumans() ?? '-' }}
                            </p>
                        </div>
                        @if ($h->result?->pass_overall)
                            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Lulus</span>
                        @elseif($h->result)
                            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Tidak Lulus</span>
                        @else
                            <span class="shrink-0 text-xs text-slate-400">-</span>
                        @endif
                    </div>
                    <div class="grid grid-cols-4 gap-2 text-center">
                        @foreach ([['TWK', $h->result?->twk_score, $h->result?->pass_twk], ['TIU', $h->result?->tiu_score, $h->result?->pass_tiu], ['TKP', $h->result?->tkp_score, $h->result?->pass_tkp], ['Total', $h->result?->total_score, $h->result?->pass_overall]] as $s)
                            <div class="bg-slate-50 rounded-lg py-2">
                                <p class="text-[10px] text-slate-500 uppercase font-medium">{{ $s[0] }}</p>
                                <p class="text-base font-bold mt-0.5 {{ $s[2] ? 'text-green-700' : 'text-slate-800' }}">{{ $s[1] ?? '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                    @if ($h->result)
                        <a href="{{ route('student.results.show', $h) }}"
                            class="mt-3 block w-full text-center text-xs font-semibold text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg py-2 transition-colors">
                            Lihat Detail
                        </a>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400 text-sm">
                    Belum ada riwayat.
                </div>
            @endforelse
            @if ($history->hasPages())
                <div>{{ $history->withQueryString()->links() }}</div>
            @endif
        </div>

        {{-- Desktop: table --}}
        <div class="hidden md:block bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left px-5 py-3 font-medium text-slate-500">Try Out</th>
                            <th class="text-left px-5 py-3 font-medium text-slate-500">Tipe</th>
                            <th class="text-right px-5 py-3 font-medium text-slate-500">TWK</th>
                            <th class="text-right px-5 py-3 font-medium text-slate-500">TIU</th>
                            <th class="text-right px-5 py-3 font-medium text-slate-500">TKP</th>
                            <th class="text-right px-5 py-3 font-medium text-slate-500">Total</th>
                            <th class="text-left px-5 py-3 font-medium text-slate-500">Status</th>
                            <th class="text-left px-5 py-3 font-medium text-slate-500">Waktu</th>
                            <th class="text-right px-5 py-3 font-medium text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($history as $h)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3 font-medium text-slate-800 max-w-[200px] truncate">
                                    {{ $h->tryout->name }}</td>
                                <td class="px-5 py-3 text-slate-500 text-xs">
                                    {{ $h->tryout->type === 'simulation' ? 'Simulasi' : 'Sub Test' }}</td>
                                <td class="px-5 py-3 text-right {{ $h->result?->pass_twk ? 'text-green-700' : 'text-slate-600' }}">
                                    {{ $h->result?->twk_score ?? '-' }}</td>
                                <td class="px-5 py-3 text-right {{ $h->result?->pass_tiu ? 'text-green-700' : 'text-slate-600' }}">
                                    {{ $h->result?->tiu_score ?? '-' }}</td>
                                <td class="px-5 py-3 text-right {{ $h->result?->pass_tkp ? 'text-green-700' : 'text-slate-600' }}">
                                    {{ $h->result?->tkp_score ?? '-' }}</td>
                                <td class="px-5 py-3 text-right font-semibold text-slate-800">
                                    {{ $h->result?->total_score ?? '-' }}</td>
                                <td class="px-5 py-3">
                                    @if ($h->result?->pass_overall)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Lulus</span>
                                    @elseif($h->result)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Tidak Lulus</span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-slate-500 text-xs">{{ $h->submitted_at?->diffForHumans() ?? '-' }}</td>
                                <td class="px-5 py-3 text-right">
                                    @if ($h->result)
                                        <a href="{{ route('student.results.show', $h) }}"
                                            class="text-xs text-primary-600 font-medium hover:underline">Detail</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-10 text-center text-slate-400">Belum ada riwayat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($history->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">{{ $history->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
@endsection
