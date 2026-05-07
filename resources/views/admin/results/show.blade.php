@extends('layouts.admin')
@section('title', 'Detail Hasil')
@section('page-title', 'Detail Hasil')
@section('back')
    <a href="{{ route('admin.results.index') }}"
        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
@endsection
@section('content')
    @php $result = $studentTryout->result; @endphp
    <div class="space-y-6">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([['label' => 'TWK', 'score' => $result->twk_score, 'pass' => $result->pass_twk, 'pg' => $studentTryout->tryout->pg_twk], ['label' => 'TIU', 'score' => $result->tiu_score, 'pass' => $result->pass_tiu, 'pg' => $studentTryout->tryout->pg_tiu], ['label' => 'TKP', 'score' => $result->tkp_score, 'pass' => $result->pass_tkp, 'pg' => $studentTryout->tryout->pg_tkp], ['label' => 'Total', 'score' => $result->total_score, 'pass' => $result->pass_overall, 'pg' => null]] as $s)
                <div class="bg-white rounded-xl border {{ $s['pass'] ? 'border-green-200' : 'border-red-200' }} p-5">
                    <div class="flex items-start justify-between">
                        <p class="text-sm text-slate-500">{{ $s['label'] }}</p>
                        @if ($s['pass'])
                            <span
                                class="text-xs font-medium text-green-700 bg-green-100 px-1.5 py-0.5 rounded-full">Lulus</span>
                        @else
                            <span class="text-xs font-medium text-red-700 bg-red-100 px-1.5 py-0.5 rounded-full">TL</span>
                        @endif
                    </div>
                    <p class="text-3xl font-bold text-slate-800 mt-2">{{ $s['score'] }}</p>
                    @if ($s['pg'])
                        <p class="text-xs text-slate-400 mt-1">PG: {{ $s['pg'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <dl class="grid sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <dt class="text-slate-500">Siswa</dt>
                    <dd class="text-slate-800 font-medium mt-0.5">{{ $studentTryout->student->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Try Out</dt>
                    <dd class="text-slate-800 mt-0.5">{{ $studentTryout->tryout->name }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Percobaan ke</dt>
                    <dd class="text-slate-800 mt-0.5">{{ $studentTryout->attempt_number }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Dikerjakan</dt>
                    <dd class="text-slate-800 mt-0.5">
                        @if ($studentTryout->duration_seconds)
                            {{ gmdate('H:i:s', $studentTryout->duration_seconds) }}
                        @else
                            -
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">Jawaban Siswa</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left px-5 py-3 font-medium text-slate-500">No</th>
                            <th class="text-left px-5 py-3 font-medium text-slate-500">Sub Test</th>
                            <th class="text-left px-5 py-3 font-medium text-slate-500">Soal</th>
                            <th class="text-left px-5 py-3 font-medium text-slate-500">Jawaban</th>
                            <th class="text-right px-5 py-3 font-medium text-slate-500">Skor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($studentTryout->answers as $i => $ans)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3 text-slate-500">{{ $i + 1 }}</td>
                                <td class="px-5 py-3">
                                    <span
                                        class="inline-flex px-1.5 py-0.5 rounded text-xs font-semibold
                                {{ $ans->question->sub_test === 'TWK' ? 'bg-blue-100 text-blue-700' : ($ans->question->sub_test === 'TIU' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700') }}">
                                        {{ $ans->question->sub_test }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-slate-700 max-w-[300px]">
                                    <div class="line-clamp-2 text-xs">{{ strip_tags($ans->question->question_text) }}</div>
                                </td>
                                <td class="px-5 py-3 text-slate-700">
                                    {{ $ans->option?->label ?? '-' }}
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-slate-800">{{ $ans->score }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
