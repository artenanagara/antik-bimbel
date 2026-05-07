@extends('layouts.student')
@section('title', 'Daftar Try Out')
@section('content')
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-slate-800">Try Out Tersedia</h2>
        @if ($tryouts->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                @foreach ($tryouts as $to)
                    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 hover:border-primary-300 hover:shadow-soft transition-colors">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="font-semibold text-slate-800 text-sm leading-snug min-w-0">{{ $to->name }}</h3>
                            <span
                                class="shrink-0 inline-flex px-2 py-0.5 rounded text-xs font-medium bg-primary-50 text-primary-700">
                                {{ $to->type === 'simulation' ? 'Simulasi' : 'Sub Test' }}
                            </span>
                        </div>
                        @if ($to->description)
                            <p class="text-xs text-slate-500 line-clamp-2">{{ $to->description }}</p>
                        @endif
                        <div class="flex items-center gap-3 mt-3 text-xs text-slate-500">
                            <span>{{ $to->duration_minutes }} menit</span>
                            <span>·</span>
                            <span>{{ $to->questions_count }} soal</span>
                        </div>
                        <a href="{{ route('student.tryouts.show', $to) }}"
                            class="mt-4 block w-full text-center text-sm bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-semibold py-2.5 rounded-xl shadow-soft transition">
                            Lihat Detail
                        </a>
                    </div>
                @endforeach
            </div>
            {{ $tryouts->links() }}
        @else
            <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-slate-400">
                Belum ada try out yang tersedia.
            </div>
        @endif
    </div>
@endsection
