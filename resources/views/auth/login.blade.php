<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manage Zoom</title>
    <!-- Favicon & Custom App Icon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="login-page antialiased" style="--login-illustration: url('{{ asset('login-meeting-illustration.svg') }}')">
    <main class="login-shell">
        <section class="login-visual" aria-labelledby="loginIntroTitle">
            <div class="login-brand">
                <img src="{{ asset('icon.svg') }}" alt="" width="34" height="34">
                <span>ManageZoom</span>
            </div>
            <div class="login-intro">
                <p class="login-eyebrow">Sistem manajemen meeting</p>
                <h2 id="loginIntroTitle">Kelola meeting, jadwal,<br>dan akun Zoom.</h2>
                <p>Ajukan peminjaman, pantau ketersediaan akun, dan siapkan meeting dalam satu sistem.</p>
            </div>
            <div class="login-illustration" aria-hidden="true"></div>
            <p class="login-visual-footer">Penjadwalan <span aria-hidden="true">/</span> Peminjaman akun <span
                    aria-hidden="true">/</span> Persetujuan meeting</p>
        </section>

        <section class="login-form-panel" aria-labelledby="loginTitle">
            <div class="login-form">
                <div class="login-mobile-brand login-brand">
                    <img src="{{ asset('icon.svg') }}" alt="" width="32" height="32">
                    <span>ManageZoom</span>
                </div>
                <header class="login-form-heading">
                    <p class="login-eyebrow">Selamat datang kembali</p>
                    <h1 id="loginTitle">Masuk ke akun Anda</h1>
                    <p>Gunakan akun yang terdaftar untuk melanjutkan.</p>
                </header>
                @if ($errors->any())
                    <div class="login-error" role="alert">
                        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="login-fields">
                    @csrf
                    <div class="login-field">
                        <label for="loginIdentity">Username atau Email</label>
                        <input type="text" name="login" id="loginIdentity" value="{{ old('login') }}" required autofocus
                            autocomplete="username" placeholder="admin atau staf@institusi.ac.id">
                    </div>

                    <div class="login-field">
                        <label for="loginPassword">Password</label>
                        <div class="login-password-field">
                            <input type="password" name="password" id="loginPassword" required
                                autocomplete="current-password" placeholder="••••••••">
                            <button type="button" onclick="togglePasswordVisibility()" class="login-password-toggle"
                                aria-label="Tampilkan password" aria-controls="loginPassword" aria-pressed="false"
                                title="Tampilkan / Sembunyikan Password">
                                <i class="fa-regular fa-eye" id="passwordEyeIcon" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <label class="login-remember">
                        <input type="checkbox" name="remember" @checked(old('remember'))>
                        <span>Ingat saya</span>
                    </label>

                    <button type="submit" class="login-submit">
                        Masuk ke Sistem <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>

                {{-- <p class="login-default-hint">Default login: <span>admin / password</span></p> --}}
            </div>
        </section>
    </main>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('loginPassword');
            const eyeIcon = document.getElementById('passwordEyeIcon');
            const toggle = document.querySelector('.login-password-toggle');
            if (!passwordInput || !eyeIcon) return;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.className = 'fa-regular fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                eyeIcon.className = 'fa-regular fa-eye';
            }
            if (toggle) {
                const visible = passwordInput.type === 'text';
                toggle.setAttribute('aria-pressed', String(visible));
                toggle.setAttribute('aria-label', visible ? 'Samarkan password' : 'Tampilkan password');
            }
        }
    </script>
</body>

</html>