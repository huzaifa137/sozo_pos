@extends('layouts.app')
@section('page-title', 'Edit Customer')

@section('topbar-actions')
    <a href="{{ route('customers.index') }}" class="topbar-btn tb-outline">← Back</a>
@endsection

@section('content')
<div style="max-width:600px">
    <div class="card">
        <h3 style="font-family:var(--font-head);font-weight:700;font-size:1.1rem;margin-bottom:1.4rem;padding-bottom:.8rem;border-bottom:1px solid var(--border)">Edit — {{ $customer->name }}</h3>
        <form action="{{ route('customers.update', $customer) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}" required>
                @error('name')<p class="error-msg">{{ $message }}</p>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}">
                </div>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="2">{{ old('address', $customer->address) }}</textarea>
            </div>
            <div style="display:flex;gap:.8rem;padding-top:1rem;border-top:1px solid var(--border)">
                <a href="{{ route('customers.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">Update Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
