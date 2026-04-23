<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserDashboardController extends Controller
{
    public function profile()
    {
        return view('dashboard.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        auth()->user()->update($request->only('name', 'phone'));
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|confirmed|min:8',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->with('error', 'Password saat ini tidak sesuai.');
        }

        auth()->user()->update(['password' => bcrypt($request->password)]);
        return back()->with('success', 'Password berhasil diubah.');
    }

    public function addresses()
    {
        $addresses = auth()->user()->addresses()->get();
        return view('dashboard.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'label'          => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'street'         => 'required|string',
            'city'           => 'required|string|max:100',
            'province'       => 'required|string|max:100',
            'postal_code'    => 'required|string|max:10',
        ]);

        if ($request->boolean('is_default')) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        auth()->user()->addresses()->create($request->all() + ['is_default' => $request->boolean('is_default')]);
        return back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function deleteAddress(Address $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);
        $address->delete();
        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    public function setDefaultAddress(Address $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);
        auth()->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        return back()->with('success', 'Alamat utama berhasil diperbarui.');
    }
}
