# Antik Bimbel — Platform Try Out SKD CPNS

Platform try out online berbasis web untuk persiapan Seleksi Kompetensi Dasar (SKD) CPNS. Dibangun dengan Laravel 13 dan Tailwind CSS v4, mencakup panel admin untuk manajemen soal & peserta, serta panel siswa untuk mengerjakan try out secara real-time.

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 13, PHP 8.3 |
| Frontend | Blade, Tailwind CSS v4, Vite |
| Database | MySQL / SQLite |
| Icons | Lucide Icons |

---

## Fitur Utama

### Panel Admin
- **Dashboard** — ringkasan jumlah siswa, soal, try out, dan pengerjaan
- **Manajemen Batch** — kelola kelas/gelombang peserta
- **Manajemen Siswa** — tambah, edit, aktifkan/nonaktifkan siswa; bulk action
- **Bank Soal** — buat soal dengan rich text editor, gambar, sub-test (TWK/TIU/TKP), kategori, dan pilihan jawaban; bulk action; import via Excel
- **Manajemen Try Out** — buat try out tipe Simulasi penuh atau Sub Test; kelola soal dari 3 sumber: buat manual, pilih dari bank soal, atau import; atur passing grade per sub-test; publish / draft / tutup; bulk action
- **Laporan Hasil** — lihat semua hasil pengerjaan siswa, skor per sub-test, detail jawaban

### Panel Siswa
- **Dashboard** — ringkasan riwayat & try out tersedia
- **Try Out** — lihat detail try out, mulai ujian, timer real-time, tandai soal (flag), navigasi antar soal, auto-submit saat waktu habis
- **Hasil** — skor total & per sub-test, perbandingan dengan nilai ambang batas
- **Pembahasan** — review jawaban lengkap dengan kunci jawaban, pembahasan, filter per sub-test & kategori
- **Riwayat** — semua riwayat pengerjaan

---

## Instalasi

### 1. Clone repository

```bash
git clone https://github.com/artenanagara/antik-bimbel.git
cd antik-bimbel
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` sesuaikan koneksi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=antik_bimbel
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi & seeder

```bash
php artisan migrate --seed
```

### 5. Storage link

```bash
php artisan storage:link
```

### 6. Build assets

```bash
npm run build
# atau untuk development:
npm run dev
```

### 7. Jalankan server

```bash
php artisan serve
```

Akses di **http://localhost:8000**

---

## Akun Default

Akun berikut dibuat otomatis saat menjalankan `php artisan migrate --seed`.

### Admin

| Field | Value |
|---|---|
| URL | `/login` |
| Username | `admin` |
| Password | `admin123` |
| Role | Administrator |

### Siswa (Contoh)

| Nama | Username | Password |
|---|---|---|
| Budi Santoso | `budi.santoso` | `siswa123` |
| Siti Rahayu | `siti.rahayu` | `siswa123` |
| Ahmad Fauzi | `ahmad.fauzi` | `siswa123` |

> Semua siswa contoh terdaftar di batch **"Batch SKD Mei 2026"**.

---

## Kategori Soal

Kategori di-seed otomatis bersama database.

| Sub-Test | Kategori |
|---|---|
| **TWK** | Nasionalisme, Integritas, Bela Negara, Pilar Negara, Bahasa Indonesia |
| **TIU** | Verbal, Numerik, Figural, Analogi, Silogisme, Analitis, Deret Angka, Perbandingan Kuantitatif, Soal Cerita |
| **TKP** | Pelayanan Publik, Jejaring Kerja, Sosial Budaya, Teknologi Informasi dan Komunikasi, Profesionalisme, Anti Radikalisme |

---

## Sistem Penilaian

Mengikuti standar CAT BKN:

| Sub-Test | Benar | Salah / Tidak Dijawab |
|---|---|---|
| TWK | +5 | 0 |
| TIU | +5 | 0 |
| TKP | Skor pilihan (1–5) | 0 |

---

## Import Soal

Admin dapat mengimpor soal secara massal via file Excel (`.xlsx`). Format kolom:

| Kolom | Keterangan |
|---|---|
| `sub_test` | TWK / TIU / TKP |
| `question_text` | Teks soal |
| `explanation` | Pembahasan (opsional) |
| `option_a` s/d `option_e` | Teks pilihan jawaban |
| `correct` | Huruf jawaban benar (a–e), khusus TWK & TIU |
| `score_a` s/d `score_e` | Skor tiap pilihan, khusus TKP |

---

## Struktur Direktori Penting

```
app/
├── Http/Controllers/
│   ├── Admin/          # TryoutController, QuestionController, StudentController, ...
│   └── Student/        # TryoutController, ResultController, ...
├── Models/             # Tryout, Question, Student, StudentTryout, ...
└── Services/
    ├── TryoutScoringService.php
    └── QuestionImportService.php

resources/views/
├── admin/              # Panel admin
├── student/            # Panel siswa
├── layouts/            # admin.blade.php, student.blade.php
└── auth/               # login.blade.php

database/
├── migrations/
└── seeders/
    ├── DatabaseSeeder.php
    └── QuestionCategorySeeder.php
```

---

## Lisensi

MIT License — bebas digunakan dan dimodifikasi untuk keperluan pendidikan.
