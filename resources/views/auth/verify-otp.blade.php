<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            height: 100vh;
            background: url('{{ asset("img/GSGunila.jpg") }}') center/cover no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .otp-card {
            width: 430px;
            background: white;
            border-radius: 18px;
            padding: 35px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .brand-title {
            font-size: 32px;
            font-weight: 900;
        }

        .brand-title span:first-child {
            color: #2563eb;
        }

        .brand-title span:last-child {
            color: #111827;
        }

        .otp-container {
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .otp-input {
            width: 48px;
            height: 58px;
            text-align: center;
            font-size: 26px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-weight: bold;
            transition: 0.2s;
        }

        .otp-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 6px rgba(37, 99, 235, 0.4);
        }
    </style>
</head>

<body>

    <div class="otp-card">
        <div class="brand-title mb-2">
            <span>Boo</span><span>GSG.</span>
        </div>

        <h4 class="otp-title mb-3">Verifikasi Email Anda</h4>

        <!-- Success Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✓ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            ℹ️ {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Error Messages -->
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ Terjadi Kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Missing Email Alert -->
        @if(empty($email))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>⚠️ Error:</strong> Email tidak ditemukan. Silakan <a href="{{ route('auth.register') }}" class="alert-link">registrasi ulang</a>.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @else

        <!-- OTP Input Form -->
        <form method="POST" action="{{ route('auth.otp.verify') }}" id="otpForm">
            @csrf

            <!-- Hidden email field -->
            <input type="hidden" name="email" value="{{ $email ?? '' }}">
            <input type="hidden" name="otp" id="otp-hidden">

            <p class="text-muted small mb-3">Kami telah mengirim kode OTP ke: <strong>{{ $email ?? '-' }}</strong></p>

            <label class="form-label fw-bold">Masukkan Kode OTP (6 digit)</label>

            <div class="otp-container mb-3">
                <input maxlength="1" class="otp-input" type="text">
                <input maxlength="1" class="otp-input" type="text">
                <input maxlength="1" class="otp-input" type="text">
                <input maxlength="1" class="otp-input" type="text">
                <input maxlength="1" class="otp-input" type="text">
                <input maxlength="1" class="otp-input" type="text">
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">Verifikasi</button>
        </form>

        <!-- Info & Resend Option -->
        <div class="mt-4">
            <p class="text-center text-muted small mb-2">
                Kode OTP berlaku selama <strong>10 menit</strong>.
            </p>

            <hr class="my-3">

            <p class="text-center text-muted small mb-2">Tidak menerima kode OTP?</p>

            <form method="POST" action="{{ route('auth.otp.resend') }}" id="resendForm" class="d-none">
                @csrf
                <input type="hidden" name="email" value="{{ $email ?? '' }}">
            </form>

            <button type="button" class="btn btn-link btn-sm w-100" onclick="document.getElementById('resendForm').submit();">
                Kirim Ulang Kode OTP
            </button>
        </div>

        @endif
    </div>

    <script>
        const inputs = document.querySelectorAll(".otp-input");
        const hiddenOtp = document.getElementById("otp-hidden");

        inputs.forEach((input, index) => {
            input.addEventListener("input", () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                updateHiddenOtp();
            });

            input.addEventListener("keydown", (e) => {
                if (e.key === "Backspace" && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        function updateHiddenOtp() {
            let otpValue = "";
            inputs.forEach(i => otpValue += (i.value || ""));
            hiddenOtp.value = otpValue;
        }
    </script>

</body>

</html>