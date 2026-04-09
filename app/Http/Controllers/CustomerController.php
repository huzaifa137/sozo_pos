<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = Customer::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($query) use ($s) {
                $query->where('name', 'like', "%$s%")
                      ->orWhere('phone', 'like', "%$s%")
                      ->orWhere('email', 'like', "%$s%");
            });
        }
        if ($request->filled('tier')) {
            $q->where('loyalty_tier', $request->tier);
        }
        $customers = $q->withCount('sales')->latest()->paginate(20)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);
        Customer::create($data);
        return redirect()->route('customers.index')->with('success', 'Customer added.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);
        $customer->update($data);
        return redirect()->route('customers.index')->with('success', 'Customer updated.');
    }

    public function show(Customer $customer)
    {
        $sales = $customer->sales()->with('items', 'user')->latest()->paginate(10);
        return view('customers.show', compact('customer', 'sales'));
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer removed.');
    }
}
