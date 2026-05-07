<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentTryout;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;

        $history = StudentTryout::where('student_id', $student->id)
            ->with(['tryout', 'result'])
            ->where('status', '!=', 'in_progress')
            ->latest()
            ->paginate(20);

        return view('student.history.index', compact('history'));
    }
}
