<?php

namespace Database\Seeders;

use App\Models\QuestionCategory;
use Illuminate\Database\Seeder;

class QuestionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // TWK
            ['sub_test' => 'TWK', 'name' => 'Nasionalisme'],
            ['sub_test' => 'TWK', 'name' => 'Integritas'],
            ['sub_test' => 'TWK', 'name' => 'Bela Negara'],
            ['sub_test' => 'TWK', 'name' => 'Pilar Negara'],
            ['sub_test' => 'TWK', 'name' => 'Bahasa Indonesia'],
            // TIU
            ['sub_test' => 'TIU', 'name' => 'Verbal'],
            ['sub_test' => 'TIU', 'name' => 'Numerik'],
            ['sub_test' => 'TIU', 'name' => 'Figural'],
            ['sub_test' => 'TIU', 'name' => 'Analogi'],
            ['sub_test' => 'TIU', 'name' => 'Silogisme'],
            ['sub_test' => 'TIU', 'name' => 'Analitis'],
            ['sub_test' => 'TIU', 'name' => 'Deret Angka'],
            ['sub_test' => 'TIU', 'name' => 'Perbandingan Kuantitatif'],
            ['sub_test' => 'TIU', 'name' => 'Soal Cerita'],
            // TKP
            ['sub_test' => 'TKP', 'name' => 'Pelayanan Publik'],
            ['sub_test' => 'TKP', 'name' => 'Jejaring Kerja'],
            ['sub_test' => 'TKP', 'name' => 'Sosial Budaya'],
            ['sub_test' => 'TKP', 'name' => 'Teknologi Informasi dan Komunikasi'],
            ['sub_test' => 'TKP', 'name' => 'Profesionalisme'],
            ['sub_test' => 'TKP', 'name' => 'Anti Radikalisme'],
        ];

        foreach ($categories as $cat) {
            QuestionCategory::create($cat);
        }
    }
}
