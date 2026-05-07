@extends('layouts.student')
@section('title', $tryout->name)
@section('content')
    <div class="max-w-2xl space-y-4">
        {{-- Header dengan back inline --}}
        <div class="flex items-start gap-2">
            <a href="{{ route('student.tryouts.index') }}"
                class="shrink-0 -ml-1 mt-0.5 w-9 h-9 rounded-full inline-flex items-center justify-center text-slate-500 hover:bg-white hover:text-slate-800 hover:shadow-soft transition"
                title="Kembali">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div class="min-w-0 flex-1 pt-1">
                <h2 class="text-lg sm:text-xl font-bold text-slate-800 leading-tight break-words">{{ $tryout->name }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Detail try out</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 space-y-4">
            @if ($tryout->description)
                <p class="text-slate-600 text-sm leading-relaxed">{{ $tryout->description }}</p>
            @endif

            <dl class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 text-sm">
                <div class="bg-slate-50 rounded-lg p-3">
                    <dt class="text-slate-500 text-xs">Tipe</dt>
                    <dd class="font-medium text-slate-800 mt-0.5">
                        {{ $tryout->type === 'simulation' ? 'Simulasi SKD' : 'Sub Test ' . $tryout->sub_test }}</dd>
                </div>
                <div class="bg-slate-50 rounded-lg p-3">
                    <dt class="text-slate-500 text-xs">Durasi</dt>
                    <dd class="font-medium text-slate-800 mt-0.5">{{ $tryout->duration_minutes }} menit</dd>
                </div>
                <div class="bg-slate-50 rounded-lg p-3">
                    <dt class="text-slate-500 text-xs">Total Soal</dt>
                    <dd class="font-medium text-slate-800 mt-0.5">
                        {{ $tryout->twk_count + $tryout->tiu_count + $tryout->tkp_count }}</dd>
                </div>
                <div class="bg-slate-50 rounded-lg p-3">
                    <dt class="text-slate-500 text-xs">Pengulangan</dt>
                    <dd class="font-medium text-slate-800 mt-0.5">
                        @php $rl = $tryout->repeat_limit; @endphp
                        {{ $rl === null ? 'Tidak terbatas' : ($rl === 0 ? 'Tidak bisa' : $rl . 'x') }}
                    </dd>
                </div>
            </dl>

            <div class="text-sm text-slate-600 space-y-1">
                <p>Passing Grade: <strong class="text-slate-800">TWK {{ $tryout->pg_twk }}</strong> · <strong
                        class="text-slate-800">TIU {{ $tryout->pg_tiu }}</strong> · <strong class="text-slate-800">TKP
                        {{ $tryout->pg_tkp }}</strong></p>
            </div>

            @if ($inProgress)
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <p class="text-sm text-amber-800 font-medium">Anda memiliki sesi yang belum selesai.</p>
                    <a href="{{ route('student.tryouts.exam', [$tryout, $inProgress]) }}"
                        class="mt-2 inline-flex items-center gap-2 text-sm bg-amber-500 hover:bg-amber-600 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                        <i data-lucide="play" class="w-4 h-4"></i> Lanjutkan
                    </a>
                </div>
            @else
                <form action="{{ route('student.tryouts.start', $tryout) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-semibold px-6 py-2.5 rounded-xl shadow-soft transition">
                        <i data-lucide="play" class="w-4 h-4"></i>
                        {{ $attempts->count() ? 'Mulai Ulang' : 'Mulai Try Out' }}
                    </button>
                </form>
            @endif

            @if ($attempts->where('status', '!=', 'in_progress')->count())
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 mb-2">Riwayat Percobaan</h3>
                    <div class="space-y-2">
                        @foreach ($attempts->where('status', '!=', 'in_progress') as $attempt)
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 bg-slate-50 rounded-lg px-3 sm:px-4 py-2.5 text-sm">
                                <span class="text-slate-600">Percobaan #{{ $attempt->attempt_number }}</span>
                                <span
                                    class="font-semibold text-slate-800">{{ $attempt->result?->total_score ?? '-' }}</span>
                                @if ($attempt->result?->pass_overall)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Lulus</span>
                                @elseif($attempt->result)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Tidak
                                        Lulus</span>
                                @endif
                                @if ($attempt->result)
                                    <a href="{{ route('student.results.show', $attempt) }}"
                                        class="ml-auto text-xs text-primary-600 font-medium hover:underline">Detail</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
