<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\QuestionOption;
use App\Models\Tryout;
use App\Models\TryoutQuestion;
use App\Services\QuestionImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TryoutController extends Controller
{
    public function index()
    {
        $tryouts = Tryout::withCount('questions')->with('batches')->latest()->paginate(15);
        return view('admin.tryouts.index', compact('tryouts'));
    }

    public function create()
    {
        $batches = Batch::where('is_active', true)->get();
        return view('admin.tryouts.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:simulation,sub_test',
            'sub_test' => 'required_if:type,sub_test|nullable|in:TWK,TIU,TKP',
            'duration_minutes' => 'required|integer|min:5|max:180',
            'twk_count' => 'nullable|integer|min:0',
            'tiu_count' => 'nullable|integer|min:0',
            'tkp_count' => 'nullable|integer|min:0',
            'pg_twk' => 'nullable|integer|min:0',
            'pg_tiu' => 'nullable|integer|min:0',
            'pg_tkp' => 'nullable|integer|min:0',
            'repeat_allowed' => 'required|in:unlimited,1,3,none',
            'status' => 'required|in:draft,published,closed',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'batch_ids' => 'nullable|array',
            'batch_ids.*' => 'exists:batches,id',
            'question_source' => 'nullable|in:manual,bank,import',
        ]);

        $validated = $this->prepareTryoutPayload($validated);

        $createdTryout = null;
        DB::transaction(function () use ($validated, &$createdTryout) {
            $createdTryout = Tryout::create($validated);
            if (!empty($validated['batch_ids'])) {
                $createdTryout->batches()->sync($validated['batch_ids']);
            }
        });

        $source = request('question_source', 'manual');
        return match ($source) {
            'bank'   => redirect()->route('admin.tryouts.questions.bank-select', $createdTryout)
                            ->with('success', 'Try out berhasil dibuat. Pilih soal dari bank soal.'),
            'import' => redirect()->route('admin.tryouts.questions.import-page', $createdTryout)
                            ->with('success', 'Try out berhasil dibuat. Upload file soal.'),
            default  => redirect()->route('admin.tryouts.questions.create', $createdTryout)
                            ->with('success', 'Try out berhasil dibuat. Mulai tambahkan soal.'),
        };
    }

    public function show(Tryout $tryout)
    {
        $tryout->load(['batches', 'questions.category', 'results']);
        $categories = QuestionCategory::all()->groupBy('sub_test');
        $bankQuestions = Question::where('status', 'active')
            ->whereNotIn('id', $tryout->questions->pluck('id'))
            ->with('category')
            ->get()
            ->groupBy('sub_test');

        return view('admin.tryouts.show', compact('tryout', 'categories', 'bankQuestions'));
    }

    public function edit(Tryout $tryout)
    {
        $tryout->load('batches');
        $batches = Batch::where('is_active', true)->get();
        return view('admin.tryouts.edit', compact('tryout', 'batches'));
    }

    public function update(Request $request, Tryout $tryout)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:simulation,sub_test',
            'sub_test' => 'required_if:type,sub_test|nullable|in:TWK,TIU,TKP',
            'duration_minutes' => 'required|integer|min:5|max:180',
            'twk_count' => 'nullable|integer|min:0',
            'tiu_count' => 'nullable|integer|min:0',
            'tkp_count' => 'nullable|integer|min:0',
            'pg_twk' => 'nullable|integer|min:0',
            'pg_tiu' => 'nullable|integer|min:0',
            'pg_tkp' => 'nullable|integer|min:0',
            'repeat_allowed' => 'required|in:unlimited,1,3,none',
            'status' => 'required|in:draft,published,closed',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'batch_ids' => 'nullable|array',
            'batch_ids.*' => 'exists:batches,id',
        ]);

        $validated = $this->prepareTryoutPayload($validated);

        DB::transaction(function () use ($validated, $tryout) {
            $tryout->update($validated);
            $tryout->batches()->sync($validated['batch_ids'] ?? []);
        });

        return redirect()->route('admin.tryouts.show', $tryout)->with('success', 'Try out berhasil diperbarui.');
    }

    public function destroy(Tryout $tryout)
    {
        $tryout->delete();
        return redirect()->route('admin.tryouts.index')->with('success', 'Try out berhasil dihapus.');
    }

    public function addFromBank(Request $request, Tryout $tryout)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $maxOrder = TryoutQuestion::where('tryout_id', $tryout->id)->max('order') ?? 0;
        foreach ($request->question_ids as $qid) {
            TryoutQuestion::firstOrCreate(
                ['tryout_id' => $tryout->id, 'question_id' => $qid],
                ['order' => ++$maxOrder]
            );
        }

        return redirect()->route('admin.tryouts.show', $tryout)
            ->with('success', count($request->question_ids) . ' soal berhasil ditambahkan.');
    }

    public function importQuestions(Request $request, Tryout $tryout, QuestionImportService $service)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls|max:10240']);
        $path = $request->file('file')->store('imports');
        $result = $service->import(Storage::disk('local')->path($path), $tryout->id);

        $message = "Berhasil import {$result['imported']} soal ke try out.";
        return redirect()->route('admin.tryouts.show', $tryout)
            ->with('success', $message)
            ->with('import_errors', $result['errors']);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'exists:tryouts,id',
            'action' => 'required|in:delete,publish,draft,close',
        ]);

        match ($request->action) {
            'delete'  => Tryout::whereIn('id', $request->ids)->delete(),
            'publish' => Tryout::whereIn('id', $request->ids)->update(['status' => 'published']),
            'draft'   => Tryout::whereIn('id', $request->ids)->update(['status' => 'draft']),
            'close'   => Tryout::whereIn('id', $request->ids)->update(['status' => 'closed']),
        };

        $label = ['delete' => 'dihapus', 'publish' => 'dipublikasikan', 'draft' => 'dijadikan draft', 'close' => 'ditutup'];
        return back()->with('success', count($request->ids) . ' try out berhasil ' . $label[$request->action] . '.');
    }

    public function bankSelectPage(Tryout $tryout)
    {
        $tryout->load(['questions']);
        $bankQuestions = Question::where('status', 'active')
            ->whereNotIn('id', $tryout->questions->pluck('id'))
            ->with('category')
            ->get()
            ->groupBy('sub_test');

        return view('admin.tryouts.questions.bank-select', compact('tryout', 'bankQuestions'));
    }

    public function importPage(Tryout $tryout)
    {
        $tryout->load(['questions.options']);
        return view('admin.tryouts.questions.import-page', compact('tryout'));
    }

    public function createQuestion(Tryout $tryout)
    {
        $categories = QuestionCategory::orderBy('sub_test')->orderBy('name')->get();

        return view('admin.tryouts.questions.create', compact('tryout', 'categories'));
    }

    public function storeQuestion(Request $request, Tryout $tryout)
    {
        $validated = $request->validate($this->questionRules());
        $validated = $this->sanitizeQuestionPayload($validated);
        $questionImage = $this->resolveQuestionImage($request, $validated['question_image'] ?? null);

        $question = null;
        DB::transaction(function () use ($validated, $questionImage, $tryout, &$question) {
            $question = Question::create([
                'sub_test' => $validated['sub_test'],
                'category_id' => $validated['category_id'] ?? null,
                'question_text' => $validated['question_text'],
                'question_image' => $questionImage,
                'explanation' => $validated['explanation'] ?? null,
                'difficulty' => $validated['difficulty'],
                'status' => $validated['status'],
            ]);

            foreach (['A', 'B', 'C', 'D', 'E'] as $label) {
                $optData = $validated['options'][$label] ?? [];
                $isCorrect = ($validated['sub_test'] !== 'TKP') && ($validated['correct_option'] === $label);
                $score = $validated['sub_test'] === 'TKP'
                    ? (int) ($optData['score'] ?? 0)
                    : ($isCorrect ? 5 : 0);

                QuestionOption::create([
                    'question_id' => $question->id,
                    'label' => $label,
                    'text' => $optData['text'],
                    'score' => $score,
                    'is_correct' => $isCorrect,
                ]);
            }

            TryoutQuestion::create([
                'tryout_id' => $tryout->id,
                'question_id' => $question->id,
                'order' => (TryoutQuestion::where('tryout_id', $tryout->id)->max('order') ?? 0) + 1,
            ]);
        });

        if ($request->action === 'save_and_add') {
            return redirect()
                ->route('admin.tryouts.questions.create', $tryout)
                ->with('success', 'Soal berhasil dibuat dan masuk ke try out. Tambah soal berikutnya.');
        }

        return redirect()
            ->route('admin.tryouts.show', $tryout)
            ->with('success', 'Soal berhasil dibuat dan masuk ke try out.');
    }

    public function removeQuestion(Tryout $tryout, Question $question)
    {
        TryoutQuestion::where('tryout_id', $tryout->id)
            ->where('question_id', $question->id)
            ->delete();

        return back()->with('success', 'Soal berhasil dihapus dari try out.');
    }

    private function prepareTryoutPayload(array $validated): array
    {
        $validated['twk_count'] = (int) ($validated['twk_count'] ?? 0);
        $validated['tiu_count'] = (int) ($validated['tiu_count'] ?? 0);
        $validated['tkp_count'] = (int) ($validated['tkp_count'] ?? 0);

        if ($validated['type'] === 'sub_test') {
            $subTest = strtolower($validated['sub_test']);
            $validated['twk_count'] = $subTest === 'twk' ? max(1, $validated['twk_count']) : 0;
            $validated['tiu_count'] = $subTest === 'tiu' ? max(1, $validated['tiu_count']) : 0;
            $validated['tkp_count'] = $subTest === 'tkp' ? max(1, $validated['tkp_count']) : 0;
        } else {
            $validated['sub_test'] = null;
        }

        $validated['total_questions'] = $validated['twk_count'] + $validated['tiu_count'] + $validated['tkp_count'];

        validator($validated, [
            'total_questions' => 'required|integer|min:1|max:200',
        ])->validate();

        return $validated;
    }

    private function questionRules(): array
    {
        return [
            'sub_test' => 'required|in:TWK,TIU,TKP',
            'category_id' => 'nullable|exists:question_categories,id',
            'question_text' => 'required|string',
            'question_image' => 'nullable|url|max:500',
            'question_image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'explanation' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'status' => 'required|in:active,draft',
            'options' => 'required|array|size:5',
            'options.*.text' => 'required|string',
            'options.*.score' => 'required|integer|min:0|max:5',
            'correct_option' => 'required_if:sub_test,TWK|required_if:sub_test,TIU|nullable|in:A,B,C,D,E',
        ];
    }

    private function resolveQuestionImage(Request $request, ?string $fallbackUrl): ?string
    {
        if ($request->hasFile('question_image_file')) {
            $path = $request->file('question_image_file')->store('questions', 'public');
            return Storage::disk('public')->url($path);
        }

        return $fallbackUrl;
    }

    private function sanitizeQuestionPayload(array $validated): array
    {
        $validated['question_text'] = $this->sanitizeRichContent($validated['question_text']);
        $validated['explanation'] = isset($validated['explanation'])
            ? $this->sanitizeRichContent($validated['explanation'])
            : null;

        return $validated;
    }

    private function sanitizeRichContent(?string $content): ?string
    {
        if ($content === null) {
            return null;
        }

        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><table><thead><tbody><tr><th><td><div><span>';
        $content = strip_tags($content, $allowedTags);

        return preg_replace('/<([a-z][a-z0-9]*)\b[^>]*>/i', '<$1>', $content);
    }
}
