<?php

namespace Tests\Feature\Admin;

use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\User;
use App\Services\QuestionImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuestionBankTest extends TestCase
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

    public function test_question_bank_index_can_be_rendered(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.questions.index'))
            ->assertOk()
            ->assertSee('Bank Soal')
            ->assertSee(route('admin.questions.import.template', absolute: false));
    }

    public function test_create_question_page_can_be_rendered_with_categories(): void
    {
        QuestionCategory::create(['sub_test' => 'TWK', 'name' => 'Nasionalisme']);

        $this->actingAs($this->admin)
            ->get(route('admin.questions.create'))
            ->assertOk()
            ->assertSee('Tambah Soal')
            ->assertSee('TWK - Nasionalisme');
    }

    public function test_admin_can_create_twk_question_with_options(): void
    {
        Storage::fake('public');
        $category = QuestionCategory::create(['sub_test' => 'TWK', 'name' => 'Nasionalisme']);

        $this->actingAs($this->admin)
            ->post(route('admin.questions.store'), $this->questionPayload([
                'sub_test' => 'TWK',
                'category_id' => $category->id,
                'question_text' => 'Contoh rumus \\(x^2\\)',
                'question_image_file' => UploadedFile::fake()->image('soal.png'),
                'correct_option' => 'C',
            ]))
            ->assertRedirect(route('admin.questions.index'));

        $question = Question::with('options')->firstOrFail();

        $this->assertSame('TWK', $question->sub_test);
        $this->assertSame('Contoh rumus \\(x^2\\)', $question->question_text);
        $this->assertStringContainsString('/storage/questions/', $question->question_image);
        $this->assertSame('TWK-0001', $question->code);
        $this->assertCount(5, $question->options);
        $this->assertTrue($question->options->firstWhere('label', 'C')->is_correct);
        $this->assertSame(5, $question->options->firstWhere('label', 'C')->score);
        $this->assertSame(0, $question->options->firstWhere('label', 'A')->score);
    }

    public function test_admin_can_create_tkp_question_with_scores(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.questions.store'), $this->questionPayload([
                'sub_test' => 'TKP',
                'correct_option' => null,
                'options' => [
                    'A' => ['text' => 'Pilihan A', 'score' => 1],
                    'B' => ['text' => 'Pilihan B', 'score' => 2],
                    'C' => ['text' => 'Pilihan C', 'score' => 3],
                    'D' => ['text' => 'Pilihan D', 'score' => 4],
                    'E' => ['text' => 'Pilihan E', 'score' => 5],
                ],
            ]))
            ->assertRedirect(route('admin.questions.index'));

        $question = Question::with('options')->firstOrFail();

        $this->assertSame('TKP', $question->sub_test);
        $this->assertFalse($question->options->contains('is_correct', true));
        $this->assertSame(5, $question->options->firstWhere('label', 'E')->score);
    }

    public function test_admin_can_update_question_options(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.questions.store'), $this->questionPayload([
                'sub_test' => 'TIU',
                'correct_option' => 'A',
            ]));

        $question = Question::firstOrFail();

        $this->actingAs($this->admin)
            ->put(route('admin.questions.update', $question), $this->questionPayload([
                'sub_test' => 'TIU',
                'question_text' => 'Pertanyaan sudah diubah',
                'correct_option' => 'E',
                'options' => [
                    'A' => ['text' => 'Opsi A update', 'score' => 0],
                    'B' => ['text' => 'Opsi B update', 'score' => 0],
                    'C' => ['text' => 'Opsi C update', 'score' => 0],
                    'D' => ['text' => 'Opsi D update', 'score' => 0],
                    'E' => ['text' => 'Opsi E update', 'score' => 0],
                ],
            ]))
            ->assertRedirect(route('admin.questions.show', $question));

        $question->refresh()->load('options');

        $this->assertSame('Pertanyaan sudah diubah', $question->question_text);
        $this->assertFalse($question->options->firstWhere('label', 'A')->is_correct);
        $this->assertTrue($question->options->firstWhere('label', 'E')->is_correct);
        $this->assertSame('Opsi E update', $question->options->firstWhere('label', 'E')->text);
    }

    public function test_template_download_route_returns_excel_file(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.questions.import.template'))
            ->assertOk()
            ->assertHeader('content-disposition');
    }

    public function test_admin_can_import_questions_from_excel_template(): void
    {
        $service = new QuestionImportService();
        $spreadsheet = $service->generateTemplate();
        $tempFile = tempnam(sys_get_temp_dir(), 'question_import_test_') . '.xlsx';
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($tempFile);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'template_import_soal.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $this->actingAs($this->admin)
            ->post(route('admin.questions.import'), [
                'file' => $uploadedFile,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseCount('questions', 3);
        $this->assertDatabaseCount('question_options', 15);
        $this->assertDatabaseHas('questions', ['sub_test' => 'TWK', 'code' => 'TWK-0001']);
        $this->assertDatabaseHas('questions', ['sub_test' => 'TIU', 'code' => 'TIU-0001']);
        $this->assertDatabaseHas('questions', ['sub_test' => 'TKP', 'code' => 'TKP-0001']);
    }

    private function questionPayload(array $overrides = []): array
    {
        return array_replace_recursive([
            'sub_test' => 'TWK',
            'category_id' => null,
            'question_text' => 'Contoh pertanyaan bank soal',
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
}
