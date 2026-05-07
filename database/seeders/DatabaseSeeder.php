<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\QuestionCategory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@to-skd.test',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create sample batches
        $batch1 = Batch::create(['name' => 'Batch SKD Mei 2026', 'description' => 'Batch reguler Mei 2026', 'is_active' => true]);
        $batch2 = Batch::create(['name' => 'Batch CPNS Intensif 1', 'description' => 'Batch intensif CPNS', 'is_active' => true]);

        // Create sample students
        $students = [
            ['name' => 'Budi Santoso', 'username' => 'budi.santoso', 'phone' => '081234567890'],
            ['name' => 'Siti Rahayu', 'username' => 'siti.rahayu', 'phone' => '081234567891'],
            ['name' => 'Ahmad Fauzi', 'username' => 'ahmad.fauzi', 'phone' => '081234567892'],
        ];
        foreach ($students as $s) {
            $user = User::create([
                'name' => $s['name'],
                'username' => $s['username'],
                'email' => $s['username'] . '@to-skd.test',
                'password' => Hash::make('siswa123'),
                'role' => 'student',
                'is_active' => true,
            ]);
            Student::create([
                'user_id' => $user->id,
                'batch_id' => $batch1->id,
                'full_name' => $s['name'],
                'phone' => $s['phone'],
            ]);
        }

        // Seed question categories
        $this->call(QuestionCategorySeeder::class);
    }
}
