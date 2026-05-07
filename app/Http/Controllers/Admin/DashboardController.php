<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Question;
use App\Models\Student;
use App\Models\Tryout;
use App\Models\TryoutResult;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => Student::count(),
            'active_batches' => Batch::where('is_active', true)->count(),
            'total_tryouts' => Tryout::count(),
            'total_questions' => Question::where('status', 'active')->count(),
            'avg_score' => round(TryoutResult::avg('total_score') ?? 0),
            'pass_rate' => $this->getPassRate(),
        ];

        $latestTryout = Tryout::where('status', 'published')->latest()->first();
        $tryoutStats = null;
        if ($latestTryout) {
            $total = $latestTryout->batches->sum(fn($b) => $b->students->count());
            $done = $latestTryout->results()->distinct('student_id')->count();
            $tryoutStats = [
                'tryout' => $latestTryout,
                'total' => $total,
                'done' => $done,
                'pending' => max(0, $total - $done),
            ];
        }

        $topStudents = TryoutResult::select('student_id', DB::raw('MAX(total_score) as best_score'))
            ->groupBy('student_id')
            ->orderByDesc('best_score')
            ->take(5)
            ->with('student.user')
            ->get();

        $recentActivity = TryoutResult::with(['student.user', 'tryout'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'tryoutStats', 'topStudents', 'recentActivity'));
    }

    private function getPassRate(): float
    {
        $total = TryoutResult::count();
        if ($total === 0) return 0;
        $passed = TryoutResult::where('pass_overall', true)->count();
        return round(($passed / $total) * 100, 1);
    }
}
