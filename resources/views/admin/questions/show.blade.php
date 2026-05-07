@extends('layouts.admin')
@section('title', 'Detail Soal')
@section('page-title', 'Detail Soal')
@section('page-subtitle', $question->code)

@section('back')
    <a href="{{ route('admin.questions.index') }}"
        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
@endsection
@section('header-actions')
    <a href="{{ route('admin.questions.edit', $question) }}"
        class="inline-flex h-9 items-center gap-2 rounded-lg bg-gradient-to-br from-primary-600 to-primary-700 px-4 text-sm font-semibold text-white shadow-soft transition hover:from-primary-700 hover:to-primary-800">
        <i data-lucide="pencil" class="h-4 w-4"></i> Edit
    </a>
@endsection

@section('content')
    <div class="grid gap-5 xl:grid-cols-[1fr_320px]">
        <div class="space-y-5">
            <section class="rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-5 py-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="rounded-md bg-slate-100 px-2 py-1 font-mono text-xs font-semibold text-slate-700">{{ $question->code }}</span>
                        <span
                            class="rounded-md px-2 py-1 text-xs font-bold
                            {{ $question->sub_test === 'TWK' ? 'bg-blue-50 text-blue-700' : ($question->sub_test === 'TIU' ? 'bg-violet-50 text-violet-700' : 'bg-emerald-50 text-emerald-700') }}">
                            {{ $question->sub_test }}
                        </span>
                        @if ($question->status === 'active')
                            <span
                                class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                        @else
                            <span
                                class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Draft</span>
                        @endif
                    </div>
                </div>
                <div class="space-y-4 p-5">
                    <div class="rich-content text-sm leading-7 text-slate-800">
                        {!! $question->question_text !!}
                    </div>
                    @if ($question->question_image)
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <img src="{{ $question->question_image }}" alt="Gambar soal"
                                class="max-h-96 rounded-lg object-contain">
                        </div>
                    @endif
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-sm font-semibold text-slate-800">Pilihan Jawaban</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach ($question->options as $option)
                        <div class="grid gap-3 p-5 sm:grid-cols-[40px_1fr_auto]">
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-bold
                                {{ $option->is_correct ? 'bg-emerald-600 text-white' : 'border border-slate-300 bg-slate-50 text-slate-700' }}">
                                {{ $option->label }}
                            </span>
                            <p class="pt-2 text-sm leading-6 text-slate-800">{{ $option->text }}</p>
                            <div class="pt-1">
                                @if ($question->sub_test === 'TKP')
                                    <span
                                        class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                        Skor {{ $option->score }}
                                    </span>
                                @elseif ($option->is_correct)
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        <i data-lucide="check" class="h-3 w-3"></i> Benar
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            @if ($question->explanation)
                <section class="rounded-lg border border-amber-200 bg-amber-50 p-5">
                    <div class="flex items-start gap-3">
                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                            <i data-lucide="lightbulb" class="h-5 w-5"></i>
                        </span>
                        <div>
                            <h2 class="text-sm font-semibold text-amber-950">Pembahasan</h2>
                            <div class="rich-content mt-2 text-sm leading-7 text-amber-950">
                                {!! $question->explanation !!}
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        </div>

        <aside class="space-y-5">
            <section class="rounded-2xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-semibold text-slate-800">Metadata</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-slate-500">Kategori</dt>
                        <dd class="text-right font-medium text-slate-800">{{ $question->category->name ?? '-' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-slate-500">Kesulitan</dt>
                        <dd class="font-medium capitalize text-slate-800">{{ $question->difficulty }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-slate-500">Dibuat</dt>
                        <dd class="font-medium text-slate-800">{{ $question->created_at->format('d M Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-slate-500">Update</dt>
                        <dd class="font-medium text-slate-800">{{ $question->updated_at->diffForHumans() }}</dd>
                    </div>
                </dl>
            </section>
        </aside>
    </div>
@endsection
