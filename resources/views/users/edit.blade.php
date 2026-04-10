@extends('layouts.app')
@section('page-title', 'Edit Staff')

@section('topbar-actions')
    <a href="{{ route('users.index') }}" class="topbar-btn tb-outline">← Back</a>
@endsection

@section('content')
<div style="max-width:600px">
    <div class="card">
        <h3 style="font-family:var(--font-head);font-weight:700;font-size:1.1rem;margin-bottom:1.4rem;padding-bottom:.8rem;border-bottom:1px solid var(--border)">Edit — {{ $user->name }}</h3>
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" required>
                        <option value="cashier"  {{ old('role',$user->role)==='cashier'  ? 'selected':'' }}>Cashier</option>
                        <option value="manager"  {{ old('role',$user->role)==='manager'  ? 'selected':'' }}>Manager</option>
                        <option value="admin"    {{ old('role',$user->role)==='admin'    ? 'selected':'' }}>Admin</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')<p class="error-msg">{{ $message }}</p>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>New Password <span style="color:var(--muted);font-weight:400;text-transform:none">(leave blank to keep)</span></label>
                    <input type="password" name="password">
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>4-Digit PIN</label>
                    <input type="text" name="pin" maxlength="4" pattern="\d{4}" value="{{ old('pin') }}" placeholder="••••" style="max-width:120px">
                </div>
                <div class="form-group">
                    <label>Account Status</label>
                    <select name="is_active">
                        <option value="1" {{ $user->is_active ? 'selected':'' }}>Active</option>
                        <option value="0" {{ !$user->is_active ? 'selected':'' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:.8rem;padding-top:1rem;border-top:1px solid var(--border)">
                <a href="{{ route('users.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
