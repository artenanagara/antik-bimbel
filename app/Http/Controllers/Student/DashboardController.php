<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Tryout;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $student = $user->student()->with('batch')->first();

        $availableTryouts = Tryout::where('status', 'published')
            ->where(function ($q) use ($student) {
                $q->whereDoesntHave('batches')
                    ->orWhereHas('batches', fn($q2) => $q2->where('batches.id', $student?->batch_id));
            })
            ->when(now(), function ($q) {
                $q->where(fn($q2) => $q2->whereNull('start_at')->orWhere('start_at', '<=', now()))
                    ->where(fn($q2) => $q2->whereNull('end_at')->orWhere('end_at', '>=', now()));
            })
            ->latest()
            ->take(6)
            ->get();

        $recentResults = $student?->results()
            ->with('tryout')
            ->latest()
            ->take(5)
            ->get();

        $bestScore = $student?->results()->max('total_score') ?? 0;
        $lastScore = $student?->results()->latest()->first()?->total_score ?? 0;

        return view('student.dashboard', compact('student', 'availableTryouts', 'recentResults', 'bestScore', 'lastScore'));
    }
}
