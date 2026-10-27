<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::orderBy('name', 'asc');

        // Default status adalah 'active' jika tidak ada filter status yang dikirim
        $status = $request->input('status', 'active');

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        } // jika 'all', jangan batasi query

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('departemen', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15)->withQueryString();
        return view('users.index', compact('users', 'status'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'username'   => ['required', 'string', 'max:100', 'unique:users,username'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:6'],
            'role'       => ['required', 'in:1,2'],
            'jabatan'    => ['nullable', 'string', 'max:100'],
            'departemen' => ['nullable', 'string', 'max:100'],
            'no_hp'      => ['nullable', 'string', 'max:20'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'username'   => ['required', 'string', 'max:100', Rule::unique('users')->ignore($user->id)],
            'email'      => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'   => ['nullable', 'string', 'min:6'],
            'role'       => ['required', 'in:1,2'],
            'is_active'  => ['required', 'in:0,1'],
            'jabatan'    => ['nullable', 'string', 'max:100'],
            'departemen' => ['nullable', 'string', 'max:100'],
            'no_hp'      => ['nullable', 'string', 'max:20'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'in:1,2'],
        ]);

        if ($user->id === auth()->id() && (int)$request->role !== 1) {
            return back()->with('error', 'Anda tidak dapat menurunkan hak akses akun Anda sendiri.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role {$user->name} berhasil diperbarui.");
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun pengguna {$user->name} berhasil {$statusStr}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Preview sinkronisasi karyawan dari database eksternal LSP
     */
    public function syncPreview(Request $request)
    {
        try {
            $karyawanList = \Illuminate\Support\Facades\DB::connection('lsp')
                ->table('karyawan')
                ->select('id', 'nama_lengkap', 'username', 'jabatan', 'departemen', 'email', 'no_hp', 'active', 'password_text')
                ->orderBy('id', 'asc')
                ->get();
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Gagal terhubung ke database LSP: ' . $e->getMessage()], 500);
            }
            return redirect()->route('users.index')->with('error', 'Gagal terhubung ke database LSP: ' . $e->getMessage());
        }

        $existingUsernames = User::pluck('username')->map(fn($u) => strtolower($u))->toArray();
        $existingEmails = User::pluck('email')->map(fn($e) => strtolower($e))->toArray();

        $previewData = [];
        foreach ($karyawanList as $k) {
            $username = trim($k->username ?? '');
            $email = trim($k->email ?? '');

            // Jika email kosong, buat fallback berdasarkan username
            if (empty($email) && $username) {
                $email = strtolower($username) . '@qhrm.institusi.ac.id';
            }

            $isExisting = in_array(strtolower($username), $existingUsernames) || in_array(strtolower($email), $existingEmails);

            $previewData[] = [
                'id'            => $k->id,
                'name'          => trim($k->nama_lengkap ?? '-'),
                'username'      => $username,
                'email'         => $email,
                'jabatan'       => trim($k->jabatan ?? '-'),
                'departemen'    => trim($k->departemen ?? '-'),
                'no_hp'         => trim($k->no_hp ?? '-'),
                'active'        => (string)$k->active === '1',
                'is_existing'   => $isExisting,
            ];
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data'    => $previewData,
                'summary' => [
                    'total'    => count($previewData),
                    'new'      => collect($previewData)->where('is_existing', false)->count(),
                    'existing' => collect($previewData)->where('is_existing', true)->count(),
                ]
            ]);
        }

        return redirect()->route('users.index');
    }

    /**
     * Eksekusi penarikan karyawan dari database eksternal LSP
     */
    public function syncExecute(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);
        $mode = $request->input('mode', 'skip'); // 'skip' or 'update'

        if (empty($selectedIds)) {
            return redirect()->route('users.index')->with('error', 'Pilih minimal satu data pengguna untuk ditarik.');
        }

        try {
            $karyawanList = \Illuminate\Support\Facades\DB::connection('lsp')
                ->table('karyawan')
                ->whereIn('id', $selectedIds)
                ->get();
        } catch (\Throwable $e) {
            return redirect()->route('users.index')->with('error', 'Gagal membaca database LSP: ' . $e->getMessage());
        }

        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($karyawanList as $k) {
            $username = trim($k->username ?? '');
            if (!$username) continue;

            $email = trim($k->email ?? '');
            if (empty($email)) {
                $email = strtolower($username) . '@qhrm.institusi.ac.id';
            }

            $rawPassword = $k->password_text ?: 'password123';

            $isActive = (string)($k->active ?? '1') === '1';

            $userData = [
                'name'       => trim($k->nama_lengkap ?? $username),
                'jabatan'    => trim($k->jabatan ?? ''),
                'departemen' => trim($k->departemen ?? ''),
                'no_hp'      => trim($k->no_hp ?? ''),
                'role'       => 2, // Pegawai biasa
                'is_active'  => $isActive,
            ];

            $existing = User::where('username', $username)->orWhere('email', $email)->first();

            if ($existing) {
                if ($mode === 'update') {
                    if (!empty($k->password_text)) {
                        $userData['password'] = Hash::make($k->password_text);
                    }
                    $existing->update($userData);
                    $updatedCount++;
                }
            } else {
                $userData['username'] = $username;
                $userData['email']    = $email;
                $userData['password'] = Hash::make($rawPassword);
                User::create($userData);
                $insertedCount++;
            }
        }

        return redirect()->route('users.index')->with(
            'success',
            "Sinkronisasi Karyawan selesai! Berhasil menambahkan {$insertedCount} pengguna baru dan memperbarui {$updatedCount} data lama."
        );
    }
}
