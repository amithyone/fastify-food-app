<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        
        return view('addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('addresses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:500',
            'address_line_2' => 'nullable|string|max:500',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'additional_instructions' => 'nullable|string|max:1000',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $address = Auth::user()->addresses()->create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Address saved successfully!',
                'address' => $address
            ]);
        }

        return redirect()->route('addresses.index')
            ->with('success', 'Address saved successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        $this->authorize('view', $address);
        
        return view('addresses.show', compact('address'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        $this->authorize('update', $address);
        
        return view('addresses.edit', compact('address'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Address $address)
    {
        $this->authorize('update', $address);

        $request->validate([
            'label' => 'nullable|string|max:255',
            'address_line_1' => 'required|string|max:500',
            'address_line_2' => 'nullable|string|max:500',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'additional_instructions' => 'nullable|string|max:1000',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully!',
                'address' => $address
            ]);
        }

        return redirect()->route('addresses.index')
            ->with('success', 'Address updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);

        $address->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully!'
            ]);
        }

        return redirect()->route('addresses.index')
            ->with('success', 'Address deleted successfully!');
    }

    /**
     * Set an address as default.
     */
    public function setDefault(Address $address)
    {
        $this->authorize('update', $address);

        // Unset all other defaults
        Auth::user()->addresses()->update(['is_default' => false]);

        // Set this one as default
        $address->update(['is_default' => true]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Default address updated successfully!'
            ]);
        }

        return redirect()->route('addresses.index')
            ->with('success', 'Default address updated successfully!');
    }

    /**
     * Get user's addresses for AJAX requests (e.g., checkout form).
     */
    public function getUserAddresses()
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();
        
        return response()->json($addresses);
    }
}
