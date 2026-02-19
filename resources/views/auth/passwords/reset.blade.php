<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Reset Password</title>

    <style>
        :root{
            --bg1:#0b1220;
            --bg2:#0f1b34;
            --card: rgba(255,255,255,.08);
            --stroke: rgba(255,255,255,.14);
            --text:#eaf1ff;
            --muted: rgba(234,241,255,.72);
            --brand:#4f46e5;
            --brand2:#22c55e;
            --danger:#ef4444;
            --warning:#f59e0b;
            --shadow: 0 20px 60px rgba(0,0,0,.35);
            --radius: 18px;
        }
        *{box-sizing:border-box}
        body{
            margin:0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
            color: var(--text);
            background:
                radial-gradient(900px 600px at 18% 15%, rgba(79,70,229,.55), transparent 55%),
                radial-gradient(900px 600px at 82% 20%, rgba(34,197,94,.35), transparent 55%),
                linear-gradient(180deg, var(--bg1), var(--bg2));
            min-height: 100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding: 36px 14px;
        }
        .wrap{
            width: 100%;
            max-width: 980px;
            display:grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 18px;
        }
        @media (max-width: 920px){
            .wrap{grid-template-columns: 1fr; max-width: 520px;}
        }
        .panel{
            border: 1px solid var(--stroke);
            background: var(--card);
            backdrop-filter: blur(16px);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow:hidden;
        }
        .left{
            padding: 30px 30px 22px;
        }
        .right{
            padding: 26px;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
        }
        .brand{
            display:flex;
            align-items:center;
            gap: 10px;
            margin-bottom: 18px;
        }
        .logo{
            width: 44px; height: 44px;
            border-radius: 14px;
            background:
                radial-gradient(circle at 30% 30%, rgba(255,255,255,.6), transparent 55%),
                linear-gradient(135deg, rgba(79,70,229,1), rgba(34,197,94,1));
            box-shadow: 0 10px 30px rgba(79,70,229,.35);
        }
        .brand h1{
            font-size: 18px;
            margin: 0;
            letter-spacing: .2px;
        }
        .brand p{
            margin: 2px 0 0;
            font-size: 13px;
            color: var(--muted);
        }
        h2{
            font-size: 24px;
            margin: 10px 0 6px;
        }
        .sub{
            color: var(--muted);
            margin: 0 0 18px;
            line-height: 1.5;
            font-size: 14px;
        }
        .alert{
            border: 1px solid var(--stroke);
            background: rgba(255,255,255,.06);
            border-radius: 14px;
            padding: 12px 12px;
            margin: 10px 0 16px;
        }
        .alert.success{ border-color: rgba(34,197,94,.45); background: rgba(34,197,94,.08);}
        .alert.error{ border-color: rgba(239,68,68,.45); background: rgba(239,68,68,.08);}
        .alert h4{
            margin:0 0 6px;
            font-size: 14px;
        }
        .alert ul{ margin:0; padding-left: 18px; color: var(--muted);}
        .form{
            display:grid;
            gap: 12px;
        }
        label{
            font-size: 12px;
            color: var(--muted);
            display:block;
            margin: 0 0 6px;
        }
        .input{
            position:relative;
        }
        input{
            width: 100%;
            padding: 14px 14px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.16);
            background: rgba(0,0,0,.22);
            color: var(--text);
            outline:none;
            transition: .15s ease;
        }
        input:focus{
            border-color: rgba(79,70,229,.7);
            box-shadow: 0 0 0 4px rgba(79,70,229,.18);
        }
        input[readonly]{
            opacity:.85;
        }
        .toggle{
            position:absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: 1px solid rgba(255,255,255,.16);
            background: rgba(255,255,255,.06);
            color: var(--text);
            padding: 7px 10px;
            border-radius: 10px;
            cursor:pointer;
            font-size: 12px;
        }
        .row{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        @media (max-width: 520px){
            .row{grid-template-columns: 1fr;}
        }
        .btn{
            display:flex;
            align-items:center;
            justify-content:center;
            gap:10px;
            width:100%;
            margin-top: 8px;
            padding: 14px 16px;
            border-radius: 14px;
            border: none;
            cursor:pointer;
            background: linear-gradient(135deg, rgba(79,70,229,1), rgba(99,102,241,1));
            color: white;
            font-weight: 700;
            letter-spacing: .2px;
            box-shadow: 0 14px 35px rgba(79,70,229,.28);
            transition: transform .08s ease, filter .15s ease;
        }
        .btn:hover{filter: brightness(1.05);}
        .btn:active{transform: translateY(1px);}
        .hint{
            margin-top: 10px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }
        .chips{
            display:flex;
            flex-wrap:wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .chip{
            border: 1px solid rgba(255,255,255,.16);
            background: rgba(255,255,255,.06);
            padding: 10px 12px;
            border-radius: 14px;
            font-size: 12px;
            color: var(--muted);
        }
        .right h3{
            margin:0 0 10px;
            font-size: 16px;
        }
        .right p{
            margin:0 0 14px;
            color: var(--muted);
            font-size: 13px;
            line-height:1.55;
        }
        .steps{
            display:grid;
            gap:10px;
        }
        .step{
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(0,0,0,.18);
            padding: 12px 12px;
            border-radius: 14px;
        }
        .step b{
            display:block;
            font-size: 13px;
            margin-bottom: 4px;
        }
        .foot{
            margin-top: 16px;
            font-size: 12px;
            color: rgba(234,241,255,.55);
        }
        a{ color: rgba(234,241,255,.9); text-decoration: none; }
        a:hover{ text-decoration: underline; }
    </style>
</head>

<body>
<div class="wrap">

    <div class="panel left">
        <div class="brand">
            <div class="logo"></div>
            <div>
                <h1>Panama Marketplace</h1>
                <p>Vendor Portal • Secure password reset</p>
            </div>
        </div>

        <h2>Reset your password</h2>
        <p class="sub">
            Enter a new password for your vendor account. Use a strong password to keep your business secure.
        </p>

        {{-- Success / error messages (optional if you redirect back with session messages) --}}
        @if(session('status'))
            <div class="alert success">
                <h4>Done</h4>
                <div class="hint">{{ session('status') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert error">
                <h4>Something went wrong</h4>
                <div class="hint">{{ session('error') }}</div>
            </div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="alert error">
                <h4>Please fix the following</h4>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="form" method="POST" action="{{ route('password.update') }}" enctype="multipart/form-data">
            @csrf

            {{-- token + email --}}
            <input type="hidden" name="token" value="{{ $token ?? old('token') }}">

            <div>
                <label>Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ $email ?? old('email') }}"
                    readonly
                />
            </div>

            <div class="row">
                <div>
                    <label>New password</label>
                    <div class="input">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Enter new password"
                            autocomplete="new-password"
                            required
                        />
                        <button type="button" class="toggle" onclick="togglePw('password', this)">Show</button>
                    </div>
                </div>

                <div>
                    <label>Confirm password</label>
                    <div class="input">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            placeholder="Confirm password"
                            autocomplete="new-password"
                            required
                        />
                        <button type="button" class="toggle" onclick="togglePw('password_confirmation', this)">Show</button>
                    </div>
                </div>
            </div>

            <button class="btn" type="submit">
                Reset Password
                <span style="opacity:.9;">→</span>
            </button>

            <div class="hint">
                Tip: Use at least 8 characters, including uppercase, lowercase, a number, and a symbol.
            </div>
        </form>
    </div>

    <div class="panel right">
        <div>
            <h3>Security checklist</h3>
            <p>
                Your reset link is verified using a secure token. If you did not request this reset, please ignore this page and
                contact support.
            </p>

            <div class="steps">
                <div class="step">
                    <b>1) Create a strong password</b>
                    Avoid common words, your business name, or phone number.
                </div>
                <div class="step">
                    <b>2) Keep it private</b>
                    Don’t share passwords with staff. Create separate staff accounts later.
                </div>
                <div class="step">
                    <b>3) Stay protected</b>
                    Enable device lock and keep your email secure.
                </div>
            </div>

            <div class="chips">
                <div class="chip">Encrypted token</div>
                <div class="chip">Vendor broker</div>
                <div class="chip">Safe reset flow</div>
            </div>
        </div>

        <div class="foot">
            © {{ date('Y') }} Panama Marketplace • <a href="{{ url('/') }}">Back to home</a>
        </div>
    </div>

</div>

<script>
    function togglePw(id, btn){
        const input = document.getElementById(id);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        btn.textContent = isHidden ? 'Hide' : 'Show';
    }
</script>
</body>
</html>
