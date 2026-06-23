<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses()->get();
        return view('customer.addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('customer.addresses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label'        => 'required|in:home,office,other',
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'address_line' => 'required|string',
            'city'         => 'required|string|max:100',
            'district'     => 'required|string|max:100',
            'zip'          => 'nullable|string|max:20',
            'is_default'   => 'boolean',
        ]);

        $data['user_id'] = auth()->id();

        if (!empty($data['is_default'])) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        Address::create($data);

        return redirect()->route('customer.addresses.index')->with('success', 'Address added.');
    }

    public function edit(Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);
        return view('customer.addresses.edit', compact('address'));
    }

    public function update(Request $request, Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'label'        => 'required|in:home,office,other',
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'address_line' => 'required|string',
            'city'         => 'required|string|max:100',
            'district'     => 'required|string|max:100',
            'zip'          => 'nullable|string|max:20',
            'is_default'   => 'boolean',
        ]);

        if (!empty($data['is_default'])) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect()->route('customer.addresses.index')->with('success', 'Address updated.');
    }

    public function destroy(Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);
        $address->delete();
        return back()->with('success', 'Address deleted.');
    }

    public function setDefault(Address $address)
    {
        abort_if($address->user_id !== auth()->id(), 403);
        auth()->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        return back()->with('success', 'Default address updated.');
    }
}
