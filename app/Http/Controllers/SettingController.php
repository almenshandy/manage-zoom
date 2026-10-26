<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::where('group', 'zoom_meeting')->get();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $allSettings = SystemSetting::where('group', 'zoom_meeting')->get();

        foreach ($allSettings as $setting) {
            if ($setting->type === 'boolean') {
                // Checkbox: jika dicentang nilainya '1', jika tidak '0'
                $value = $request->has($setting->key) ? '1' : '0';
            } else {
                $value = $request->input($setting->key, $setting->value);
            }

            SystemSetting::set($setting->key, $value);
        }

        return back()->with('success', 'Konfigurasi default Zoom API berhasil diperbarui.');
    }
}
