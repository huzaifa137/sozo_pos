@extends('layouts.app')
@section('page-title', 'Add Staff Member')

@section('topbar-actions')
    <a href="{{ route('users.index') }}" class="topbar-btn tb-outline">← Back</a>
@endsection

@section('content')
<div style="max-width:600px">
    <div class="card">
        <h3 style="font-family:var(--font-head);font-weight:700;font-size:1.1rem;margin-bottom:1.4rem;padding-bottom:.8rem;border-bottom:1px solid var(--border)">New Staff Member</h3>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                    @error('name')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" required>
                        <option value="cashier"  {{ old('role') === 'cashier'  ? 'selected':'' }}>Cashier</option>
                        <option value="manager"  {{ old('role') === 'manager'  ? 'selected':'' }}>Manager</option>
                        <option value="admin"    {{ old('role') === 'admin'    ? 'selected':'' }}>Admin</option>
                    </select>
                    @error('role')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
                @error('email')<p class="error-msg">{{ $message }}</p>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required>
                    @error('password')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="password_confirmation" required>
                </div>
            </div>
            <div class="form-group">
                <label>4-Digit PIN <span style="color:var(--muted);font-weight:400;text-transform:none">(optional, for quick login)</span></label>
                <input type="text" name="pin" maxlength="4" pattern="\d{4}" placeholder="e.g. 1234" style="max-width:120px">
                @error('pin')<p class="error-msg">{{ $message }}</p>@enderror
            </div>
            <div style="display:flex;gap:.8rem;padding-top:1rem;border-top:1px solid var(--border)">
                <a href="{{ route('users.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">Create Staff</button>
            </div>
        </form>
    </div>
</div>
@endsection
