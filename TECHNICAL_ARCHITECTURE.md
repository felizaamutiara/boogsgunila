# Boogsgunila App - Technical Architecture & Security Documentation

**Project:** Boogsgunila (Venue Booking Management System)  
**Framework:** Laravel 10.48.29  
**Database:** PostgreSQL  
**PHP Version:** 8.4  
**Auth System:** Session-based with Role-Based Access Control (RBAC)

---

## 1. AUTHENTICATION & VERIFICATION SYSTEM

### 1.1 Login Mechanism

**File:** `app/Http/Controllers/AuthController.php`

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        $user = Auth::user();
        
        // Role-based redirect
        if ($user && $user->role === 'A') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->intended(route('booking.index'));
    }

    return back()->withErrors(['email' => 'Kredensial tidak valid']);
}
```

**How it works:**
1. User submits email + password via `resources/views/auth/login.blade.php`
2. `Auth::attempt()` verifies credentials against database
3. **Session regeneration** prevents session fixation attacks
4. **Role-based redirect:** Admin (role='A') → admin dashboard, User (role='U') → booking list
5. Failed login returns error message

### 1.2 Password Hashing

**Location:** User registration in `AuthController::register()`

```php
$user = User::create([
    'name' => $data['name'],
    'email' => $data['email'],
    'password' => bcrypt($data['password']),  // ← BCrypt hashing
    'role' => 'U',
]);
```

**User Model Casting** (`app/Models/User.php`):
```php
protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',  // ← Auto-hashing on save
];
```

**Key Points:**
- ✅ Passwords are hashed with **bcrypt** (Laravel default, cost factor 10)
- ✅ Password field is **never logged or exposed** (in `$hidden` array)
- ✅ Model casting auto-hashes on `User::create()` or `$user->save()`

### 1.3 Email Verification

**Database Field** (`database/migrations/2025_11_11_104525_create_users_table.php`):
```php
$table->timestamp('email_verified_at')->nullable();
```

**Middleware Check** (`app/Http/Controllers/BookingController.php`):
```php
public function __construct()
{
    $this->middleware(['auth', 'verified']);  // ← Verifies email_verified_at is not null
}
```

**Status:**
- ✅ Email verification field exists (`email_verified_at`)
- ❌ **Email verification logic NOT implemented** (no send email, no verify route)
- ✅ Middleware enforces verification for booking routes
- ⚠️ **Current workaround:** Seeder sets `email_verified_at = now()` for all test users

**What's needed to enable real email verification:**
1. Implement Mailable class for verification email
2. Send verification link on registration
3. Route to verify email token
4. Update user `email_verified_at` when link clicked

### 1.4 Session Management

**Configuration:** `config/auth.php`
```php
'defaults' => [
    'guard' => 'web',           // ← Uses Laravel's default session guard
    'passwords' => 'users',
],

'guards' => [
    'web' => [
        'driver' => 'session',  // ← Session-based authentication
        'provider' => 'users',
    ],
],
```

**Session Storage** (default Laravel):
- Files: `storage/framework/sessions/`
- Cookie name: `XSRF-TOKEN` + `laravel_session`
- CSRF protection: `VerifyCsrfToken` middleware (auto-applied)
- Session timeout: Configured in `.env` (default 120 minutes)

---

## 2. AUTHORIZATION & ACCESS CONTROL

### 2.1 Role-Based Access Control (RBAC)

**Two roles in system:**

| Role | Value | Access |
|------|-------|--------|
| Admin | `'A'` | Admin dashboard, schedule mgmt, user mgmt, payments, gedung/fasilitas config |
| User | `'U'` | Own bookings, payments, public pages |

**Database Schema** (`users` table):
```php
$table->enum('role', ['A', 'U'])->default('U');  // Default: User
```

### 2.2 Middleware: CheckRole

**File:** `app/Http/Middleware/CheckRole.php`
```php
public function handle(Request $request, Closure $next, string $role): Response
{
    if (!auth()->check() || auth()->user()->role !== $role) {
        abort(403, 'Unauthorized action.');  // ← Returns 403 Forbidden
    }
    return $next($request);
}
```

**Usage in Routes** (`routes/web.php`):
```php
Route::middleware(['auth', 'role:A'])->prefix('admin')->name('admin.')->group(function () {
    // Only admin routes (role='A')
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    // ...
});

