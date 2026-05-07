<?php

namespace Tests\Feature\Admin;

use App\Models\Batch;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\QuestionOption;
use App\Models\Tryout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TryoutManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_simulation_tryout_without_total_questions_field(): void
    {
        $batch = Batch::create(['name' => 'Batch SKD Mei 2026', 'is_active' => true]);

        $this->actingAs($this->admin)
            ->post(route('admin.tryouts.store'), $this->tryoutPayload([
                'batch_ids' => [$batch->id],
                'twk_count' => 30,
                'tiu_count' => 35,
                'tkp_count' => 45,
            ]))
            ->assertRedirect(route('admin.tryouts.show', Tryout::first()));

        $tryout = Tryout::firstOrFail();

        $this->assertSame(110, $tryout->total_questions);
        $this->assertNull($tryout->sub_test);
        $this->assertTrue($tryout->batches()->whereKey($batch->id)->exists());
    }

    public function test_admin_can_create_sub_test_tryout_without_total_questions_field(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.tryouts.store'), $this->tryoutPayload([
                'type' => 'sub_test',
                'sub_test' => 'TIU',
                'twk_count' => 30,
                'tiu_count' => 35,
                'tkp_count' => 45,
            ]))
            ->assertRedirect(route('admin.tryouts.show', Tryout::first()));

        $tryout = Tryout::firstOrFail();

        $this->assertSame('TIU', $tryout->sub_test);
        $this->assertSame(0, $tryout->twk_count);
        $this->assertSame(35, $tryout->tiu_count);
        $this->assertSame(0, $tryout->tkp_count);
        $this->assertSame(35, $tryout->total_questions);
    }

    public function test_admin_can_update_tryout_and_recalculate_total_questions(): void
    {
        $tryout = Tryout::create([
            'name' => 'Try Out Lama',
            'type' => 'simulation',
            'duration_minutes' => 100,
            'total_questions' => 110,
            'twk_count' => 30,
            'tiu_count' => 35,
            'tkp_count' => 45,
            'repeat_allowed' => 'unlimited',
            'status' => 'draft',
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.tryouts.update', $tryout), $this->tryoutPayload([
                'name' => 'Try Out Baru',
                'twk_count' => 20,
                'tiu_count' => 25,
                'tkp_count' => 30,
            ]))
            ->assertRedirect(route('admin.tryouts.show', $tryout));

        $tryout->refresh();

        $this->assertSame('Try Out Baru', $tryout->name);
        $this->assertSame(75, $tryout->total_questions);
    }

    public function test_tryout_detail_page_can_be_rendered(): void
    {
        $tryout = $this->createTryout();

        $this->actingAs($this->admin)
            ->get(route('admin.tryouts.show', $tryout))
            ->assertOk()
            ->assertSee('Soal dalam Try Out')
            ->assertSee(route('admin.tryouts.questions.create', $tryout, absolute: false))
            ->assertSee(route('admin.tryouts.questions.import', $tryout, absolute: false))
            ->assertSee(route('admin.tryouts.questions.bank', $tryout, absolute: false));
    }

    public function test_admin_can_create_manual_question_inside_tryout(): void
    {
        Storage::fake('public');
        $tryout = $this->createTryout();
        QuestionCategory::create(['sub_test' => 'TIU', 'name' => 'Aritmetika']);

        $this->actingAs($this->admin)
            ->get(route('admin.tryouts.questions.create', $tryout))
            ->assertOk()
            ->assertSee('Buat Soal untuk Try Out')
            ->assertSee('TIU - Aritmetika');

        $this->actingAs($this->admin)
            ->post(route('admin.tryouts.questions.store', $tryout), $this->questionPayload([
                'sub_test' => 'TIU',
                'question_text' => 'Hitung nilai \\(x^2\\)',
                'question_image_file' => UploadedFile::fake()->image('rumus.png'),
                'correct_option' => 'B',
            ]))
            ->assertRedirect(route('admin.tryouts.show', $tryout));

        $question = Question::with('options')->firstOrFail();

        $this->assertTrue($tryout->questions()->whereKey($question->id)->exists());
        $this->assertStringContainsString('/storage/questions/', $question->question_image);
        $this->assertSame('Hitung nilai \\(x^2\\)', $question->question_text);
        $this->assertTrue($question->options->firstWhere('label', 'B')->is_correct);
    }

    public function test_admin_can_add_and_remove_questions_from_tryout(): void
    {
        $tryout = $this->createTryout();
        $question = $this->createQuestion();

        $this->actingAs($this->admin)
            ->post(route('admin.tryouts.questions.bank', $tryout), [
                'question_ids' => [$question->id],
            ])
            ->assertRedirect();

        $this->assertTrue($tryout->questions()->whereKey($question->id)->exists());

        $this->actingAs($this->admin)
            ->delete(route('admin.tryouts.questions.remove', [$tryout, $question]))
            ->assertRedirect();

        $this->assertFalse($tryout->questions()->whereKey($question->id)->exists());
    }

    private function tryoutPayload(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Try Out',
            'description' => 'Deskripsi try out',
            'type' => 'simulation',
            'sub_test' => null,
            'duration_minutes' => 100,
            'twk_count' => 30,
            'tiu_count' => 35,
            'tkp_count' => 45,
            'pg_twk' => 65,
            'pg_tiu' => 80,
            'pg_tkp' => 166,
            'repeat_allowed' => 'unlimited',
            'status' => 'draft',
            'start_at' => null,
            'end_at' => null,
            'batch_ids' => [],
        ], $overrides);
    }

    private function questionPayload(array $overrides = []): array
    {
        return array_replace_recursive([
            'sub_test' => 'TWK',
            'category_id' => null,
            'question_text' => 'Contoh pertanyaan try out',
            'question_image' => null,
            'explanation' => 'Pembahasan singkat',
            'difficulty' => 'medium',
            'status' => 'active',
            'correct_option' => 'A',
            'options' => [
                'A' => ['text' => 'Pilihan A', 'score' => 0],
                'B' => ['text' => 'Pilihan B', 'score' => 0],
                'C' => ['text' => 'Pilihan C', 'score' => 0],
                'D' => ['text' => 'Pilihan D', 'score' => 0],
                'E' => ['text' => 'Pilihan E', 'score' => 0],
            ],
        ], $overrides);
    }

    private function createTryout(): Tryout
    {
        return Tryout::create([
            'name' => 'Try Out',
            'type' => 'simulation',
            'duration_minutes' => 100,
            'total_questions' => 110,
            'twk_count' => 30,
            'tiu_count' => 35,
            'tkp_count' => 45,
            'repeat_allowed' => 'unlimited',
            'status' => 'draft',
        ]);
    }

    private function createQuestion(): Question
    {
        $question = Question::create([
            'sub_test' => 'TWK',
            'question_text' => 'Contoh soal TWK',
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
