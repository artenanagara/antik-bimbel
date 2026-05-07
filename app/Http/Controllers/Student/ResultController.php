<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentTryout;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function show(StudentTryout $studentTryout)
    {
        $this->authorize($studentTryout);
        $studentTryout->load(['tryout.questions', 'result', 'student', 'answers.question']);

        return view('student.results.show', compact('studentTryout'));
    }

    public function discussion(StudentTryout $studentTryout)
    {
        $this->authorize($studentTryout);
        $studentTryout->load(['tryout.questions.options', 'tryout.questions.category', 'result', 'answers.option']);

        return view('student.results.discussion', compact('studentTryout'));
    }

    private function authorize(StudentTryout $studentTryout): void
    {
        $student = Auth::user()->student;
        if ($studentTryout->student_id !== $student?->id) {
            abort(403);
        }
    }
}
