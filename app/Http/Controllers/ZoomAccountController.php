<?php

namespace App\Http\Controllers;

use App\Models\ZoomAccount;
use Illuminate\Http\Request;

class ZoomAccountController extends Controller
{
    public function index()
    {
        $accounts = ZoomAccount::withCount('bookings')->latest()->paginate(10);
        return view('zoom_accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('zoom_accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'unique:zoom_accounts,email'],
            'password'           => ['nullable', 'string', 'max:255'],
            'capacity'           => ['required', 'integer', 'min:1'],
            'expired_date'       => ['nullable', 'date'],
            'password_hotmail'   => ['nullable', 'string'],
            'account_id'         => ['nullable', 'string'],
            'client_id'          => ['nullable', 'string'],
            'client_secret'      => ['nullable', 'string'],
            'secret_token'       => ['nullable', 'string'],
            'verification_token' => ['nullable', 'string'],
            'status'             => ['required', 'in:active,inactive,maintenance'],
        ]);

        ZoomAccount::create($validated);

        return redirect()->route('zoom-accounts.index')->with('success', 'Akun Zoom berhasil ditambahkan.');
    }

    public function edit(ZoomAccount $zoomAccount)
    {
        return view('zoom_accounts.edit', compact('zoomAccount'));
    }

    public function update(Request $request, ZoomAccount $zoomAccount)
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'unique:zoom_accounts,email,' . $zoomAccount->id],
            'password'           => ['nullable', 'string', 'max:255'],
            'capacity'           => ['required', 'integer', 'min:1'],
            'expired_date'       => ['nullable', 'date'],
            'password_hotmail'   => ['nullable', 'string'],
            'account_id'         => ['nullable', 'string'],
            'client_id'          => ['nullable', 'string'],
            'client_secret'      => ['nullable', 'string'],
            'secret_token'       => ['nullable', 'string'],
            'verification_token' => ['nullable', 'string'],
            'status'             => ['required', 'in:active,inactive,maintenance'],
        ]);

        // Only update secret fields if provided
        if (empty($validated['client_secret'])) {
            unset($validated['client_secret']);
        }
        if (empty($validated['secret_token'])) {
            unset($validated['secret_token']);
        }
        if (empty($validated['verification_token'])) {
            unset($validated['verification_token']);
        }

        $zoomAccount->update($validated);

        return redirect()->route('zoom-accounts.index')->with('success', 'Data Akun Zoom berhasil diperbarui.');
    }

    public function destroy(ZoomAccount $zoomAccount)
    {
        $zoomAccount->delete();
        return redirect()->route('zoom-accounts.index')->with('success', 'Akun Zoom berhasil dihapus.');
    }
}
