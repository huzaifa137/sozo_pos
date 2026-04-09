<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>SOZO POS — Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{background:#0b0d11;color:#eef0f6;font-family:'DM Sans',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}
        body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(240,192,64,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(240,192,64,.03) 1px,transparent 1px);background-size:48px 48px;pointer-events:none}
        .login-wrap{width:100%;max-width:440px;position:relative;z-index:1}
        .brand{text-align:center;margin-bottom:2rem}
        .brand h1{font-family:'Syne',sans-serif;font-weight:800;font-size:2rem;color:#f0c040;letter-spacing:-1px}
        .brand h1 span{color:#eef0f6}
        .brand p{color:#6b7280;font-size:.88rem;margin-top:.3rem}
        .card{background:#13161d;border:1px solid #252b3b;border-radius:14px;padding:2rem}
        .form-group{margin-bottom:1rem}
        label{display:block;font-size:.75rem;font-weight:600;color:#6b7280;margin-bottom:.4rem;text-transform:uppercase;letter-spacing:.05em}
        input{width:100%;background:#0b0d11;border:1px solid #252b3b;color:#eef0f6;border-radius:8px;padding:.75rem 1rem;font-family:'DM Sans',sans-serif;font-size:.9rem;outline:none;transition:border-color .18s,box-shadow .18s}
        input:focus{border-color:#f0c040;box-shadow:0 0 0 3px rgba(240,192,64,.1)}
        input::placeholder{color:#3d4356}
        .submit-btn{width:100%;background:#f0c040;color:#0b0d11;border:none;border-radius:9px;padding:.85rem;font-family:'Syne',sans-serif;font-size:1rem;font-weight:800;cursor:pointer;transition:background .18s;margin-top:.4rem}
        .submit-btn:hover{background:#ffd55e}
        .error-box{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:8px;padding:.75rem 1rem;margin-bottom:1.2rem;font-size:.85rem;color:#fca5a5}
        .error-box ul{list-style:none;padding:0}
        .info-note{background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.25);border-radius:8px;padding:.7rem 1rem;margin-bottom:1.2rem;font-size:.82rem;color:#93c5fd}
        .footer-note{text-align:center;margin-top:1.4rem;font-size:.82rem;color:#3d4356}
        .footer-note a{color:#6b7280;text-decoration:none;transition:color .15s}
        .footer-note a:hover{color:#f0c040}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:.8rem}
        .error-msg{color:#fca5a5;font-size:.78rem;margin-top:.3rem}
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="brand">
        <h1>SOZO<span>POS</span></h1>
        <p>Create your admin account</p>
    </div>

    <div class="card">
        <div class="info-note">
            The first registered account is automatically set as <strong>Admin</strong>. Use the Staff section to add cashiers and managers.
        </div>

        @if($errors->any())
        <div class="error-box">
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="e.g. John Ssebulime">
                @error('name')<p class="error-msg">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="you@example.com">
                @error('email')<p class="error-msg">{{ $message }}</p>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Min 8 chars">
                    @error('password')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>Confirm</label>
                    <input type="password" name="password_confirmation" required placeholder="Repeat">
                </div>
            </div>
            <button type="submit" class="submit-btn">Create Account</button>
        </form>
    </div>

    <div class="footer-note">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </div>
</div>
</body>
</html>
