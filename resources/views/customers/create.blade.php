@extends('layouts.app')
@section('page-title', 'Add Customer')

@section('topbar-actions')
    <a href="{{ route('customers.index') }}" class="topbar-btn tb-outline">← Back</a>
@endsection

@section('content')
<div style="max-width:600px">
    <div class="card">
        <h3 style="font-family:var(--font-head);font-weight:700;font-size:1.1rem;margin-bottom:1.4rem;padding-bottom:.8rem;border-bottom:1px solid var(--border)">New Customer</h3>
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Grace Nakamya">
                @error('name')<p class="error-msg">{{ $message }}</p>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+256 7XX XXX XXX">
                    @error('phone')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@example.com">
                    @error('email')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="2" placeholder="Street, area, city…">{{ old('address') }}</textarea>
            </div>
            <div style="display:flex;gap:.8rem;padding-top:1rem;border-top:1px solid var(--border)">
                <a href="{{ route('customers.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">Save Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
