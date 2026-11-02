<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentPositionController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'departments'); // 'departments' or 'positions'

        $departments = Department::orderBy('kode_departemen', 'asc')->get();
        $positions = Position::orderBy('kode_jabatan', 'asc')->get();

        return view('departments_positions.index', compact('departments', 'positions', 'tab'));
    }

    /**
     * Preview data dari database server LSP
     */
    public function syncPreview(Request $request)
    {
        try {
            $lspDepartments = DB::connection('lsp')->table('departemen')->get();
            $lspPositions = DB::connection('lsp')->table('jabatan')->get();

            $existingDeptCodes = Department::pluck('kode_departemen')->toArray();
            $existingPosCodes = Position::pluck('kode_jabatan')->toArray();

            $deptPreview = $lspDepartments->map(function ($d) use ($existingDeptCodes) {
                return [
                    'id'              => $d->id,
                    'kode_departemen' => $d->kode_departemen,
                    'nama_departemen' => $d->nama_departemen,
                    'rumpun_direksi'  => $d->rumpun_direksi ?? '-',
                    'pejabat'         => $d->pejabat ?? '-',
                    'is_existing'     => in_array($d->kode_departemen, $existingDeptCodes),
                ];
            });

            $posPreview = $lspPositions->map(function ($j) use ($existingPosCodes) {
                return [
                    'id'           => $j->id,
                    'kode_jabatan' => $j->kode_jabatan,
                    'nama_jabatan' => $j->nama_jabatan,
                    'is_existing'  => in_array($j->kode_jabatan, $existingPosCodes),
                ];
            });

            return response()->json([
                'success'     => true,
                'departments' => $deptPreview,
                'positions'   => $posPreview,
                'summary'     => [
                    'dept_total'    => count($deptPreview),
                    'dept_new'      => $deptPreview->where('is_existing', false)->count(),
                    'pos_total'     => count($posPreview),
                    'pos_new'       => $posPreview->where('is_existing', false)->count(),
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Gagal terhubung ke database LSP: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eksekusi penarikan data dari server LSP ke database lokal
     */
    public function syncExecute(Request $request)
    {
        $syncTarget = $request->input('target', 'all'); // 'all', 'departments', 'positions'
        $mode = $request->input('mode', 'update'); // 'update' or 'skip'

        $deptSynced = 0;
        $posSynced = 0;

        try {
            if (in_array($syncTarget, ['all', 'departments'])) {
                $lspDepartments = DB::connection('lsp')->table('departemen')->get();
                foreach ($lspDepartments as $d) {
                    $data = [
                        'nama_departemen' => $d->nama_departemen,
                        'rumpun_direksi'  => $d->rumpun_direksi ?? null,
                        'pejabat'         => $d->pejabat ?? null,
                    ];

                    $dept = Department::where('kode_departemen', $d->kode_departemen)->first();
                    if ($dept) {
                        if ($mode === 'update') {
                            $dept->update($data);
                            $deptSynced++;
                        }
                    } else {
                        Department::create(array_merge(['kode_departemen' => $d->kode_departemen], $data));
                        $deptSynced++;
                    }
                }
            }

            if (in_array($syncTarget, ['all', 'positions'])) {
                $lspPositions = DB::connection('lsp')->table('jabatan')->get();
                foreach ($lspPositions as $j) {
                    $data = [
                        'nama_jabatan' => $j->nama_jabatan,
                    ];

                    $pos = Position::where('kode_jabatan', $j->kode_jabatan)->first();
                    if ($pos) {
                        if ($mode === 'update') {
                            $pos->update($data);
                            $posSynced++;
                        }
                    } else {
                        Position::create(array_merge(['kode_jabatan' => $j->kode_jabatan], $data));
                        $posSynced++;
                    }
                }
            }

            return back()->with('success', "Sinkronisasi berhasil! {$deptSynced} Departemen & {$posSynced} Jabatan berhasil disinkronkan dari server LSP.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal sinkronisasi data dari LSP: ' . $e->getMessage());
        }
    }

    // CRUD Departemen Lokal
    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'kode_departemen' => 'required|string|max:50|unique:departments,kode_departemen',
            'nama_departemen' => 'required|string|max:255',
            'rumpun_direksi'  => 'nullable|string|max:255',
            'pejabat'         => 'nullable|string|max:255',
        ]);

        Department::create($validated);
        return back()->with('success', 'Departemen baru berhasil ditambahkan.');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $validated = $request->validate([
            'kode_departemen' => 'required|string|max:50|unique:departments,kode_departemen,' . $department->id,
            'nama_departemen' => 'required|string|max:255',
            'rumpun_direksi'  => 'nullable|string|max:255',
            'pejabat'         => 'nullable|string|max:255',
        ]);

        $department->update($validated);
        return back()->with('success', 'Data Departemen berhasil diperbarui.');
    }

    public function destroyDepartment(Department $department)
    {
        $department->delete();
        return back()->with('success', 'Departemen berhasil dihapus.');
    }

    // CRUD Jabatan Lokal
    public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'kode_jabatan' => 'required|string|max:50|unique:positions,kode_jabatan',
            'nama_jabatan' => 'required|string|max:255',
        ]);

        Position::create($validated);
        return back()->with('success', 'Jabatan baru berhasil ditambahkan.');
    }

    public function updatePosition(Request $request, Position $position)
    {
        $validated = $request->validate([
            'kode_jabatan' => 'required|string|max:50|unique:positions,kode_jabatan,' . $position->id,
            'nama_jabatan' => 'required|string|max:255',
        ]);

        $position->update($validated);
        return back()->with('success', 'Data Jabatan berhasil diperbarui.');
    }

    public function destroyPosition(Position $position)
    {
        $position->delete();
        return back()->with('success', 'Jabatan berhasil dihapus.');
    }
}
