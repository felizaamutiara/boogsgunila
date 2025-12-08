<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kode OTP Verifikasi - BooGSG</title>
</head>

<body style="font-family: 'Segoe UI', Arial, sans-serif; background:#e9eef3; padding:25px;">

    <div style="
        max-width:520px; 
        margin:0 auto; 
        background:white; 
        border-radius:14px; 
        overflow:hidden; 
        box-shadow:0 8px 20px rgba(0,0,0,0.15);
    ">

        <!-- HEADER -->
        <div style="
            background: linear-gradient(135deg, #0D47A1, #ffffffff);
            padding:28px 0;
            text-align:center;
            color:white;
        ">
            <h1 style="margin:0; font-size:26px; letter-spacing:1px;">
                <strong>BooGSG</strong>
            </h1>
            <p style="margin:6px 0 0; font-size:14px; opacity:0.9;">
                Sistem Verifikasi Akun
            </p>
        </div>

        <!-- CONTENT -->
        <div style="padding:30px 32px;">
            <h2 style="text-align:center; color:#222; font-size:22px; margin-bottom:18px;">
                Verifikasi Email Anda
            </h2>

            <p style="text-align:center; font-size:15px; color:#555; line-height:1.6;">
                Hai <strong>{{ $user->name }}</strong>,
                berikut adalah kode OTP untuk memverifikasi akun Anda di <strong>BooGSG</strong>.
            </p>

            <div style="text-align:center; margin:32px 0;">
                <div style="
                    display:inline-block;
                    background:#0D47A1;
                    color:white;
                    padding:18px 38px;
                    font-size:30px;
                    border-radius:12px;
                    letter-spacing:6px;
                    font-weight:700;
                    box-shadow:0 5px 12px rgba(13,71,161,0.45);
                ">
                    {{ $otp }}
                </div>
            </div>

            <p style="font-size:14px; color:#444; line-height:1.6;">
                Kode ini berlaku selama <strong>10 menit</strong>.
                Jangan berikan kode ini kepada siapa pun, termasuk pihak yang mengaku dari BooGSG.
            </p>

            <p style="font-size:13px; color:#777; margin-top:25px;">
                Jika Anda tidak meminta verifikasi ini, abaikan email ini.
            </p>
        </div>

        <!-- FOOTER -->
        <div style="background:#0D47A1; padding:16px 0; text-align:center;">
            <p style="font-size:12px; color:white; margin:0;">
                © {{ date('Y') }} <strong>BooGSG</strong> — Semua hak dilindungi.
            </p>
        </div>

    </div>

</body>

</html>