<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentAnswer;
use App\Models\StudentTryout;
use App\Models\Tryout;
use App\Services\TryoutScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TryoutController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;

        $tryouts = Tryout::where('status', 'published')
            ->where(function ($q) use ($student) {
                $q->whereDoesntHave('batches')
                    ->orWhereHas('batches', fn($q2) => $q2->where('batches.id', $student?->batch_id));
            })
            ->withCount('questions')
            ->latest()
            ->paginate(12);

        return view('student.tryouts.index', compact('tryouts', 'student'));
    }

    public function show(Tryout $tryout)
    {
        $student = Auth::user()->student;
        $attempts = StudentTryout::where('student_id', $student->id)
            ->where('tryout_id', $tryout->id)
            ->with('result')
            ->orderBy('attempt_number')
            ->get();

        $inProgress = $attempts->where('status', 'in_progress')->first();

        return view('student.tryouts.show', compact('tryout', 'attempts', 'inProgress'));
    }

    public function start(Request $request, Tryout $tryout)
    {
        $student = Auth::user()->student;

        // Check if there's already an in-progress attempt
        $inProgress = StudentTryout::where('student_id', $student->id)
            ->where('tryout_id', $tryout->id)
            ->where('status', 'in_progress')
            ->first();

        if ($inProgress) {
            return redirect()->route('student.tryouts.exam', [$tryout, $inProgress]);
        }

        // Check repeat limit
        $attemptCount = StudentTryout::where('student_id', $student->id)
            ->where('tryout_id', $tryout->id)
            ->where('status', '!=', 'in_progress')
            ->count();

        $limit = $tryout->repeat_limit;
        if ($limit !== null && $attemptCount >= $limit + 1) {
            return back()->withErrors(['limit' => 'Batas pengulangan try out telah tercapai.']);
        }

        $studentTryout = StudentTryout::create([
            'student_id' => $student->id,
            'tryout_id' => $tryout->id,
            'attempt_number' => $attemptCount + 1,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return redirect()->route('student.tryouts.exam', [$tryout, $studentTryout]);
    }

    public function exam(Tryout $tryout, StudentTryout $studentTryout)
    {
        $this->authorizeStudentTryout($studentTryout);

        if ($studentTryout->status !== 'in_progress') {
            return redirect()->route('student.results.show', $studentTryout);
        }

        // Auto-submit if time is up
        if ($studentTryout->getRemainingSeconds() <= 0) {
            return $this->doSubmit($tryout, $studentTryout, app(TryoutScoringService::class));
        }

        $questions = $tryout->questions()->with('options')->get();
        $answers = $studentTryout->answers()->get()->keyBy('question_id');
        $remainingSeconds = $studentTryout->getRemainingSeconds();

        return view('student.tryouts.exam', compact('tryout', 'studentTryout', 'questions', 'answers', 'remainingSeconds'));
    }

    public function saveAnswer(Request $request, Tryout $tryout, StudentTryout $studentTryout)
    {
        $this->authorizeStudentTryout($studentTryout);

        if ($studentTryout->status !== 'in_progress') {
            return response()->json(['error' => 'Try out sudah selesai.'], 422);
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'option_id' => 'nullable|exists:question_options,id',
        ]);

        // Compute score
        $score = 0;
        if ($request->option_id) {
            $option = \App\Models\QuestionOption::find($request->option_id);
            $score = $option?->score ?? 0;
        }

        StudentAnswer::updateOrCreate(
            [
                'student_tryout_id' => $studentTryout->id,
                'question_id' => $request->question_id,
            ],
            [
                'option_id' => $request->option_id,
                'score' => $score,
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function toggleFlag(Request $request, Tryout $tryout, StudentTryout $studentTryout)
    {
        $this->authorizeStudentTryout($studentTryout);

        $request->validate(['question_id' => 'required|exists:questions,id']);

        $answer = StudentAnswer::firstOrCreate(
            [
                'student_tryout_id' => $studentTryout->id,
                'question_id' => $request->question_id,
            ],
            ['option_id' => null, 'score' => 0]
        );

        $answer->update(['is_flagged' => !$answer->is_flagged]);

        return response()->json(['flagged' => $answer->is_flagged]);
    }

    public function submit(Request $request, Tryout $tryout, StudentTryout $studentTryout, TryoutScoringService $scoringService)
    {
        $this->authorizeStudentTryout($studentTryout);
        return $this->doSubmit($tryout, $studentTryout, $scoringService);
    }

    private function doSubmit(Tryout $tryout, StudentTryout $studentTryout, TryoutScoringService $scoringService)
    {
        $duration = $studentTryout->started_at
            ? max(0, now()->timestamp - $studentTryout->started_at->timestamp)
            : 0;

        $studentTryout->update([
            'status' => 'completed',
            'submitted_at' => now(),
            'duration_seconds' => $duration,
        ]);

        $scoringService->calculateAndSave($studentTryout);

        return redirect()->route('student.results.show', $studentTryout)
            ->with('success', 'Jawaban berhasil dikumpulkan.');
    }

    private function authorizeStudentTryout(StudentTryout $studentTryout): void
    {
        $student = Auth::user()->student;
        if ($studentTryout->student_id !== $student?->id) {
            abort(403);
        }
    }
}
