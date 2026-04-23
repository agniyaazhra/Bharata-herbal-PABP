<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSession;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class AdminSessionController extends Controller
{

    public function index(Request $request)
    {
        $currentSessionToken = session()->getId();

        $sessions = AdminSession::where('user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('last_active', 'desc')
            ->get()
            ->map(function ($session) use ($currentSessionToken) {
                $session->is_current = ($session->token_id === $currentSessionToken);
                return $session;
            });

        return view('admin.sessions.index', compact('sessions'));
    }

    public function destroy(AdminSession $adminSession, Request $request)
    {
       
        if ($adminSession->user_id !== auth()->id()) {
            abort(403);
        }

        if ($adminSession->token_id === session()->getId()) {
            $message = 'Tidak bisa menghapus sesi saat ini melalui cara ini. Gunakan tombol Logout.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 403);
            }
            return back()->with('error', $message);
        }

        $adminSession->update(['is_active' => false]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Sesi pada perangkat tersebut berhasil diakhiri.',
            ], 200);
        }

        return back()->with('success', 'Sesi pada perangkat tersebut berhasil diakhiri.');
    }

    public function destroyAll(Request $request)
    {
        $currentSessionId = session()->getId();

        AdminSession::where('user_id', auth()->id())
            ->where('token_id', '!=', $currentSessionId)
            ->update(['is_active' => false]);

        $message = 'Semua sesi lain telah diakhiri. Sesi saat ini tetap aktif.';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
            ], 200);
        }

        return back()->with('success', $message);
    }

    public static function recordSession(Request $request): void
    {
        $userAgent = $request->userAgent() ?? '';

        $browser    = self::parseBrowser($userAgent);
        $deviceName = self::parseDevice($userAgent);

        AdminSession::updateOrCreate(
            [
                'user_id'  => auth()->id(),
                'token_id' => session()->getId(),
            ],
            [
                'device_name' => $deviceName,
                'browser'     => $browser,
                'ip_address'  => $request->ip(),
                'location'    => null,
                'last_active' => now(),
                'is_current'  => true,
                'is_active'   => true,
            ]
        );

        auth()->user()->update(['last_login' => now()]);
    }

    public static function touchSession(): void
    {
        AdminSession::where('user_id', auth()->id())
            ->where('token_id', session()->getId())
            ->update(['last_active' => now()]);
    }

    private static function parseBrowser(string $ua): string
    {
        if (str_contains($ua, 'Edg/'))    return 'Microsoft Edge';
        if (str_contains($ua, 'Chrome'))  return 'Google Chrome';
        if (str_contains($ua, 'Firefox')) return 'Mozilla Firefox';
        if (str_contains($ua, 'Safari'))  return 'Apple Safari';
        if (str_contains($ua, 'Opera'))   return 'Opera';
        return 'Browser Tidak Diketahui';
    }

    private static function parseDevice(string $ua): string
    {
        if (preg_match('/Android|iPhone|iPad|iPod/i', $ua))  return 'Mobile';
        if (str_contains($ua, 'Tablet'))                      return 'Tablet';
        return 'Desktop / Laptop';
    }
}
