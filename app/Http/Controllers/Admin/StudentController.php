<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'batch'])
            ->when($request->search, fn($q) => $q->where('full_name', 'like', "%{$request->search}%")
                ->orWhereHas('user', fn($q2) => $q2->where('username', 'like', "%{$request->search}%")))
            ->when($request->batch_id, fn($q) => $q->where('batch_id', $request->batch_id))
            ->when($request->status, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('is_active', $request->status === 'active')));

        $students = $query->latest()->paginate(20)->withQueryString();
        $batches = Batch::where('is_active', true)->get();

        return view('admin.students.index', compact('students', 'batches'));
    }

    public function create()
    {
        $batches = Batch::where('is_active', true)->get();
        return view('admin.students.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'batch_id' => 'nullable|exists:batches,id',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['full_name'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role' => 'student',
                'is_active' => $validated['is_active'] ?? true,
            ]);

            Student::create([
                'user_id' => $user->id,
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'batch_id' => $validated['batch_id'] ?? null,
            ]);
        });

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(Student $student)
    {
        $student->load(['user', 'batch']);
        $results = $student->results()->with('tryout')->latest()->get();
        $bestScore = $results->max('total_score');
        $lastScore = $results->first()?->total_score;
        $avgScore = round($results->avg('total_score') ?? 0);
        $passCount = $results->where('pass_overall', true)->count();

        return view('admin.students.show', compact('student', 'results', 'bestScore', 'lastScore', 'avgScore', 'passCount'));
    }

    public function edit(Student $student)
    {
        $student->load('user');
        $batches = Batch::where('is_active', true)->get();
        return view('admin.students.edit', compact('student', 'batches'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $student->user_id . '|alpha_dash',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'batch_id' => 'nullable|exists:batches,id',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($validated, $student, $request) {
            $student->user->update([
                'name' => $validated['full_name'],
                'username' => $validated['username'],
                'is_active' => $request->boolean('is_active'),
            ]);
            $student->update([
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'batch_id' => $validated['batch_id'] ?? null,
            ]);
        });

        return redirect()->route('admin.students.show', $student)->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->user->delete();
        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'exists:students,id',
            'action' => 'required|in:delete,activate,deactivate',
        ]);

        $students = Student::whereIn('id', $request->ids)->with('user')->get();

        match ($request->action) {
            'delete'     => $students->each(fn($s) => $s->user->delete()),
            'activate'   => $students->each(fn($s) => $s->user->update(['is_active' => true])),
            'deactivate' => $students->each(fn($s) => $s->user->update(['is_active' => false])),
        };

        $label = ['delete' => 'dihapus', 'activate' => 'diaktifkan', 'deactivate' => 'dinonaktifkan'];
        return back()->with('success', count($request->ids) . ' siswa berhasil ' . $label[$request->action] . '.');
    }

    public function resetPassword(Request $request, Student $student)
    {
        $request->validate(['password' => 'required|string|min:6']);
        $student->user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password berhasil direset.');
    }
}
