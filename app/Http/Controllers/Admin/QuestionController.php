<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\QuestionOption;
use App\Services\QuestionImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with('category')
            ->when($request->sub_test, fn($q) => $q->where('sub_test', $request->sub_test))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->difficulty, fn($q) => $q->where('difficulty', $request->difficulty))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where(function ($searchQuery) use ($request) {
                $searchQuery->where('question_text', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            }));

        $questions = $query->latest()->paginate(20)->withQueryString();
        $categories = QuestionCategory::orderBy('sub_test')->orderBy('name')->get()->groupBy('sub_test');
        $stats = [
            'total' => Question::count(),
            'active' => Question::where('status', 'active')->count(),
            'draft' => Question::where('status', 'draft')->count(),
            'tkp' => Question::where('sub_test', 'TKP')->count(),
        ];

        return view('admin.questions.index', compact('questions', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = QuestionCategory::orderBy('sub_test')->orderBy('name')->get();
        return view('admin.questions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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
            'options.*.is_correct' => 'nullable|boolean',
            'correct_option' => 'required_if:sub_test,TWK|required_if:sub_test,TIU|nullable|in:A,B,C,D,E',
        ]);

        $validated = $this->sanitizeQuestionPayload($validated);
        $questionImage = $this->resolveQuestionImage($request, $validated['question_image'] ?? null);

        DB::transaction(function () use ($validated, $questionImage) {
            $question = Question::create([
                'sub_test' => $validated['sub_test'],
                'category_id' => $validated['category_id'] ?? null,
                'question_text' => $validated['question_text'],
                'question_image' => $questionImage,
                'explanation' => $validated['explanation'] ?? null,
                'difficulty' => $validated['difficulty'],
                'status' => $validated['status'],
            ]);

            $labels = ['A', 'B', 'C', 'D', 'E'];
            foreach ($labels as $label) {
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
        });

        if ($request->action === 'save_and_add') {
            return redirect()->route('admin.questions.create')->with('success', 'Soal berhasil disimpan. Tambah soal berikutnya.');
        }

        return redirect()->route('admin.questions.index')->with('success', 'Soal berhasil disimpan.');
    }

    public function show(Question $question)
    {
        $question->load('options', 'category');
        return view('admin.questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        $question->load('options');
        $categories = QuestionCategory::orderBy('sub_test')->orderBy('name')->get();
        return view('admin.questions.edit', compact('question', 'categories'));
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
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
            'correct_option' => 'nullable|in:A,B,C,D,E',
        ]);

        $validated = $this->sanitizeQuestionPayload($validated);
        $questionImage = $this->resolveQuestionImage($request, $validated['question_image'] ?? null);

        DB::transaction(function () use ($validated, $question, $questionImage) {
            $question->update([
                'sub_test' => $validated['sub_test'],
                'category_id' => $validated['category_id'] ?? null,
                'question_text' => $validated['question_text'],
                'question_image' => $questionImage,
                'explanation' => $validated['explanation'] ?? null,
                'difficulty' => $validated['difficulty'],
                'status' => $validated['status'],
            ]);

            $labels = ['A', 'B', 'C', 'D', 'E'];
            foreach ($labels as $label) {
                $optData = $validated['options'][$label] ?? [];
                $isCorrect = ($validated['sub_test'] !== 'TKP') && ($validated['correct_option'] === $label);
                $score = $validated['sub_test'] === 'TKP'
                    ? (int) ($optData['score'] ?? 0)
                    : ($isCorrect ? 5 : 0);

                $question->options()->where('label', $label)->update([
                    'text' => $optData['text'],
                    'score' => $score,
                    'is_correct' => $isCorrect,
                ]);
            }
        });

        return redirect()->route('admin.questions.show', $question)->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('admin.questions.index')->with('success', 'Soal berhasil dihapus.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'exists:questions,id',
            'action' => 'required|in:delete,activate,draft',
        ]);

        match ($request->action) {
            'delete'   => Question::whereIn('id', $request->ids)->delete(),
            'activate' => Question::whereIn('id', $request->ids)->update(['status' => 'active']),
            'draft'    => Question::whereIn('id', $request->ids)->update(['status' => 'draft']),
        };

        $label = ['delete' => 'dihapus', 'activate' => 'diaktifkan', 'draft' => 'dijadikan draft'];
        return back()->with('success', count($request->ids) . ' soal berhasil ' . $label[$request->action] . '.');
    }

    public function import(Request $request, QuestionImportService $service)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
            'tryout_id' => 'nullable|exists:tryouts,id',
        ]);

        $path = $request->file('file')->store('imports');
        $result = $service->import(Storage::disk('local')->path($path), $request->tryout_id);

        $message = "Berhasil import {$result['imported']} soal.";
        if ($result['skipped'] > 0) {
            $message .= " {$result['skipped']} baris dilewati.";
        }

        return back()->with('success', $message)->with('import_errors', $result['errors']);
    }

    public function downloadTemplate(QuestionImportService $service)
    {
        $spreadsheet = $service->generateTemplate();
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'template_soal_');
        $writer->save($tempFile);

        return response()->download($tempFile, 'template_import_soal.xlsx')->deleteFileAfterSend();
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
