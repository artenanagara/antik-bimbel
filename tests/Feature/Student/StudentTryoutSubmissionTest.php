<?php

namespace Tests\Feature\Student;

use App\Models\Batch;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Student;
use App\Models\StudentAnswer;
use App\Models\StudentTryout;
use App\Models\Tryout;
use App\Models\TryoutQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTryoutSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_submit_tryout_clamps_negative_duration_to_zero(): void
    {
        $batch = Batch::create(['name' => 'Batch Test', 'is_active' => true]);
        $user = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
        ]);
        $student = Student::create([
            'user_id' => $user->id,
            'batch_id' => $batch->id,
            'full_name' => 'Siswa Test',
        ]);

        $tryout = Tryout::create([
            'name' => 'Try Out Test',
            'type' => 'simulation',
            'duration_minutes' => 100,
            'total_questions' => 1,
            'twk_count' => 1,
            'tiu_count' => 0,
            'tkp_count' => 0,
            'pg_twk' => 0,
            'pg_tiu' => 0,
            'pg_tkp' => 0,
            'repeat_allowed' => 'unlimited',
            'status' => 'published',
        ]);
        $tryout->batches()->sync([$batch->id]);

        $question = Question::create([
            'sub_test' => 'TWK',
            'question_text' => 'Contoh soal',
            'difficulty' => 'medium',
            'status' => 'active',
        ]);
        $option = QuestionOption::create([
            'question_id' => $question->id,
            'label' => 'A',
            'text' => 'Jawaban A',
            'score' => 5,
            'is_correct' => true,
        ]);
        TryoutQuestion::create([
            'tryout_id' => $tryout->id,
            'question_id' => $question->id,
            'order' => 1,
        ]);

        $studentTryout = StudentTryout::create([
            'student_id' => $student->id,
            'tryout_id' => $tryout->id,
            'attempt_number' => 1,
            'status' => 'in_progress',
            'started_at' => now()->addMinute(),
        ]);
        StudentAnswer::create([
            'student_tryout_id' => $studentTryout->id,
            'question_id' => $question->id,
            'option_id' => $option->id,
            'score' => 5,
        ]);

        $this->actingAs($user)
            ->post(route('student.tryouts.submit', [$tryout, $studentTryout]))
            ->assertRedirect(route('student.results.show', $studentTryout));

        $studentTryout->refresh();

        $this->assertSame('completed', $studentTryout->status);
        $this->assertSame(0, $studentTryout->duration_seconds);
        $this->assertDatabaseHas('tryout_results', [
            'student_tryout_id' => $studentTryout->id,
            'total_score' => 5,
        ]);
    }

    public function test_result_counts_answered_questions_and_discussion_shows_unanswered_questions(): void
    {
        $batch = Batch::create(['name' => 'Batch Test', 'is_active' => true]);
        $user = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
        ]);
        $student = Student::create([
            'user_id' => $user->id,
            'batch_id' => $batch->id,
            'full_name' => 'Siswa Test',
        ]);

        $tryout = Tryout::create([
            'name' => 'Try Out Test',
            'type' => 'simulation',
            'duration_minutes' => 100,
            'total_questions' => 2,
            'twk_count' => 2,
            'tiu_count' => 0,
            'tkp_count' => 0,
            'pg_twk' => 0,
            'pg_tiu' => 0,
            'pg_tkp' => 0,
            'repeat_allowed' => 'unlimited',
            'status' => 'published',
        ]);
        $tryout->batches()->sync([$batch->id]);

        $answeredQuestion = $this->createQuestionWithOptions('Soal dijawab');
        $unansweredQuestion = $this->createQuestionWithOptions('Soal tidak dijawab');

        TryoutQuestion::create([
            'tryout_id' => $tryout->id,
            'question_id' => $answeredQuestion->id,
            'order' => 1,
        ]);
        TryoutQuestion::create([
            'tryout_id' => $tryout->id,
            'question_id' => $unansweredQuestion->id,
            'order' => 2,
        ]);

        $studentTryout = StudentTryout::create([
            'student_id' => $student->id,
            'tryout_id' => $tryout->id,
            'attempt_number' => 1,
            'status' => 'in_progress',
            'started_at' => now()->subMinute(),
        ]);
        StudentAnswer::create([
            'student_tryout_id' => $studentTryout->id,
            'question_id' => $answeredQuestion->id,
            'option_id' => $answeredQuestion->options()->where('label', 'B')->first()->id,
            'score' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('student.tryouts.submit', [$tryout, $studentTryout]))
            ->assertRedirect(route('student.results.show', $studentTryout));

        $this->actingAs($user)
            ->get(route('student.results.show', $studentTryout))
            ->assertOk()
            ->assertSee('1/2 dijawab');

        $this->actingAs($user)
            ->get(route('student.results.discussion', $studentTryout))
            ->assertOk()
            ->assertSee('Soal dijawab')
            ->assertSee('Soal tidak dijawab')
            ->assertSee('Tidak dijawab');
    }

    private function createQuestionWithOptions(string $text): Question
    {
        $question = Question::create([
            'sub_test' => 'TWK',
            'question_text' => $text,
            'explanation' => "Pembahasan {$text}",
            'difficulty' => 'medium',
            'status' => 'active',
        ]);

        foreach (['A', 'B', 'C', 'D', 'E'] as $label) {
            QuestionOption::create([
                'question_id' => $question->id,
                'label' => $label,
                'text' => "Pilihan {$label}",
                'score' => $label === 'A' ? 5 : 0,
                'is_correct' => $label === 'A',
            ]);
        }

        return $question;
    }
}
