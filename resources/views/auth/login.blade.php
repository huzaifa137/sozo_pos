<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>SOZO POS — Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{
            background:#0b0d11;
            color:#eef0f6;
            font-family:'DM Sans',sans-serif;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:1rem;
        }

        /* subtle grid background */
        body::before{
            content:'';
            position:fixed;inset:0;
            background-image:
                linear-gradient(rgba(240,192,64,.03) 1px,transparent 1px),
                linear-gradient(90deg,rgba(240,192,64,.03) 1px,transparent 1px);
            background-size:48px 48px;
            pointer-events:none;
        }

        .login-wrap{
            width:100%;max-width:420px;
            position:relative;z-index:1;
        }

        .brand{
            text-align:center;
            margin-bottom:2rem;
        }
        .brand h1{
            font-family:'Syne',sans-serif;
            font-weight:800;
            font-size:2rem;
            color:#f0c040;
            letter-spacing:-1px;
        }
        .brand h1 span{color:#eef0f6}
        .brand p{color:#6b7280;font-size:.88rem;margin-top:.3rem}

        .card{
            background:#13161d;
            border:1px solid #252b3b;
            border-radius:14px;
            padding:2rem;
        }

        .form-group{margin-bottom:1.1rem}
        label{
            display:block;font-size:.75rem;font-weight:600;
            color:#6b7280;margin-bottom:.4rem;
            text-transform:uppercase;letter-spacing:.05em;
        }
        input{
            width:100%;
            background:#0b0d11;
            border:1px solid #252b3b;
            color:#eef0f6;
            border-radius:8px;
            padding:.75rem 1rem;
            font-family:'DM Sans',sans-serif;
            font-size:.95rem;
            outline:none;
            transition:border-color .18s, box-shadow .18s;
        }
        input:focus{
            border-color:#f0c040;
            box-shadow:0 0 0 3px rgba(240,192,64,.1);
        }
        input::placeholder{color:#3d4356}

        .remember-row{
            display:flex;align-items:center;justify-content:space-between;
            margin-bottom:1.4rem;font-size:.85rem;
        }
        .remember-row label{
            display:flex;align-items:center;gap:.5rem;
            text-transform:none;letter-spacing:0;font-size:.85rem;
            color:#6b7280;cursor:pointer;margin-bottom:0;
        }
        input[type=checkbox]{
            width:16px;height:16px;accent-color:#f0c040;cursor:pointer;
        }
        .forgot-link{color:#6b7280;text-decoration:none;font-size:.82rem;transition:color .15s}
        .forgot-link:hover{color:#f0c040}

        .submit-btn{
            width:100%;
            background:#f0c040;
            color:#0b0d11;
            border:none;
            border-radius:9px;
            padding:.85rem;
            font-family:'Syne',sans-serif;
            font-size:1rem;
            font-weight:800;
            cursor:pointer;
            transition:background .18s;
            letter-spacing:.01em;
        }
        .submit-btn:hover{background:#ffd55e}

        .error-box{
            background:rgba(239,68,68,.1);
            border:1px solid rgba(239,68,68,.3);
            border-radius:8px;
            padding:.75rem 1rem;
            margin-bottom:1.2rem;
            font-size:.85rem;
            color:#fca5a5;
        }
        .error-box ul{list-style:none;padding:0}
        .error-box li{margin-bottom:.2rem}
        .error-box li:last-child{margin-bottom:0}

        .footer-note{
            text-align:center;
            margin-top:1.4rem;
            font-size:.82rem;
            color:#3d4356;
        }
        .footer-note a{color:#6b7280;text-decoration:none;transition:color .15s}
        .footer-note a:hover{color:#f0c040}
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="brand">
        <h1>SOZO<span>POS</span></h1>
        <p>Sign in to your account</p>
    </div>

    <div class="card">
        @if($errors->any())
        <div class="error-box">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('status'))
        <div style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);border-radius:8px;padding:.75rem 1rem;margin-bottom:1.2rem;font-size:.85rem;color:#86efac">
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="you@example.com"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                >
            </div>

            <div class="remember-row">
                <label>
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
                @if(Route::has('password.request'))
                <a class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="submit-btn">Sign In</button>
        </form>
    </div>

    <div class="footer-note">
        Need an account? <a href="{{ route('register') }}">Register here</a>
    </div>
</div>
</body>
</html>
