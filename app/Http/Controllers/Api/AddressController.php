<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $addresses = auth()->user()->addresses()->get();

        return $this->success(AddressResource::collection($addresses));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label'          => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'street'         => ['required', 'string', 'max:255'],
            'city'           => ['required', 'string', 'max:100'],
            'province'       => ['required', 'string', 'max:100'],
            'postal_code'    => ['required', 'string', 'regex:/^[0-9]{5,6}$/'],
            'is_default'     => ['nullable', 'boolean'],
        ]);

        // If this is the first address or user wants it as default, set it as default
        if (!auth()->user()->addresses()->exists() || $validated['is_default']) {
            auth()->user()->addresses()->update(['is_default' => false]);
            $validated['is_default'] = true;
        } else {
            $validated['is_default'] = false;
        }

        $address = auth()->user()->addresses()->create($validated);

        return $this->success(new AddressResource($address), 'Alamat berhasil ditambahkan.', 201);
    }

    public function destroy(Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            return $this->error('Unauthorized', 403);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        // If deleted address was default, set another as default
        if ($wasDefault) {
            $nextAddress = auth()->user()->addresses()->first();
            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
            }
        }

        return $this->success(null, 'Alamat berhasil dihapus.');
    }

    public function setDefault(Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            return $this->error('Unauthorized', 403);
        }

        auth()->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return $this->success(new AddressResource($address), 'Alamat default berhasil diubah.');
    }
}
