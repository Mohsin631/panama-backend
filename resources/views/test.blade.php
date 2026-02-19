<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Panama Suppliers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            background: #f5f7fb;
            color: #1a1a2e;
        }

        .login-container {
            display: flex;
            min-height: 100vh;
        }

        /* ========== LEFT HERO SECTION ========== */
        .hero-section {
            flex: 1;
            background: linear-gradient(180deg, #dce6fc 0%, #c5d4f7 40%, #b8c9f2 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 50px 60px 80px;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 50%);
            pointer-events: none;
        }

        .hero-illustration {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            top: 30%;
            z-index: 0;
        }

        /* Office scene SVG illustration */
        .hero-illustration svg {
            width: 100%;
            height: 100%;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-content h1 {
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1.25;
            color: #111827;
            margin-bottom: 20px;
            max-width: 440px;
        }

        .hero-content p {
            font-size: 0.95rem;
            color: #6b7280;
            line-height: 1.7;
            margin-bottom: 36px;
            max-width: 400px;
        }

        .feature-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            font-weight: 500;
            color: #1f2937;
        }

        .feature-list li .check-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            color: #3b82f6;
        }

        /* ========== RIGHT FORM SECTION ========== */
        .form-section {
            width: 520px;
            min-width: 420px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 50px;
        }

        .form-wrapper {
            width: 100%;
            max-width: 400px;
        }

        .form-wrapper h2 {
            font-size: 1.65rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 6px;
        }

        .form-wrapper .subtitle {
            font-size: 0.9rem;
            color: #9ca3af;
            margin-bottom: 36px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 8px;
        }

        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="text"] {
            width: 100%;
            height: 50px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 0 16px;
            font-size: 0.9rem;
            font-family: inherit;
            color: #111827;
            background: #fff;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-group input::placeholder {
            color: #c0c4cc;
        }

        .form-group input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #3b82f6;
            border-radius: 4px;
            cursor: pointer;
        }

        .remember-me span {
            font-size: 0.875rem;
            color: #4b5563;
            user-select: none;
        }

        .forgot-link {
            font-size: 0.875rem;
            font-weight: 600;
            color: #3b82f6;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #2563eb;
        }

        .btn-login {
            width: 100%;
            height: 50px;
            background: #1d4ed8;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.1s ease;
        }

        .btn-login:hover {
            background: #1e40af;
        }

        .btn-login:active {
            transform: scale(0.99);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 28px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            font-size: 0.85rem;
            color: #9ca3af;
            white-space: nowrap;
        }

        .btn-google {
            width: 100%;
            height: 50px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #374151;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s;
        }

        .btn-google:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        .btn-google svg {
            width: 20px;
            height: 20px;
        }

        .signup-link {
            text-align: center;
            margin-top: 32px;
            font-size: 0.9rem;
            color: #6b7280;
        }

        .signup-link a {
            color: #3b82f6;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .signup-link a:hover {
            color: #2563eb;
        }

        /* ========== OFFICE ILLUSTRATION (CSS-based) ========== */
        .office-scene {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 65%;
            overflow: hidden;
            z-index: 0;
        }

        .office-scene svg {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: auto;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 1024px) {
            .hero-section {
                padding: 40px 30px 40px 40px;
            }

            .hero-content h1 {
                font-size: 1.85rem;
            }

            .form-section {
                width: 460px;
                min-width: 380px;
                padding: 40px 36px;
            }
        }

        @media (max-width: 860px) {
            .login-container {
                flex-direction: column;
            }

            .hero-section {
                min-height: 380px;
                padding: 40px 30px;
                align-items: center;
                text-align: center;
            }

            .hero-content h1 {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-content p {
                margin-left: auto;
                margin-right: auto;
            }

            .feature-list {
                align-items: center;
            }

            .form-section {
                width: 100%;
                min-width: unset;
                padding: 40px 24px;
            }

            .form-wrapper {
                max-width: 440px;
                margin: 0 auto;
            }

            .office-scene {
                height: 55%;
            }
        }

        @media (max-width: 480px) {
            .hero-section {
                min-height: 320px;
                padding: 30px 20px;
            }

            .hero-content h1 {
                font-size: 1.5rem;
            }

            .hero-content p {
                font-size: 0.85rem;
            }

            .feature-list li {
                font-size: 0.875rem;
            }

            .form-section {
                padding: 30px 20px;
            }

            .form-wrapper h2 {
                font-size: 1.4rem;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- LEFT: Hero Section -->
        <div class="hero-section">
            <div class="hero-content">
                <h1>Start Sourcing from Verified Panama Suppliers</h1>
                <p>Access wholesale deals, contact suppliers, and grow your business with trusted sourcing.</p>
                <ul class="feature-list">
                    <li>
                        <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Verified suppliers
                    </li>
                    <li>
                        <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Direct WhatsApp contact
                    </li>
                    <li>
                        <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Exclusive wholesale deals
                    </li>
                </ul>
            </div>

            <!-- Office Illustration -->
            <div class="office-scene">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 900 500" preserveAspectRatio="xMidYMax slice">
                    <!-- Floor -->
                    <rect x="0" y="400" width="900" height="100" fill="#a8b8d8" opacity="0.4"/>

                    <!-- Back wall shelves -->
                    <rect x="280" y="120" width="340" height="280" fill="#c4d0e8" opacity="0.5" rx="4"/>
                    <!-- Shelf rows -->
                    <rect x="290" y="140" width="320" height="4" fill="#9aadce"/>
                    <rect x="290" y="200" width="320" height="4" fill="#9aadce"/>
                    <rect x="290" y="260" width="320" height="4" fill="#9aadce"/>
                    <rect x="290" y="320" width="320" height="4" fill="#9aadce"/>
                    <!-- Shelf items - boxes -->
                    <rect x="300" y="150" width="40" height="45" fill="#e8c96a" rx="3"/>
                    <rect x="350" y="155" width="35" height="40" fill="#7cb87c" rx="3"/>
                    <rect x="400" y="148" width="45" height="47" fill="#e8c96a" rx="3"/>
                    <rect x="460" y="155" width="35" height="40" fill="#85a8d0" rx="3"/>
                    <rect x="510" y="150" width="40" height="45" fill="#7cb87c" rx="3"/>
                    <rect x="560" y="152" width="35" height="43" fill="#e8c96a" rx="3"/>

                    <rect x="300" y="210" width="35" height="45" fill="#85a8d0" rx="3"/>
                    <rect x="345" y="215" width="40" height="40" fill="#e8c96a" rx="3"/>
                    <rect x="400" y="208" width="45" height="47" fill="#7cb87c" rx="3"/>
                    <rect x="460" y="215" width="35" height="40" fill="#e8c96a" rx="3"/>
                    <rect x="510" y="210" width="40" height="45" fill="#85a8d0" rx="3"/>
                    <rect x="560" y="213" width="38" height="42" fill="#7cb87c" rx="3"/>

                    <rect x="300" y="270" width="40" height="45" fill="#7cb87c" rx="3"/>
                    <rect x="350" y="275" width="35" height="40" fill="#85a8d0" rx="3"/>
                    <rect x="400" y="268" width="45" height="47" fill="#e8c96a" rx="3"/>
                    <rect x="460" y="275" width="38" height="40" fill="#7cb87c" rx="3"/>
                    <rect x="510" y="270" width="40" height="45" fill="#e8c96a" rx="3"/>
                    <rect x="560" y="273" width="35" height="42" fill="#85a8d0" rx="3"/>

                    <rect x="305" y="330" width="45" height="65" fill="#e8c96a" rx="3"/>
                    <rect x="360" y="335" width="40" height="60" fill="#85a8d0" rx="3"/>
                    <rect x="420" y="328" width="45" height="67" fill="#7cb87c" rx="3"/>
                    <rect x="480" y="335" width="40" height="60" fill="#e8c96a" rx="3"/>
                    <rect x="535" y="330" width="45" height="65" fill="#85a8d0" rx="3"/>

                    <!-- Chat bubble icon -->
                    <rect x="540" y="80" width="90" height="55" fill="#7a8fb5" rx="15"/>
                    <circle cx="560" cy="107" r="5" fill="#c4d0e8"/>
                    <circle cx="585" cy="107" r="5" fill="#c4d0e8"/>
                    <circle cx="610" cy="107" r="5" fill="#c4d0e8"/>

                    <!-- Desk -->
                    <rect x="120" y="350" width="660" height="12" fill="#8a9cc4" rx="3"/>
                    <!-- Desk legs -->
                    <rect x="140" y="362" width="8" height="50" fill="#7a8aad"/>
                    <rect x="752" y="362" width="8" height="50" fill="#7a8aad"/>

                    <!-- Left person - sitting at desk -->
                    <g transform="translate(80, 220)">
                        <!-- Body -->
                        <rect x="30" y="70" width="50" height="80" fill="#374151" rx="5"/>
                        <!-- Head -->
                        <circle cx="55" cy="55" r="22" fill="#f0d5b8"/>
                        <!-- Hair -->
                        <path d="M35 48 Q35 30 55 30 Q75 30 75 48" fill="#374151"/>
                        <!-- Arm to laptop -->
                        <rect x="68" y="95" width="50" height="10" fill="#374151" rx="4"/>
                        <!-- Laptop -->
                        <rect x="100" y="115" width="60" height="5" fill="#4b5563" rx="2"/>
                        <rect x="105" y="80" width="50" height="35" fill="#5b6b8a" rx="3"/>
                        <rect x="110" y="85" width="40" height="25" fill="#93b5e8" rx="2"/>
                    </g>

                    <!-- Right person - sitting at desk -->
                    <g transform="translate(620, 220)">
                        <!-- Body -->
                        <rect x="30" y="70" width="50" height="80" fill="#374151" rx="5"/>
                        <!-- Head -->
                        <circle cx="55" cy="55" r="22" fill="#f0d5b8"/>
                        <!-- Hair -->
                        <path d="M35 48 Q35 30 55 30 Q75 30 75 48" fill="#1f2937"/>
                        <!-- Arm to laptop -->
                        <rect x="-10" y="95" width="50" height="10" fill="#374151" rx="4"/>
                        <!-- Laptop -->
                        <rect x="-25" y="115" width="60" height="5" fill="#4b5563" rx="2"/>
                        <rect x="-20" y="80" width="50" height="35" fill="#5b6b8a" rx="3"/>
                        <rect x="-15" y="85" width="40" height="25" fill="#93b5e8" rx="2"/>
                    </g>

                    <!-- Middle person standing (background) -->
                    <g transform="translate(430, 200)">
                        <rect x="25" y="60" width="45" height="75" fill="#4b5563" rx="5"/>
                        <circle cx="47" cy="45" r="20" fill="#f0d5b8"/>
                        <path d="M30 38 Q30 22 47 22 Q64 22 64 38" fill="#374151"/>
                        <!-- Legs -->
                        <rect x="30" y="135" width="14" height="65" fill="#4b5563" rx="3"/>
                        <rect x="52" y="135" width="14" height="65" fill="#4b5563" rx="3"/>
                    </g>

                    <!-- Small decorative elements -->
                    <!-- Clipboard on desk -->
                    <rect x="350" y="335" width="30" height="18" fill="#e2e8f0" rx="2"/>
                    <rect x="355" y="340" width="20" height="2" fill="#94a3b8"/>
                    <rect x="355" y="345" width="15" height="2" fill="#94a3b8"/>

                    <!-- Coffee cup -->
                    <rect x="270" y="337" width="16" height="14" fill="#f5f5f5" rx="3"/>
                    <rect x="286" y="341" width="6" height="6" fill="none" stroke="#d1d5db" stroke-width="2" rx="3"/>
                </svg>
            </div>
        </div>

        <!-- RIGHT: Login Form Section -->
        <div class="form-section">
            <div class="form-wrapper">
                <h2>Welcome Back</h2>
                <p class="subtitle">Login to continue to your account</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Enter your email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                        @error('email')
                            <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >
                        @error('password')
                            <span style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} checked>
                            <span>Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="" class="forgot-link">Forgot Password?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn-login">Login</button>
                </form>

                <div class="divider">
                    <span>or continue with</span>
                </div>

                <button type="button" class="btn-google" onclick="window.location.href='{{ url('/auth/google') }}'">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Google
                </button>

                <p class="signup-link">
                    Don't have an account? <a href="=">Sign Up</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
