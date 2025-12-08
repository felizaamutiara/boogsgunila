# OTP Verification Configuration

## Cara Matiin/Hidupkan OTP

OTP verification bisa diatur dengan mudah menggunakan environment variable `OTP_ENABLED` di file `.env`.

### Current Status
- **OTP_ENABLED=false** → OTP dimatiin (Development Mode)
- User langsung verified saat register
- User bisa langsung login tanpa OTP

---

## 1. MATIIN OTP (Development Mode)

**Edit `.env`:**
```env
OTP_ENABLED=false
```

**Behavior:**
- User register → langsung terverifikasi
- User langsung bisa login
- Tidak perlu OTP

---

## 2. HIDUPKAN OTP (Production Mode)

**Edit `.env`:**
```env
OTP_ENABLED=true
```

**Behavior:**
- User register → generate OTP
- OTP dikirim ke email user
- User harus verifikasi OTP dulu sebelum bisa login
- Login akan reject jika email_verified_at NULL

---

## 3. Restart Application

Setelah ubah `.env`, restart application:

```bash
# Matikan server saat ini (Ctrl+C)

# Clear config cache
php artisan config:clear

# Jalankan server lagi
php artisan serve
```

---

## 4. Testing Flow

### Saat OTP_ENABLED=false:
1. Register user baru
2. Langsung diarahkan ke dashboard
3. Bisa langsung akses aplikasi

### Saat OTP_ENABLED=true:
1. Register user baru
2. Diarahkan ke OTP verification page
3. Check debug OTP: `http://localhost:8000/debug-otp/email@example.com`
4. Copy OTP code ke form
5. Setelah verified, baru bisa login

---

## 5. Email Configuration (untuk production)

Untuk OTP benar-benar terkirim ke email, setup SMTP di `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@boogssg.com
MAIL_FROM_NAME="BooGSG"
```

Untuk Gmail:
1. Enable 2FA di Google Account
2. Buat App Password (bukan password akun biasa)
3. Gunakan App Password di `MAIL_PASSWORD`

---

## Summary

| Setting | Mode | Behavior |
|---------|------|----------|
| `OTP_ENABLED=false` | Development | Auto-verify, instant login |
| `OTP_ENABLED=true` | Production | Require OTP, need email |

**Default saat ini:** `OTP_ENABLED=false` (development mode)
