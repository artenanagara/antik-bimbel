@extends('layouts.student')
@section('title', 'Hasil Try Out')
@section('content')
    @php
        $result = $studentTryout->result;
        $questions = $studentTryout->tryout->questions;
        $questionTotals = $questions->count()
            ? $questions->groupBy('sub_test')->map->count()
            : collect([
                'TWK' => $studentTryout->tryout->twk_count,
                'TIU' => $studentTryout->tryout->tiu_count,
                'TKP' => $studentTryout->tryout->tkp_count,
            ]);
        $answeredTotals = $studentTryout->answers
            ->filter(fn($answer) => filled($answer->option_id) && $answer->question)
            ->groupBy(fn($answer) => $answer->question->sub_test)
            ->map->count();
        $totalQuestions = $questions->count() ?: $studentTryout->tryout->total_questions;
    @endphp
    <div class="max-w-3xl space-y-5 sm:space-y-6">
        <div class="flex items-start gap-2">
            <a href="{{ route('student.tryouts.show', $studentTryout->tryout) }}"
                class="shrink-0 -ml-1 mt-0.5 w-9 h-9 rounded-full inline-flex items-center justify-center text-slate-500 hover:bg-white hover:text-slate-800 hover:shadow-soft transition"
                title="Kembali">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div class="min-w-0 flex-1 pt-1">
                <h2 class="text-lg sm:text-xl font-bold text-slate-800 leading-tight break-words">{{ $studentTryout->tryout->name }}</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Percobaan ke-{{ $studentTryout->attempt_number }}</p>
            </div>
        </div>

        {{-- Pass/Fail banner --}}
        @if ($result)
            <div
                class="rounded-xl border-2 p-5 {{ $result->pass_overall ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="{{ $result->pass_overall ? 'check-circle-2' : 'x-circle' }}"
                        class="w-8 h-8 {{ $result->pass_overall ? 'text-green-600' : 'text-red-600' }}"></i>
                    <div>
                        <p class="font-bold text-lg {{ $result->pass_overall ? 'text-green-800' : 'text-red-800' }}">
                            {{ $result->pass_overall ? 'Selamat, Anda LULUS!' : 'Belum Lulus' }}
                        </p>
                        @if (!$result->pass_overall)
                            <p class="text-sm {{ $result->pass_overall ? 'text-green-700' : 'text-red-700' }} mt-0.5">
                                {{ $result->getPassStatusLabel() }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Score cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                @foreach ([['label' => 'TWK', 'score' => $result->twk_score, 'pass' => $result->pass_twk, 'pg' => $studentTryout->tryout->pg_twk, 'answered' => $answeredTotals->get('TWK', 0), 'total' => $questionTotals->get('TWK', 0)], ['label' => 'TIU', 'score' => $result->tiu_score, 'pass' => $result->pass_tiu, 'pg' => $studentTryout->tryout->pg_tiu, 'answered' => $answeredTotals->get('TIU', 0), 'total' => $questionTotals->get('TIU', 0)], ['label' => 'TKP', 'score' => $result->tkp_score, 'pass' => $result->pass_tkp, 'pg' => $studentTryout->tryout->pg_tkp, 'answered' => $answeredTotals->get('TKP', 0), 'total' => $questionTotals->get('TKP', 0)], ['label' => 'Total', 'score' => $result->total_score, 'pass' => $result->pass_overall, 'pg' => null, 'answered' => $answeredTotals->sum(), 'total' => $totalQuestions]] as $s)
                    <div
                        class="bg-white rounded-xl border-2 p-4 {{ $s['pass'] ? 'border-green-200' : 'border-slate-200' }}">
                        <div class="flex items-start justify-between">
                            <p class="text-xs text-slate-500 font-medium">{{ $s['label'] }}</p>
                            @if ($s['pass'])
                                <span
                                    class="text-xs bg-green-100 text-green-700 font-medium px-1.5 py-0.5 rounded-full">✓</span>
                            @else
                                <span
                                    class="text-xs bg-red-100 text-red-600 font-medium px-1.5 py-0.5 rounded-full">✗</span>
                            @endif
                        </div>
                        <p class="text-3xl font-bold text-slate-800 mt-1">{{ $s['score'] }}</p>
                        @if ($s['pg'])
                            <p class="text-xs text-slate-400">PG: {{ $s['pg'] }}</p>
                        @endif
                        <p class="text-xs text-slate-400 mt-0.5">{{ $s['answered'] }}/{{ $s['total'] }} dijawab</p>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
            <a href="{{ route('student.results.discussion', $studentTryout) }}"
                class="inline-flex items-center justify-center gap-2 text-sm bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-semibold px-5 py-2.5 rounded-xl shadow-soft transition">
                <i data-lucide="book-open" class="w-4 h-4"></i> Lihat Pembahasan
            </a>
            <a href="{{ route('student.tryouts.show', $studentTryout->tryout) }}"
                class="inline-flex items-center justify-center gap-2 text-sm border border-slate-300 hover:bg-slate-50 px-5 py-2.5 rounded-xl transition-colors text-slate-700">
                Kembali ke Try Out
            </a>
        </div>
    </div>
@endsection
