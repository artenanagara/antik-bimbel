<?php

namespace App\Services;

use App\Models\StudentAnswer;
use App\Models\StudentTryout;
use App\Models\TryoutResult;
use Illuminate\Support\Facades\DB;

class TryoutScoringService
{
    public function calculateAndSave(StudentTryout $studentTryout): TryoutResult
    {
        $tryout = $studentTryout->tryout;
        $answers = $studentTryout->answers()->with(['question', 'option'])->get();

        $twkScore = 0;
        $tiuScore = 0;
        $tkpScore = 0;
        $twkCorrect = 0;
        $tiuCorrect = 0;
        $tkpAnswered = 0;
        $totalAnswered = 0;

        foreach ($answers as $answer) {
            if (!$answer->option) continue;
            $totalAnswered++;
            $subTest = $answer->question->sub_test;
            $score = $answer->option->score;

            // Update computed score on answer record
            $answer->update(['score' => $score]);

            if ($subTest === 'TWK') {
                $twkScore += $score;
                if ($answer->option->is_correct) $twkCorrect++;
            } elseif ($subTest === 'TIU') {
                $tiuScore += $score;
                if ($answer->option->is_correct) $tiuCorrect++;
            } elseif ($subTest === 'TKP') {
                $tkpScore += $score;
                $tkpAnswered++;
            }
        }

        $totalScore = $twkScore + $tiuScore + $tkpScore;

        $passTwk = $twkScore >= $tryout->pg_twk;
        $passTiu = $tiuScore >= $tryout->pg_tiu;
        $passTkp = $tkpScore >= $tryout->pg_tkp;
        $passOverall = $passTwk && $passTiu && $passTkp;

        $result = TryoutResult::updateOrCreate(
            ['student_tryout_id' => $studentTryout->id],
            [
                'student_id' => $studentTryout->student_id,
                'tryout_id' => $studentTryout->tryout_id,
                'attempt_number' => $studentTryout->attempt_number,
                'twk_score' => $twkScore,
                'tiu_score' => $tiuScore,
                'tkp_score' => $tkpScore,
                'total_score' => $totalScore,
                'twk_correct' => $twkCorrect,
                'tiu_correct' => $tiuCorrect,
                'tkp_answered' => $tkpAnswered,
                'total_answered' => $totalAnswered,
                'pass_twk' => $passTwk,
                'pass_tiu' => $passTiu,
                'pass_tkp' => $passTkp,
                'pass_overall' => $passOverall,
            ]
        );

        return $result;
    }
}
