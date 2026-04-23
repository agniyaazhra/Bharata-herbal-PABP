<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class StoreSettingController extends Controller
{
    private const GROUP = 'store';

    private const TYPES = [
        'store_name'        => 'string',
        'store_description' => 'string',
        'store_address'     => 'string',
        'whatsapp_number'   => 'string',
        'business_email'    => 'string',
        'instagram'         => 'string',
    ];

    public function index()
    {
        $settings = Setting::getGroup(self::GROUP);
        return view('admin.settings.store', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name'        => 'required|string|max:100',
            'store_description' => 'nullable|string|max:500',
            'store_address'     => 'nullable|string|max:300',
            'whatsapp_number'   => ['nullable', 'regex:/^(\+62|08)[0-9]{7,13}$/'],
            'business_email'    => 'nullable|email|max:100',
            'instagram'         => 'nullable|string|max:50',
        ]);

        Setting::saveGroup(self::GROUP, $validated, self::TYPES);

        return back()->with('success', 'Pengaturan toko berhasil disimpan.');
    }
}
