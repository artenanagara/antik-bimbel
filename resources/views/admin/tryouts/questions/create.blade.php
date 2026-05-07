@extends('layouts.admin')
@section('title', 'Buat Soal Try Out')
@section('page-title', 'Buat Soal untuk Try Out')
@section('page-subtitle', $tryout->name)
@section('back')
    <a href="{{ route('admin.tryouts.show', $tryout) }}"
        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
@endsection

@section('content')
    <form action="{{ route('admin.tryouts.questions.store', $tryout) }}" method="POST" enctype="multipart/form-data"
        class="grid gap-5 xl:grid-cols-[1fr_320px]">
        @csrf
        <div class="space-y-5">
            <section class="rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-sm font-semibold text-slate-800">Konten Soal</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Soal yang dibuat di sini otomatis masuk ke try out ini.</p>
                </div>
                <div class="space-y-4 p-5">
                    <div>
                        @include('admin.questions.partials.rich-editor', [
                            'name' => 'question_text',
                            'label' => 'Teks Soal',
                            'value' => '',
                            'rows' => 7,
                            'required' => true,
                            'placeholder' => 'Masukkan teks soal, rumus, atau tabel...',
                        ])
                    </div>

                    <div class="grid gap-4 lg:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Upload Gambar <span
                                    class="font-normal text-slate-400">(opsional)</span></label>
                            <input type="file" name="question_image_file" accept="image/*"
                                class="block w-full rounded-lg border border-slate-300 text-sm text-slate-700 file:mr-3 file:border-0 file:bg-primary-50 file:px-3 file:py-2.5 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">atau URL Gambar <span
                                    class="font-normal text-slate-400">(opsional)</span></label>
                            <input type="url" name="question_image" value="{{ old('question_image') }}"
                                class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm text-slate-800 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-100"
                                placeholder="https://...">
                        </div>
                    </div>

                    <div>
                        @include('admin.questions.partials.rich-editor', [
                            'name' => 'explanation',
                            'label' => 'Pembahasan (opsional)',
                            'value' => '',
                            'rows' => 4,
                            'placeholder' => 'Tambahkan pembahasan...',
                        ])
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white">
                <div
                    class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-800">Pilihan Jawaban</h2>
                        <p class="mt-0.5 text-sm text-slate-500">TWK/TIU memakai satu jawaban benar. TKP memakai skor 1-5.</p>
                    </div>
                    <span id="answer-mode-badge"
                        class="w-fit rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">Mode
                        TWK/TIU</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach (['A', 'B', 'C', 'D', 'E'] as $label)
                        <div class="grid gap-3 p-5 lg:grid-cols-[40px_1fr_120px]">
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-300 bg-slate-50 text-sm font-bold text-slate-700">{{ $label }}</span>
                            <textarea name="options[{{ $label }}][text]" rows="2" required
                                class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm leading-6 text-slate-800 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-100"
                                placeholder="Teks pilihan {{ $label }}">{{ old("options.$label.text") }}</textarea>
                            <div class="flex items-start justify-end">
                                <label
                                    class="twk-tiu-correct flex h-10 items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700">
                                    <input type="radio" name="correct_option" value="{{ $label }}"
                                        {{ old('correct_option') === $label ? 'checked' : '' }}
                                        class="text-blue-600 focus:ring-blue-500">
                                    Benar
                                </label>
                                <label class="tkp-score hidden items-center gap-2 text-sm font-medium text-slate-700">
                                    Skor
                                    <input type="number" name="options[{{ $label }}][score]"
                                        value="{{ old("options.$label.score", 1) }}" min="1" max="5"
                                        class="h-10 w-16 rounded-lg border border-slate-300 px-2 text-center text-sm outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <aside class="space-y-5">
            <section class="rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-sm font-semibold text-slate-800">Pengaturan</h2>
                </div>
                <div class="space-y-4 p-5">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Sub Test</label>
                        <x-form.select name="sub_test" id="sub_test" required placeholder="Pilih sub test" icon="tag"
                            :options="['TWK' => 'TWK', 'TIU' => 'TIU', 'TKP' => 'TKP']" :selected="old('sub_test', $tryout->sub_test)" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Kategori</label>
                        <div class="relative">
                            <i data-lucide="folder" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <select id="category_select" name="category_id"
                                class="w-full pl-9 pr-3 py-2.5 text-sm border border-slate-200 rounded-xl bg-white text-slate-700 outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition appearance-none">
                                <option value="">Tanpa kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        data-sub="{{ $cat->sub_test }}"
                                        {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Kesulitan</label>
                        <x-form.select name="difficulty" icon="gauge" :options="['easy' => 'Mudah', 'medium' => 'Sedang', 'hard' => 'Sulit']" :selected="old('difficulty', 'medium')" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Status</label>
                        <x-form.select name="status" icon="flag" :options="['active' => 'Aktif', 'draft' => 'Draft']" :selected="old('status', 'active')" />
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5">
                <div class="flex flex-col gap-3">
                    <button type="submit"
                        class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-primary-600 to-primary-700 px-4 text-sm font-semibold text-white shadow-soft transition hover:from-primary-700 hover:to-primary-800">
                        <i data-lucide="save" class="h-4 w-4"></i> Simpan ke Try Out
                    </button>
                    <button type="submit" name="action" value="save_and_add"
                        class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50">
                        <i data-lucide="plus-circle" class="h-4 w-4"></i> Simpan & Tambah Lagi
                    </button>
                    <div class="border-t border-slate-100 pt-3">
                        <a href="{{ route('admin.tryouts.show', $tryout) }}"
                            class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-lg border border-primary-200 bg-primary-50 px-4 text-sm font-semibold text-primary-700 transition hover:bg-primary-100">
                            <i data-lucide="eye" class="h-4 w-4"></i> Selesai & Lihat Preview
                        </a>
                    </div>
                </div>
            </section>
        </aside>
    </form>
@endsection

@push('scripts')
    <script>
        function handleSubTestChange(value) {
            const isTKP = value === 'TKP';
            const badge = document.getElementById('answer-mode-badge');

            document.querySelectorAll('.twk-tiu-correct').forEach((element) => {
                element.classList.toggle('hidden', isTKP);
                element.classList.toggle('flex', !isTKP);
            });

            document.querySelectorAll('.tkp-score').forEach((element) => {
                element.classList.toggle('hidden', !isTKP);
                element.classList.toggle('flex', isTKP);
            });

            if (badge) {
                badge.textContent = isTKP ? 'Mode Skor TKP' : 'Mode TWK/TIU';
                badge.className = isTKP ?
                    'w-fit rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700' :
                    'w-fit rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700';
            }
        }

        function filterCategories(subTest) {
            const sel = document.getElementById('category_select');
            if (!sel) return;
            const opts = sel.querySelectorAll('option[data-sub]');
            let hasVisible = false;
            opts.forEach(opt => {
                const show = !subTest || opt.dataset.sub === subTest;
                opt.hidden = !show;
                opt.disabled = !show;
                if (!show && opt.selected) {
                    sel.value = '';
                }
                if (show) hasVisible = true;
            });
        }

        const subTestInput = document.querySelector('#sub_test [data-fs-input]');
        if (subTestInput) {
            subTestInput.addEventListener('change', () => {
                handleSubTestChange(subTestInput.value);
                filterCategories(subTestInput.value);
            });
            handleSubTestChange(subTestInput.value);
            filterCategories(subTestInput.value);
        }
    </script>
@endpush
