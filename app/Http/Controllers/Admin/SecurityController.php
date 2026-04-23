<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSession;
use App\Models\User;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    
    public function index(Request $request)
    {
       
        $currentUser = auth()->user();

        $admins = User::where('role', 'admin')
            ->orWhere('role', 'super_admin')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $adminsArray = $admins->getCollection()->toArray();

        $sessions = AdminSession::where('user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('last_active', 'desc')
            ->get()
            ->map(function ($session) {
                $session->is_current = ($session->token_id === session()->getId());
                return $session;
            });

        $sessionsArray = $sessions->toArray();

        return view('admin.security.index', compact('admins', 'sessions', 'currentUser', 'adminsArray', 'sessionsArray'));
    }
}
