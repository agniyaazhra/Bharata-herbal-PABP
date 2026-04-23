<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationSettingController extends Controller
{
    private const GROUP = 'notification';

    private const EVENTS = ['order_created', 'payment_confirmed', 'order_shipped', 'order_completed'];

    public function index()
    {
        $settings = Setting::getGroup(self::GROUP);
        $events   = self::EVENTS;

        return view('admin.settings.notification', compact('settings', 'events'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'email_primary'  => 'nullable|email|max:100',
            'email_backup'   => 'nullable|email|max:100',
            'whatsapp_primary'=> ['nullable', 'regex:/^(\+62|08)[0-9]{7,13}$/'],
            'whatsapp_backup' => ['nullable', 'regex:/^(\+62|08)[0-9]{7,13}$/'],
        ]);

        foreach (self::EVENTS as $event) {
            Setting::set(self::GROUP, "event_{$event}",
                $request->boolean("event_{$event}") ? '1' : '0', 'boolean');
        }

        Setting::set(self::GROUP, 'email_primary',   $request->input('email_primary', ''),    'string');
        Setting::set(self::GROUP, 'email_backup',    $request->input('email_backup', ''),     'string');
        Setting::set(self::GROUP, 'whatsapp_primary',$request->input('whatsapp_primary', ''), 'string');
        Setting::set(self::GROUP, 'whatsapp_backup', $request->input('whatsapp_backup', ''),  'string');

        return back()->with('success', 'Pengaturan notifikasi berhasil disimpan.');
    }

    public function testEmail(Request $request)
    {
        $email = Setting::get(self::GROUP, 'email_primary', '');

        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Email utama belum dikonfigurasi.']);
        }

        Log::info("TEST EMAIL → to: {$email} | time: " . now()->toDateTimeString());

        return response()->json(['success' => true, 'message' => "Email uji coba berhasil dikirim ke {$email}."]);
    }

    public function testWhatsapp(Request $request)
    {
        $phone = Setting::get(self::GROUP, 'whatsapp_primary', '');

        if (!$phone) {
            return response()->json(['success' => false, 'message' => 'Nomor WhatsApp utama belum dikonfigurasi.']);
        }

        Log::info("TEST WHATSAPP → to: {$phone} | time: " . now()->toDateTimeString());

        return response()->json(['success' => true, 'message' => "Pesan uji coba berhasil dikirim ke {$phone}."]);
    }
}
