<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class QuestionImportService
{
    public function import(string $filePath, ?int $tryoutId = null): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $errors = [];
        $imported = 0;
        $skipped = 0;

        // Skip header row
        array_shift($rows);

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;
            try {
                $this->importRow($row, $rowNum, $tryoutId);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris {$rowNum}: " . $e->getMessage();
                $skipped++;
            }
        }

        return compact('imported', 'skipped', 'errors');
    }

    private function importRow(array $row, int $rowNum, ?int $tryoutId): void
    {
        // Columns: A=No, B=Sub Test, C=Materi, D=Pertanyaan, E=Gambar Soal,
        // F=Pilihan A, G=Pilihan B, H=Pilihan C, I=Pilihan D, J=Pilihan E,
        // K=Jawaban Benar, L=Skor A, M=Skor B, N=Skor C, O=Skor D, P=Skor E,
        // Q=Pembahasan, R=Tingkat Kesulitan, S=Status

        $subTest = strtoupper(trim($row['B'] ?? ''));
        if (!in_array($subTest, ['TWK', 'TIU', 'TKP'])) {
            throw new \InvalidArgumentException("Sub test tidak valid: '{$subTest}'. Harus TWK, TIU, atau TKP.");
        }

        $questionText = trim($row['D'] ?? '');
        if (empty($questionText)) {
            throw new \InvalidArgumentException('Kolom pertanyaan kosong.');
        }

        $categoryName = trim($row['C'] ?? '');
        $category = QuestionCategory::where('sub_test', $subTest)
            ->where('name', $categoryName)
            ->first();

        $difficulty = match (strtolower(trim($row['R'] ?? 'sedang'))) {
            'mudah', 'easy' => 'easy',
            'sulit', 'hard' => 'hard',
            default => 'medium',
        };

        $status = strtolower(trim($row['S'] ?? 'aktif')) === 'draft' ? 'draft' : 'active';

        $questionImage = trim($row['E'] ?? '') ?: null;
        $explanation = trim($row['Q'] ?? '') ?: null;

        DB::transaction(function () use ($row, $subTest, $questionText, $questionImage, $explanation, $category, $difficulty, $status, $tryoutId) {
            $question = Question::create([
                'sub_test' => $subTest,
                'category_id' => $category?->id,
                'question_text' => $this->sanitizeRichContent($questionText),
                'question_image' => $questionImage,
                'explanation' => $this->sanitizeRichContent($explanation),
                'difficulty' => $difficulty,
                'status' => $status,
            ]);

            $correctLetter = strtoupper(trim($row['K'] ?? ''));
            $labels = ['A' => 'F', 'B' => 'G', 'C' => 'H', 'D' => 'I', 'E' => 'J'];
            $scoreColumns = ['A' => 'L', 'B' => 'M', 'C' => 'N', 'D' => 'O', 'E' => 'P'];

            foreach ($labels as $label => $col) {
                $optionText = trim($row[$col] ?? '');
                if (empty($optionText)) continue;

                $isCorrect = ($subTest !== 'TKP') && ($label === $correctLetter);
                $score = 0;
                if ($subTest === 'TKP') {
                    $score = (int) ($row[$scoreColumns[$label]] ?? 0);
                } elseif ($isCorrect) {
                    $score = 5;
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'label' => $label,
                    'text' => $optionText,
                    'score' => $score,
                    'is_correct' => $isCorrect,
                ]);
            }

            if ($tryoutId) {
                $maxOrder = \App\Models\TryoutQuestion::where('tryout_id', $tryoutId)->max('order') ?? 0;
                \App\Models\TryoutQuestion::create([
                    'tryout_id' => $tryoutId,
                    'question_id' => $question->id,
                    'order' => $maxOrder + 1,
                ]);
            }
        });
    }

    public function generateTemplate(): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Soal');

        $headers = [
            'A' => 'No',
            'B' => 'Sub Test',
            'C' => 'Materi',
            'D' => 'Pertanyaan / LaTeX / HTML',
            'E' => 'Gambar Soal (URL)',
            'F' => 'Pilihan A',
            'G' => 'Pilihan B',
            'H' => 'Pilihan C',
            'I' => 'Pilihan D',
            'J' => 'Pilihan E',
            'K' => 'Jawaban Benar (TWK/TIU)',
            'L' => 'Skor A',
            'M' => 'Skor B',
            'N' => 'Skor C',
            'O' => 'Skor D',
            'P' => 'Skor E',
            'Q' => 'Pembahasan / LaTeX / HTML',
            'R' => 'Tingkat Kesulitan',
            'S' => 'Status',
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . '1', $header);
        }

        // Sample rows
        $samples = [
            [
                1,
                'TWK',
                'Nasionalisme',
                'Sikap yang sesuai sila kedua Pancasila adalah ...',
                '',
                'Menghargai martabat setiap orang',
                'Memilih teman berdasarkan daerah',
                'Mengutamakan kelompok sendiri',
                'Menolak pendapat berbeda',
                'Mendahulukan senior tanpa aturan',
                'A',
                0,
                0,
                0,
                0,
                0,
                'Sila kedua menekankan kemanusiaan yang adil dan beradab.',
                'Mudah',
                'Aktif',
            ],
            [
                2,
                'TIU',
                'Deret Angka',
                'Tentukan angka berikutnya: 5, 9, 17, 33, ...\nRumus boleh ditulis: \\(a_n = 2^n + 1\\)',
                'https://example.com/gambar-soal.png',
                '49',
                '57',
                '65',
                '67',
                '71',
                'C',
                0,
                0,
                0,
                0,
                0,
                'Selisihnya \\(4, 8, 16, 32\\), sehingga \\(33 + 32 = 65\\).',
                'Sedang',
                'Aktif',
            ],
            [
                3,
                'TKP',
                'Pelayanan Publik',
                '<p>Anda menerima keluhan warga. Pilihan respons terbaik?</p><table><tbody><tr><td>Kondisi</td><td>Respons</td></tr><tr><td>Data belum lengkap</td><td>Minta data tambahan</td></tr></tbody></table>',
                '',
                'Meminta warga kembali lain hari tanpa penjelasan',
                'Mencatat keluhan dan memberi alur tindak lanjut',
                'Mengabaikan karena bukan tugas utama',
                'Menyalahkan petugas sebelumnya',
                'Meminta warga mencari petugas lain',
                '-',
                1,
                5,
                2,
                1,
                2,
                '<p>Respons terbaik menunjukkan empati, akuntabilitas, dan tindak lanjut yang jelas.</p>',
                'Sedang',
                'Aktif',
            ],
        ];

        foreach ($samples as $i => $row) {
            $cols = array_keys($headers);
            foreach ($row as $j => $val) {
                $sheet->setCellValue($cols[$j] . ($i + 2), $val);
            }
        }

        $sheet->getStyle('A1:S1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:S1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4F46E5');
        $sheet->getStyle('A:S')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->getStyle('D:D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('Q:Q')->getAlignment()->setWrapText(true);
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(52);
        $sheet->getColumnDimension('E')->setWidth(36);
        foreach (range('F', 'J') as $col) {
            $sheet->getColumnDimension($col)->setWidth(28);
        }
        $sheet->getColumnDimension('K')->setWidth(22);
        foreach (range('L', 'P') as $col) {
            $sheet->getColumnDimension($col)->setWidth(10);
        }
        $sheet->getColumnDimension('Q')->setWidth(52);
        $sheet->getColumnDimension('R')->setWidth(18);
        $sheet->getColumnDimension('S')->setWidth(12);
        $sheet->freezePane('A2');

        $guide = $spreadsheet->createSheet();
        $guide->setTitle('Panduan');
        $guideRows = [
            ['Bagian', 'Panduan'],
            ['LaTeX inline', 'Gunakan delimiter \\( ... \\), contoh: \\(x^2 + y^2 = z^2\\)'],
            ['LaTeX block', 'Gunakan delimiter \\[ ... \\], contoh: \\[\\frac{a}{b}\\]'],
            ['Tabel', 'Boleh pakai HTML sederhana: <table><tbody><tr><td>Kolom 1</td><td>Kolom 2</td></tr></tbody></table>'],
            ['Gambar', 'Kolom E hanya menerima URL gambar, misalnya https://domain.com/gambar.png atau /storage/questions/file.png. Jika gambar masih di komputer, upload lewat form manual lalu pakai URL-nya.'],
            ['TWK/TIU', 'Isi Jawaban Benar dengan A/B/C/D/E. Skor otomatis 5 untuk benar dan 0 untuk lainnya.'],
            ['TKP', 'Isi Skor A-E dengan angka 1 sampai 5. Jawaban Benar boleh diisi tanda - atau dikosongkan.'],
            ['Status', 'Isi Aktif atau Draft.'],
            ['Kesulitan', 'Isi Mudah, Sedang, atau Sulit.'],
        ];

        foreach ($guideRows as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $column = $colIndex === 0 ? 'A' : 'B';
                $guide->setCellValue($column . ($rowIndex + 1), $value);
            }
        }
        $guide->getStyle('A1:B1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $guide->getStyle('A1:B1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4F46E5');
        $guide->getColumnDimension('A')->setWidth(20);
        $guide->getColumnDimension('B')->setWidth(110);
        $guide->getStyle('A:B')->getAlignment()->setWrapText(true)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
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
