<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::withCount("students")->latest()->paginate(15);
        return view("admin.batches.index", compact("batches"));
    }

    public function create()
    {
        return view("admin.batches.create");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "description" => "nullable|string",
            "is_active" => "boolean",
        ]);
        Batch::create($validated);
        return redirect()->route("admin.batches.index")->with("success", "Batch berhasil dibuat.");
    }

    public function show(Batch $batch)
    {
        $batch->loadCount("students")->load("students.user");
        return view("admin.batches.show", compact("batch"));
    }

    public function edit(Batch $batch)
    {
        return view("admin.batches.edit", compact("batch"));
    }

    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "description" => "nullable|string",
            "is_active" => "boolean",
        ]);
        $batch->update($validated);
        return redirect()->route("admin.batches.index")->with("success", "Batch berhasil diperbarui.");
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();
        return redirect()->route("admin.batches.index")->with("success", "Batch berhasil dihapus.");
    }
}
