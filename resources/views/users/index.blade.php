@extends('layouts.app')
@section('page-title', 'Staff & Users')

@section('topbar-actions')
    <a href="{{ route('users.create') }}" class="topbar-btn tb-primary">+ Add Staff</a>
@endsection

@section('content')
<div class="card" style="padding:0;overflow:hidden">
    <table class="data-table">
        <thead>
            <tr>
                <th>Staff Member</th>
                <th>Email</th>
                <th>Role</th>
                <th>Sales Today</th>
                <th>Status</th>
                <th>Joined</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:.7rem">
                        <div style="width:34px;height:34px;border-radius:50%;background:rgba(240,192,64,.1);border:1px solid rgba(240,192,64,.2);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-weight:700;font-size:.78rem;color:var(--accent);flex-shrink:0">
                            {{ substr($user->name,0,2) }}
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:.9rem">{{ $user->name }}</div>
                            @if($user->id === auth()->id())<span style="font-size:.7rem;color:var(--muted)">(you)</span>@endif
                        </div>
                    </div>
                </td>
                <td style="font-size:.85rem;color:var(--muted)">{{ $user->email }}</td>
                <td>
                    <span class="badge {{ $user->role === 'admin' ? 'badge-yellow' : ($user->role === 'manager' ? 'badge-blue' : 'badge-green') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td style="font-family:var(--font-head);font-weight:700">{{ $user->sales_today ?? 0 }}</td>
                <td>
                    <form action="{{ route('users.toggle-active', $user) }}" method="POST" style="display:inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="badge {{ $user->is_active ? 'badge-green' : 'badge-red' }}" style="cursor:pointer;border:none;background:none;padding:.2rem .6rem">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </form>
                </td>
                <td style="font-size:.78rem;color:var(--muted)">{{ $user->created_at->format('d M Y') }}</td>
                <td>
                    <div style="display:flex;gap:.4rem">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-outline btn-sm">Edit</a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Remove this staff member?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background:rgba(239,68,68,.1);color:#fca5a5;border:1px solid rgba(239,68,68,.2)">Del</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--muted)">No staff found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
