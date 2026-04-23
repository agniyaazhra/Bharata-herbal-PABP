<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::whereIn('role', ['admin', 'super_admin'])
            ->orderBy('role', 'desc')
            ->orderBy('name');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $admins     = $query->paginate(15)->withQueryString();
        $currentUser = auth()->user();

        return view('admin.admins.index', compact('admins', 'currentUser'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,super_admin',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'phone'    => $request->phone,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "Admin {$user->name} berhasil ditambahkan.",
                'user' => $user,
            ], 201);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', "Admin {$user->name} berhasil ditambahkan.");
    }

    public function edit(User $user)
    {
        abort_if($user->isCustomer(), 404);

        return view('admin.admins.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->isCustomer(), 404);

        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            $message = 'Hanya Super Admin yang bisa mengubah data Super Admin lain.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 403);
            }
            return back()->with('error', $message);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'     => 'required|in:admin,super_admin',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "Data admin {$user->name} berhasil diperbarui.",
                'user' => $user,
            ], 200);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', "Data admin {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user, Request $request)
    {
        abort_if($user->isCustomer(), 404);

        if ($user->id === auth()->id()) {
            $message = 'Anda tidak bisa menghapus akun Anda sendiri.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 403);
            }
            return back()->with('error', $message);
        }

        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            $message = 'Hanya Super Admin yang bisa menghapus Super Admin lain.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 403);
            }
            return back()->with('error', $message);
        }

        $name = $user->name;
        $user->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "Admin {$name} berhasil dihapus.",
            ], 200);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', "Admin {$name} berhasil dihapus.");
    }
}