Route::middleware(['auth'])->group(function () {
    // User routes (any authenticated user)
    Route::prefix('booking')->name('booking.')->group(function () {
        // User booking routes
    });
});
```

### 2.3 Controller-Level Authorization

**Example: Booking Index** (`app/Http/Controllers/BookingController.php`):
```php
public function index()
{
    $query = Booking::with(['user', 'gedung']);
    
    // Non-admin users only see OWN bookings
    if (auth()->user()->role !== 'A') {
        $query->where('user_id', auth()->id());
    }
    
    return view('booking.index', ['items' => $query->latest()->get()]);
}
```

**Authorization Checks:**
- ✅ Users can only view/edit/delete their own bookings
- ✅ Admins can view/manage all bookings
- ✅ Admin-only actions (approve/reject) use middleware
- ✅ Middleware prevents route access (403 abort)

### 2.4 Route Protection

**Protected Routes Example:**

| Route | Middleware | Purpose |
|-------|-----------|---------|
| `/booking/*` | `auth`, `verified` | Only verified users can book |
| `/admin/*` | `auth`, `role:A` | Only admins can access |
| `/auth/login` | `guest` | Only non-logged-in users can access |
| `/payments/upload` | `auth` | Only authenticated users |

---

## 3. DATA VALIDATION

### 3.1 Form Validation Rules

**Booking Creation** (`BookingController::store()`):

```php
$request->validate([
    'gedung_id' => 'required|exists:gedung,id',
    'event_name' => 'required|string|max:255',
    'event_type' => 'required|string|max:100',
    'capacity' => 'required|integer|min:1',
    'phone' => 'required|string|max:30',
    'date' => 'required|date',                               // Must be valid date
    'end_date' => 'nullable|date|after_or_equal:date',      // Can be null or >= start date
    'start_time' => 'required|date_format:H:i',             // 24-hour format
    'end_time' => 'required|date_format:H:i|after:start_time', // Must be after start_time
    'proposal_file' => 'nullable|file|mimes:pdf,doc,docx',  // File validation
    'fasilitas' => 'nullable|array',
    'fasilitas.*.id' => 'required|exists:fasilitas,id',
    'fasilitas.*.jumlah' => 'required|integer|min:1',
]);
```

**Validation Rules Explained:**
- `required` - Field must be provided
- `exists:table,column` - Value must exist in database (foreign key check)
- `date_format:H:i` - Must match time format (hours:minutes)
- `after:field` - Must be after another field value
- `mimes:pdf,doc,docx` - File must be one of specified MIME types
- `array` - Must be array structure

### 3.2 Business Logic Validation

**Schedule Conflict Detection** (`BookingController::store()`):

```php
$overlap = Booking::where('gedung_id', $request->input('gedung_id'))
    ->where('status', '2')  // Only approved bookings block time
    ->where(function($q) use ($startDate, $endDate, $request) {
        $q->where(function($query) use ($startDate, $endDate) {
            // Check if date ranges overlap
            $query->whereBetween('date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($q2) use ($startDate, $endDate) {
                      $q2->where('date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
        })
        ->where(function($timeQuery) use ($request) {
            // Check if time slots overlap (same day)
            $timeQuery->where('start_time', '<', $request->input('end_time'))
                      ->where('end_time', '>', $request->input('start_time'));
        });
    })
    ->exists();

if ($overlap) {
    return back()->withErrors(['date' => 'Jadwal bentrok...']),withInput();
}
```

**Key Validations:**
- ✅ Schedule conflict check (date + time overlap)
- ✅ Only approved (status='2') bookings block availability
- ✅ Date range validation (end_date >= start_date)
- ✅ Time validation (end_time > start_time)

### 3.3 Payment Validation

**Payment Upload** (`PaymentController::uploadProof()`):

```php
$request->validate([
    'proof' => 'required|file|mimes:jpeg,jpg,png,pdf|max:4096',  // 4MB max
    'payment_account_id' => 'required_if:selected_method,!=bayar-ditempat|exists:payment_accounts,id',
    'selected_method' => 'required|in:bayar-ditempat,transfer-bank,e-wallet',
]);
```

### 3.4 Error Display

**Blade Template Error Handling:**

```blade
@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded text-sm text-red-700">
        <strong>Terjadi kesalahan:</strong>
        <ul class="list-disc list-inside mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@error('field_name')
    <p class="text-red-600 text-sm">{{ $message }}</p>
@enderror
```

---

## 4. DATABASE SCHEMA & MODELS

### 4.1 Users Table

**Migration:** `2025_11_11_104525_create_users_table.php`

```php
Schema::create('users', function (Blueprint $table) {
    $table->uuid('id')->primary();                    // UUID primary key
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable(); // Email verification marker
    $table->string('password');                       // Hashed password
    $table->enum('role', ['A', 'U'])->default('U');  // Admin/User role
    $table->rememberToken();
    $table->timestamps();                             // created_at, updated_at
});
```

**Model:** `app/Models/User.php`
```php
class User extends Authenticatable {
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    protected $keyType = 'string';     // UUID is string
    public $incrementing = false;      // UUID not auto-increment
}
```

### 4.2 Bookings Table

**Migration:** `2025_11_11_104652_create_bookings_table.php`

```php
Schema::create('bookings', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('user_id');                          // Foreign key → users
    $table->uuid('gedung_id');                        // Foreign key → gedung
    $table->string('event_name');
    $table->string('event_type');
    $table->integer('capacity');
    $table->date('date');                             // Start date
    $table->time('start_time');                       // Start time
    $table->time('end_time');                         // End time
    $table->date('end_date')->nullable();             // Multi-day bookings
    $table->string('proposal_file')->nullable();      // Uploaded file
    $table->enum('status', ['1', '2', '3', '4'])     // Status codes
        ->default('1');
    // Status codes:
    // '1' = Pending (menunggu)
    // '2' = Approved (disetujui)
    // '3' = Rejected (ditolak)
    // '4' = Completed (selesai)
    $table->text('catatan')->nullable();
    $table->decimal('total_amount', 12, 2)->default(0);
    $table->timestamps();

    $table->foreign('user_id')
        ->references('id')->on('users')
        ->cascadeOnUpdate()->restrictOnDelete();
    $table->foreign('gedung_id')
        ->references('id')->on('gedung')
        ->cascadeOnUpdate()->restrictOnDelete();
});
```

**Additional Migrations:**
- `2025_11_25_041349_add_end_date_to_bookings_table.php` - Added multi-day support
- `2025_11_26_001200_add_phone_to_bookings.php` - Contact information

### 4.3 Payments Table

**Migration:** `2025_11_11_104740_create_payments_table.php`

```php
Schema::create('payments', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('booking_id');                       // Foreign key → bookings
    $table->decimal('amount', 12, 2);                 // Payment amount
    $table->string('method');                         // Payment method
    $table->string('proof_file')->nullable();         // Upload proof/receipt
    $table->char('status', 1)->default('0');         // Payment status
    // Status codes:
    // '0' = Pending (belum diverifikasi)
    // '1' = Verified/Processing (terverifikasi)
    // '2' = Cancelled/Rejected (dibatalkan)
    $table->timestamps();

    $table->foreign('booking_id')
        ->references('id')->on('bookings')
        ->cascadeOnDelete();  // Delete payment if booking deleted
});
```

**Later Addition:**
- `2025_11_25_025820_create_payment_accounts_table.php` - Bank/payment method details

### 4.4 Gedung (Venue) Table

**Schema:**
- `id` (UUID)
- `nama` - Venue name
- `lokasi` - Location
- `harga` - Price per day (decimal)
- `image` - Photo (added later)
- `timestamps`

### 4.5 Fasilitas (Facilities) Table

**Schema:**
- `id` (UUID)
- `nama` - Facility name
- `harga` - Price per unit
- `image` - Photo
- `timestamps`

### 4.6 BookingFasilitas (Booking-Facility Join Table)

**Purpose:** Many-to-many relationship between bookings and facilities with quantity

```php
$table->uuid('booking_id');
$table->uuid('fasilitas_id');
$table->integer('jumlah');  // Quantity
$table->timestamps();
```

### 4.7 Model Relationships

**User Model:**
```php
public function bookings()
{
    return $this->hasMany(Booking::class);
}
```

**Booking Model:**
```php
public function user()
{
    return $this->belongsTo(User::class);
}

public function gedung()
{
    return $this->belongsTo(Gedung::class);
}

public function bookingFasilitas()
{
    return $this->hasMany(BookingFasilitas::class);
}

public function payment()
{
    return $this->hasOne(Payment::class);
}
```

**BookingFasilitas Model:**
```php
public function booking()
{
    return $this->belongsTo(Booking::class);
}

public function fasilitas()
{
    return $this->belongsTo(Fasilitas::class);
}
```

---

## 5. BUSINESS LOGIC & WORKFLOWS

### 5.1 Booking Creation Flow

**Step-by-step process:**

1. **User accesses booking form** → `/booking/create`
   - Controller: `BookingController::create()`
   - Loads available gedung and fasilitas
   - Checks `auth`, `verified` middleware

2. **User submits form** → `POST /booking`
   - Controller: `BookingController::store()`
   - **Validation:**
     - Check all required fields (form validation)
     - Verify gedung exists in database
     - Verify fasilitas exists (if selected)
     - Check date/time format
     - **Schedule conflict check:** Is this time slot available?
   - **On validation fail:** Return to form with errors + user input

3. **Create Booking record:**
   ```php
   $booking = Booking::create([
       'user_id' => Auth::id(),
       'gedung_id' => $request->gedung_id,
       'event_name' => $request->event_name,
       'status' => '1',  // Pending
       // ... other fields
   ]);
   ```

4. **Add selected fasilitas:**
   ```php
   foreach ($request->fasilitas as $item) {
       BookingFasilitas::create([
           'booking_id' => $booking->id,
           'fasilitas_id' => $item['id'],
           'jumlah' => $item['jumlah'],
       ]);
   }
   ```

5. **Calculate total amount:**
   - If `gedung->harga` exists: `amount = (gedung_price × days) + facilities_total`
   - Else: `amount = (500,000 × hours × days) + facilities_total`

6. **Create Payment record:**
   ```php
   Payment::create([
       'booking_id' => $booking->id,
       'amount' => $calculated_amount,
       'status' => '0',  // Pending
   ]);
   ```

7. **Redirect to payment upload** → `/payments/{id}/upload`

**Status Flow:**
- User books → Booking status = `'1'` (Pending)
- User uploads payment proof → Payment status = `'0'` (Pending verification)
- Admin verifies payment → Payment status = `'1'` (Verified) → Booking status = `'2'` (Approved)

### 5.2 Admin Booking Creation

**Different flow (admin creates on behalf of user):**

1. Admin accesses `/admin/booking/create`
2. Admin selects:
   - User (pemesan)
   - Gedung
   - Event details
   - Fasilitas (optional)
   - Status (can set to pending/approved immediately)
3. Creates booking directly with admin validation
4. Booking created with user_id specified by admin

**Controller:** `AdminController::bookingCreate()` and `bookingStore()`

### 5.3 Payment Workflow

**Steps:**

1. **User uploads proof** → `POST /payments/{id}/upload`
   - File validation (JPG, PNG, PDF, max 4MB)
   - Stores file to `storage/app/public/payments/`
   - Payment status remains `'0'` (pending verification)

2. **Admin verifies payment** → `/admin/payments`
   - Admin can see all pending payments
   - Admin clicks "Verify" button
   - Updates Payment status to `'1'` (verified)
   - System updates Booking status to `'2'` (approved)

3. **Payment confirmed**
   - Booking appears as confirmed to user
   - Gedung calendar shows as booked (blocks other users)

**Payment Statuses:**
- `'0'` = Pending (bukti sudah diupload, menunggu verifikasi admin)
- `'1'` = Verified/Processing (pembayaran terkonfirmasi)
- `'2'` = Cancelled/Rejected (pembayaran ditolak)

### 5.4 Admin Booking Management

**Approve/Reject Pending Booking:**

```php
// AdminController::approveBooking()
public function approveBooking($id)
{
    $booking = Booking::findOrFail($id);
    if ($booking->status !== '1') {
        return redirect()->back()->with('error', 'Only pending bookings can be approved.');
    }
    $booking->update(['status' => '2']);
    return redirect()->back()->with('success', 'Booking approved.');
}

// AdminController::rejectBooking()
public function rejectBooking($id)
{
    $booking = Booking::findOrFail($id);
    if ($booking->status !== '1') {
        return redirect()->back()->with('error', 'Only pending bookings can be rejected.');
    }
    $booking->update(['status' => '3']);
    return redirect()->back()->with('success', 'Booking rejected.');
}
```

**Edit Booking (Admin):**
- `AdminController::bookingEdit()` - Show edit form
- `AdminController::bookingUpdate()` - Save changes
- Can update user, gedung, dates, times, fasilitas, status
- Triggers schedule validation again

**Delete Booking (Admin):**
- `AdminController::bookingDestroy()`
- Deletes booking + related bookingFasilitas + payment records
- Redirects to admin schedules

---

## 6. SECURITY FEATURES & GAPS

### ✅ Implemented Security

| Feature | Location | Status |
|---------|----------|--------|
| Password hashing (bcrypt) | `AuthController::register()` | ✅ Secure |
| CSRF protection | `VerifyCsrfToken` middleware | ✅ Enabled (all forms) |
| SQL injection prevention | Laravel ORM (Eloquent) | ✅ Parameterized queries |
| Session fixation prevention | `$request->session()->regenerate()` | ✅ Regenerate on login |
| Role-based access control | `CheckRole` middleware | ✅ Admin routes protected |
| User data isolation | Controller-level checks | ✅ Users see only own bookings |
| XSS prevention | Blade template escaping `{{ }}` | ✅ Auto-escape by default |
| File upload validation | MIME type + size checks | ✅ PDF/DOC/JPG/PNG only |

### ❌ Security Gaps

| Gap | Risk | Recommendation |
|-----|------|-----------------|
| No email verification emails sent | Unverified users can book | Implement `Mailable` + send verify email on register |
| No password reset link | Users stuck if password lost | Add `ResetPassword` Mailable + reset routes |
| No 2FA (two-factor auth) | Account takeover risk | Add TOTP via `laravel/fortify` |
| No rate limiting on login | Brute force attacks possible | Add `throttle:6,1` middleware on login route |
| No audit logging | No trace of admin actions | Add audit log table, log all model changes |
| No request validation on API | If APIs exist, vulnerable | Validate all API inputs |
| Limited error handling | May expose DB structure | Catch exceptions, return generic errors |

---

## 7. ENVIRONMENT & CONFIGURATION

### 7.1 .env Variables (Key Authentication)

```
APP_NAME="Boogsgunila"
APP_DEBUG=false                    # Set to false in production
APP_URL=http://localhost

DB_CONNECTION=pgsql               # PostgreSQL
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=boogsgunila
DB_USERNAME=postgres
DB_PASSWORD=...

SESSION_DRIVER=file               # Or redis, database
SESSION_LIFETIME=120              # Minutes
SESSION_SECURE_COOKIES=true       # For HTTPS only
SESSION_HTTP_ONLY=true            # JS can't access (prevents XSS)
SESSION_SAME_SITE=lax            # CSRF protection

MAIL_DRIVER=smtp                  # For email verification (if enabled)
```

### 7.2 Key Middleware Stack

**Applied to all routes:**
```php
\App\Http\Middleware\EncryptCookies::class,
\App\Http\Middleware\VerifyCsrfToken::class,
\Illuminate\Auth\Middleware\Authenticate::class,
```

**Custom Middleware:**
- `CheckRole::class` - Verify user role before proceeding
- `PreventBackHistory::class` - Prevent accessing previous pages after logout

### 7.3 Route Middleware Groups

```php
// Guest only (not authenticated)
Route::middleware('guest')->group(function () {
    Route::get('/login', ...);
    Route::post('/login', ...);
    Route::get('/register', ...);
    Route::post('/register', ...);
});

// Authenticated users (any role)
Route::middleware(['auth'])->group(function () {
    Route::get('/booking', ...);
    Route::post('/booking', ...);
});

// Verified users (email_verified_at not null)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/booking/create', ...);
});

// Admin only (role = 'A')
Route::middleware(['auth', 'role:A'])->prefix('admin')->group(function () {
    Route::get('/dashboard', ...);
    Route::resource('users', ...);
    Route::resource('booking', ...);
});
```

---

## 8. KEY CONSTANTS & STATUS CODES

### Booking Status
```
'1' = Pending/Menunggu (user just created, waiting for admin approval or payment verification)
'2' = Approved/Disetujui (payment verified, booking confirmed, blocks calendar)
'3' = Rejected/Ditolak (admin rejected or user cancelled)
'4' = Completed/Selesai (event happened, past date)
```

### Payment Status
```
'0' = Pending (user uploaded proof, waiting for admin verification)
'1' = Verified/Processing (admin confirmed, booking approved)
'2' = Cancelled/Rejected (payment rejected, user needs to resubmit)
```

### User Roles
```
'A' = Admin (full system access)
'U' = User (can only manage own bookings)
```

### Booking Calculation Constants
```
Base hourly rate (if no gedung.harga): 500,000 per hour per day
Facility prices: Stored in fasilitas.harga
Total formula: (gedung_price × days) + (facility_price × quantity × days)
```

---

## 9. DATA FLOW DIAGRAMS

### Registration & Login Flow
```
User Registration:
  ↓
  Form: name, email, password, password_confirm
  ↓
  Validate: email unique, password >= 8 chars, password matches
  ↓
  Hash password with bcrypt
  ↓
  Create user (role='U' default)
  ↓
  Auto-login user
  ↓
  Redirect to /booking

User Login:
  ↓
  Form: email, password
  ↓
  Auth::attempt() - verify email + hash password
  ↓
  Session::regenerate() - prevent fixation
  ↓
  Check user role
    ├─ role='A' → admin.dashboard
    └─ role='U' → booking.index
```

### Booking Creation Flow
```
User Booking:
  ↓
  /booking/create - select gedung, dates, times, facilities
  ↓
  POST /booking - BookingController::store()
  ↓
  Validate form + schedule conflict check
  ↓
  Create Booking (status='1')
  ↓
  Create BookingFasilitas entries
  ↓
  Calculate total = (gedung_price × days) + facilities
  ↓
  Create Payment (status='0')
  ↓
  Redirect to /payments/{id}/upload

Payment Upload:
  ↓
  /payments/{id}/upload - upload proof file
  ↓
  POST /payments/{id}/upload - PaymentController::uploadProof()
  ↓
  Validate file + form
  ↓
  Store file to storage
  ↓
  Payment remains status='0' (pending admin verification)
  ↓
  Email admin notification (if configured)

Admin Verification:
  ↓
  /admin/payments - list pending payments
  ↓
  Admin clicks "Verify" or "Reject"
  ↓
  PUT /admin/payments/{id}/status - PaymentController::adminMark()
  ↓
  Update Payment status to '1' (verified) or '2' (rejected)
  ↓
  If verified: Update Booking status to '2' (approved)
  ↓
  User sees confirmed booking in dashboard
```

### Admin Booking Management
```
Admin Schedule View:
  ↓
  /admin/schedules - list all bookings
  ↓
  For each booking:
    ├─ Edit button → /admin/booking/{id}/edit
    ├─ Delete button → DELETE /admin/booking/{id}
    ├─ Detail button → /admin/booking/{id}/invoice
    └─ Approve/Reject (if status='1')

Admin Edit Booking:
  ↓
  /admin/booking/{id}/edit - form with current values
  ↓
  Update: user, gedung, dates, times, facilities, status
  ↓
  PUT /admin/booking/{id} - AdminController::bookingUpdate()
  ↓
  Schedule conflict validation runs again
  ↓
  Redirect to /admin/schedules with success message
```

---

## 10. QUICK CHECKLIST FOR DEVELOPERS

### Before Deployment
- [ ] Set `APP_DEBUG=false` in production
- [ ] Set `SESSION_SECURE_COOKIES=true` (HTTPS only)
- [ ] Configure real SMTP for email verification
- [ ] Set up `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME`
- [ ] Create `.env` from `.env.example` with real DB credentials
- [ ] Run `php artisan key:generate` (app encryption key)
- [ ] Run `php artisan migrate` (create database tables)
- [ ] Run `php artisan storage:link` (create public symlink for uploads)
- [ ] Set file permissions: `storage/` and `bootstrap/cache/` writable by web server
- [ ] Enable HTTPS (SSL certificate) - required for secure cookies
- [ ] Configure web server (Nginx/Apache) to serve `public/` as root

### Debugging Tips
- Check logs: `storage/logs/laravel.log`
- Debug mode: `.env` `APP_DEBUG=true` (dev only, shows errors)
- Test routes: `php artisan route:list`
- Test database: `php artisan tinker` → `User::count()`
- Clear cache: `php artisan config:clear && php artisan cache:clear`

### Common Issues
| Issue | Cause | Fix |
|-------|-------|-----|
| "email_verified_at" error on booking | Middleware `verified` fails | Seeder sets `email_verified_at=now()` or skip middleware |
| "Unauthorized" 403 errors | Role check failed | Verify user `role` in database, check middleware |
| Session not persisting | Session driver misconfigured | Check `config/session.php`, verify `storage/framework/sessions/` writable |
| File uploads fail | Storage permissions | Run `php artisan storage:link`, check directory permissions |
| Schedule conflicts not working | Date logic error | Debug conflict query, check date format (YYYY-MM-DD) |

---

## 11. ADDITIONAL RESOURCES

**Laravel Documentation:**
- Auth: https://laravel.com/docs/10.x/authentication
- Authorization: https://laravel.com/docs/10.x/authorization
- Middleware: https://laravel.com/docs/10.x/middleware
- Validation: https://laravel.com/docs/10.x/validation
- Security: https://laravel.com/docs/10.x/security

**Database:**
- PostgreSQL docs: https://www.postgresql.org/docs/
- Laravel migrations: https://laravel.com/docs/10.x/migrations
- Eloquent ORM: https://laravel.com/docs/10.x/eloquent

---

**Document Version:** 1.0  
**Last Updated:** November 27, 2025  
**Maintained By:** Development Team
