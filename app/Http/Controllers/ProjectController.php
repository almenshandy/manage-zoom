<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Department;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['department'])
            ->withCount('bookings')
            ->latest();

        // 1. Pencarian Teks
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('department', function ($dq) use ($search) {
                      $dq->where('nama_departemen', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. Filter Departemen
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $projects = $query->paginate(15)->withQueryString();

        // Ambil daftar seluruh departemen untuk pilihan dropdown
        $departments = Department::orderBy('nama_departemen', 'asc')->get();

        // Ringkasan per departemen
        $deptProjectCounts = Project::whereNotNull('department_id')
            ->selectRaw('department_id, count(*) as count')
            ->groupBy('department_id')
            ->pluck('count', 'department_id');

        return view('projects.index', compact('projects', 'departments', 'deptProjectCounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => ['nullable', 'exists:departments,id'],
            'name'          => ['required', 'string', 'max:255', 'unique:projects,name'],
            'code'          => ['nullable', 'string', 'max:50'],
            'description'   => ['nullable', 'string'],
            'start_date'    => ['nullable', 'date'],
            'end_date'      => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'        => ['required', 'in:active,completed,inactive'],
        ]);

        Project::create($validated);

        return back()->with('success', 'Master Proyek baru berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'department_id' => ['nullable', 'exists:departments,id'],
            'name'          => ['required', 'string', 'max:255', 'unique:projects,name,' . $project->id],
            'code'          => ['nullable', 'string', 'max:50'],
            'description'   => ['nullable', 'string'],
            'start_date'    => ['nullable', 'date'],
            'end_date'      => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'        => ['required', 'in:active,completed,inactive'],
        ]);

        $project->update($validated);

        return back()->with('success', 'Data Master Proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return back()->with('success', 'Data Master Proyek berhasil dihapus.');
    }
}
