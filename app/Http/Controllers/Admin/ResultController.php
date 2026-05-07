<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\StudentTryout;
use App\Models\Tryout;
use App\Models\TryoutResult;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = TryoutResult::with(['student.user', 'student.batch', 'tryout'])
            ->when($request->tryout_id, fn($q) => $q->where('tryout_id', $request->tryout_id))
            ->when($request->batch_id, fn($q) => $q->whereHas('student', fn($q2) => $q2->where('batch_id', $request->batch_id)))
            ->when($request->student_id, fn($q) => $q->where('student_id', $request->student_id))
            ->when($request->status === 'pass', fn($q) => $q->where('pass_overall', true))
            ->when($request->status === 'fail', fn($q) => $q->where('pass_overall', false));

        $results = $query->latest()->paginate(25)->withQueryString();
        $tryouts = Tryout::all();
        $batches = Batch::all();

        return view('admin.results.index', compact('results', 'tryouts', 'batches'));
    }

    public function show(StudentTryout $studentTryout)
    {
        $studentTryout->load(['student.user', 'tryout', 'result', 'answers.question.options', 'answers.option']);
        return view('admin.results.show', compact('studentTryout'));
    }

    public function export(StudentTryout $studentTryout)
    {
        $studentTryout->load(['student.user', 'tryout', 'result', 'answers.question.options', 'answers.option']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Hasil Try Out');

        // Header info
        $sheet->setCellValue('A1', 'Nama Siswa');
        $sheet->setCellValue('B1', $studentTryout->student->full_name);
        $sheet->setCellValue('A2', 'Try Out');
        $sheet->setCellValue('B2', $studentTryout->tryout->name);
        $sheet->setCellValue('A3', 'Tanggal');
        $sheet->setCellValue('B3', $studentTryout->submitted_at?->format('d/m/Y H:i'));
        $sheet->setCellValue('A5', 'Sub Test');
        $sheet->setCellValue('B5', 'Nilai');
        $sheet->setCellValue('C5', 'Passing Grade');
        $sheet->setCellValue('D5', 'Status');

        $result = $studentTryout->result;
        $rows = [
            ['TWK', $result?->twk_score, $studentTryout->tryout->pg_twk, $result?->pass_twk ? 'Lulus' : 'Tidak Lulus'],
            ['TIU', $result?->tiu_score, $studentTryout->tryout->pg_tiu, $result?->pass_tiu ? 'Lulus' : 'Tidak Lulus'],
            ['TKP', $result?->tkp_score, $studentTryout->tryout->pg_tkp, $result?->pass_tkp ? 'Lulus' : 'Tidak Lulus'],
            ['Total', $result?->total_score, '-', $result?->pass_overall ? 'Lulus' : 'Tidak Lulus'],
        ];

        foreach ($rows as $i => $row) {
            $r = $i + 6;
            $sheet->setCellValue('A' . $r, $row[0]);
            $sheet->setCellValue('B' . $r, $row[1]);
            $sheet->setCellValue('C' . $r, $row[2]);
            $sheet->setCellValue('D' . $r, $row[3]);
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'result_');
        $writer->save($tempFile);
        $filename = 'hasil_' . str_replace(' ', '_', $studentTryout->student->full_name) . '_' . now()->format('Ymd') . '.xlsx';

        return response()->download($tempFile, $filename)->deleteFileAfterSend();
    }
}
