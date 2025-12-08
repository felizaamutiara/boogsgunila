<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Fasilitas;
use App\Models\Gedung;
use App\Models\Payment;

class AdminController extends Controller
{
	public function __construct()
	{
		$this->middleware(['auth', 'role:A']);
	}

	public function index()
	{
		// Ringkasan booking terbaru
		$recentBookings = Booking::with(['user', 'gedung'])
			->latest('created_at')
			->limit(5)
			->get();

		// Data fasilitas lengkap dengan harga
		$fasilitas = Fasilitas::orderBy('nama')->get();

		return view('admin.dashboard', [
			'title' => 'Admin Dashboard',
			'stats' => [
				'users' => \App\Models\User::count(),
				'bookings' => Booking::count(),
				'gedung' => Gedung::count(),
				'fasilitas' => Fasilitas::count(),
				'payments_pending' => Payment::where('status', '1')->count(),
				'active_rentals' => Booking::where('status', '2')->count(),
				'pending_approval' => Booking::where('status', '1')->count(),
				'rejected_bookings' => Booking::where('status', '3')->count(),
			],
			'recentBookings' => $recentBookings,
			'fasilitas' => $fasilitas,
		]);
	}

	public function usersIndex()
	{
		$req = request();
		$query = \App\Models\User::orderBy('name');
		if ($q = $req->input('q')) {
			$query->where(function($qq) use ($q) {
				$qq->where('name', 'like', "%$q%")
				   ->orWhere('email', 'like', "%$q%")
				   ->orWhere('phone', 'like', "%$q%");
			});
		}
		$users = $query->get();
		return view('admin.users', ['title' => 'Data Pengguna', 'users' => $users, 'q' => $q ?? null]);
	}

	public function usersCreate()
	{
		return view('admin.users_create', ['title' => 'Tambah Pengguna']);
	}

	public function usersStore(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|email|unique:users,email',
			'password' => 'required|string|min:6',
			'role' => 'required|in:A,U',
			'phone' => 'nullable|string|max:30',
		]);

		$user = \App\Models\User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => \Illuminate\Support\Facades\Hash::make($request->password),
			'role' => $request->role,
			'phone' => $request->phone,
			'email_verified_at' => now(),
		]);

		return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan. User dapat langsung login tanpa OTP.');
	}

	public function usersEdit($id)
	{
		$user = \App\Models\User::findOrFail($id);
		return view('admin.users_edit', ['title' => 'Edit Pengguna', 'user' => $user]);
	}

	public function usersUpdate(Request $request, $id)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|email|unique:users,email,' . $id,
			'password' => 'nullable|string|min:6',
			'role' => 'required|in:A,U',
			'phone' => 'nullable|string|max:30',
		]);

		$user = \App\Models\User::findOrFail($id);
		$data = [
			'name' => $request->name,
			'email' => $request->email,
			'role' => $request->role,
		];

		if ($request->filled('password')) {
			$data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
		}

		$data['phone'] = $request->phone;

		$user->update($data);

		return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
	}

	public function usersDestroy($id)
	{
		$user = \App\Models\User::findOrFail($id);
		// Prevent deleting yourself
		if ($user->id === auth()->id()) {
			return redirect()->route('admin.users.index')->with('error', 'Tidak dapat menghapus akun sendiri.');
		}

		// Check if the user has any bookings. If so, return a friendly error instead of letting a DB exception happen.
		$hasBookings = Booking::where('user_id', $user->id)->exists();
		if ($hasBookings) {
			return redirect()->route('admin.users.index')->with('error', 'Tidak dapat menghapus pengguna karena masih memiliki booking. Hapus atau batalkan booking terlebih dahulu.');
		}

		try {
			$user->delete();
		} catch (\Exception $e) {
			// Log the exception for debugging and return a friendly message
			\Log::error('Error deleting user '.$user->id.': '.$e->getMessage());
			return redirect()->route('admin.users.index')->with('error', 'Terjadi kesalahan saat menghapus pengguna. Silakan coba lagi atau hubungi admin.');
		}

		return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
	}

	public function schedulesIndex()
	{
		$req = request();
		$query = Booking::with(['user','gedung','bookingFasilitas.fasilitas'])->latest();
		if ($q = $req->input('q')) {
			$query->where(function($qq) use ($q) {
				$qq->where('event_name', 'like', "%$q%")
				   ->orWhere('event_type', 'like', "%$q%")
				   ->orWhere('date', 'like', "%$q%");
			})
			->orWhereHas('user', function($u) use ($q) { $u->where('name', 'like', "%$q%"); })
			->orWhereHas('gedung', function($g) use ($q) { $g->where('nama', 'like', "%$q%"); });
		}
		$items = $query->get();
		return view('admin.schedules', ['title' => 'Data Jadwal', 'items' => $items, 'q' => $q ?? null]);
	}

	public function rentalsIndex()
	{
		$req = request();
		$query = Booking::with(['user','gedung','bookingFasilitas.fasilitas'])->whereIn('status',['1','2'])->latest();
		if ($q = $req->input('q')) {
			$query->where(function($qq) use ($q) {
				$qq->where('event_name', 'like', "%$q%")
				   ->orWhere('event_type', 'like', "%$q%")
				   ->orWhere('date', 'like', "%$q%");
			})
			->orWhereHas('user', function($u) use ($q) { $u->where('name', 'like', "%$q%"); })
			->orWhereHas('gedung', function($g) use ($q) { $g->where('nama', 'like', "%$q%"); });
		}
		$items = $query->get();
		return view('admin.rentals', ['title' => 'Detail Sewa', 'items' => $items, 'q' => $q ?? null]);
	}

	public function bookingCreate()
	{
		$users = \App\Models\User::orderBy('name')->get();
		$gedung = \App\Models\Gedung::orderBy('nama')->get();
		return view('admin.create_booking', [
			'title' => 'Tambah Jadwal',
			'users' => $users,
			'gedung' => $gedung,
		]);
	}

	public function bookingEdit($id)
	{
		$booking = Booking::with('bookingFasilitas.fasilitas')->findOrFail($id);
		$gedung = \App\Models\Gedung::orderBy('nama')->get();
		$fasilitas = \App\Models\Fasilitas::orderBy('nama')->get();
		return view('admin.edit_booking', [
			'title' => 'Edit Booking (Admin)',
			'item' => $booking,
			'gedung' => $gedung,
			'fasilitas' => $fasilitas,
		]);
	}

	public function bookingUpdate(Request $request, $id)
	{
		// reuse BookingController update-like logic but allow admin
		$request->validate([
			'gedung_id' => 'required|exists:gedung,id',
			'event_name' => 'required|string|max:255',
			'event_type' => 'required|string|max:100',
			'capacity' => 'required|integer|min:1',
			'phone' => 'required|string|max:30',
			'date' => 'required|date',
			'end_date' => 'nullable|date|after_or_equal:date',
			'start_time' => 'required|regex:/^\d{1,2}:\d{2}(:\d{2})?$/',
			'end_time' => 'required|regex:/^\d{1,2}:\d{2}(:\d{2})?$/',
			'fasilitas' => 'nullable|array',
			'fasilitas.*.id' => 'required_with:fasilitas|exists:fasilitas,id',
			'fasilitas.*.jumlah' => 'required_with:fasilitas|integer|min:1',
			'status' => 'required|in:1,2,3,4',
		], [
			'start_time.regex' => 'Jam mulai tidak valid',
			'end_time.regex' => 'Jam selesai tidak valid',
		]);

		$booking = Booking::findOrFail($id);

		$updateData = $request->only(['gedung_id','event_name','event_type','capacity','phone','date','end_date','start_time','end_time']);
		$updateData['status'] = $request->input('status');

		// handle proposal file if uploaded
		if ($request->hasFile('proposal_file')) {
			$filePath = $request->file('proposal_file')->store('proposals', 'public');
			$updateData['proposal_file'] = $filePath;
		}

		$oldStatus = $booking->status;

		$booking->update($updateData);

		// Sinkronisasi status pembayaran jika booking status berubah
		if (isset($updateData['status']) && $oldStatus !== $updateData['status']) {
			$newBookingStatus = $updateData['status'];
			
			// Jika booking di-approve (status 2), mark payment sebagai verified
			if ($newBookingStatus === '2') {
				$payment = \App\Models\Payment::where('booking_id', $booking->id)->first();
				if ($payment) {
					$payment->update(['status' => '2']); // 2 = verified
				}
			}
			// Jika booking di-reject (status 3), mark payment sebagai rejected
			else if ($newBookingStatus === '3') {
				$payment = \App\Models\Payment::where('booking_id', $booking->id)->first();
				if ($payment) {
					$payment->update(['status' => '3']); // 3 = rejected
				}
			}
		}

		// Prepare friendly message if status changed
		$statusLabels = ['1' => 'Menunggu', '2' => 'Disetujui', '3' => 'Ditolak', '4' => 'Selesai'];
		if (isset($updateData['status']) && $oldStatus !== $updateData['status']) {
			$oldLabel = $statusLabels[$oldStatus] ?? $oldStatus;
			$newLabel = $statusLabels[$updateData['status']] ?? $updateData['status'];
			$msg = "Status booking \"{$booking->event_name}\" diubah dari {$oldLabel} menjadi {$newLabel}.";
			return redirect()->route('admin.schedules.index')->with('success', $msg);
		}

		// Update fasilitas
		$booking->bookingFasilitas()->delete();
		foreach ($request->input('fasilitas', []) as $item) {
			\App\Models\BookingFasilitas::create([
				'booking_id' => $booking->id,
				'fasilitas_id' => $item['id'],
				'jumlah' => $item['jumlah'] ?? 1,
			]);
		}

		return redirect()->route('admin.schedules.index')->with('success', 'Booking berhasil diperbarui oleh admin.');
	}

	public function bookingStore(\Illuminate\Http\Request $request)
	{
		$request->validate([
			'user_id' => 'required|exists:users,id',
			'gedung_id' => 'required|exists:gedung,id',
			'event_name' => 'required|string|max:255',
			'event_type' => 'required|string|max:100',
			'capacity' => 'required|integer|min:1',
			'phone' => 'required|string|max:30',
			'date' => 'required|date',
			'end_date' => 'nullable|date|after_or_equal:date',
			'start_time' => 'required|date_format:H:i',
			'end_time' => 'required|date_format:H:i|after:start_time',
			'proposal_file' => 'nullable|file|mimes:pdf,doc,docx',
		]);

		$filePath = null;
		if ($request->hasFile('proposal_file')) {
			$filePath = $request->file('proposal_file')->store('proposals', 'public');
		}

		$booking = Booking::create([
			'user_id' => $request->user_id,
			'gedung_id' => $request->gedung_id,
			'event_name' => $request->event_name,
			'event_type' => $request->event_type,
			'capacity' => $request->capacity,
			'phone' => $request->phone,
			'date' => $request->date,
			'end_date' => $request->end_date,
			'start_time' => $request->start_time,
			'end_time' => $request->end_time,
			'proposal_file' => $filePath,
			'status' => '1',
		]);

		return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan.');
	}

	public function bookingDestroy($id)
	{
		$booking = Booking::findOrFail($id);

		// remove related fasilitas and payments if any
		try {
			$booking->bookingFasilitas()->delete();
		} catch (\Exception $e) {
			// ignore if relation missing
		}
		try {
			Payment::where('booking_id', $id)->delete();
		} catch (\Exception $e) {
			// ignore
		}

		$booking->delete();

		return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus.');
	}

	public function bookingInvoice($id)
	{
		$booking = Booking::with(['user', 'gedung', 'bookingFasilitas.fasilitas'])->findOrFail($id);

		$payment = Payment::where('booking_id', $id)->first();
		if (!$payment) {
			// calculate amount similar to BookingController
			$start = strtotime($booking->start_time);
			$end = strtotime($booking->end_time);
			$hours = max(1, ceil(($end - $start) / 3600));

			$facilitiesTotal = 0;
			foreach ($booking->bookingFasilitas as $bf) {
				$facilitiesTotal += ($bf->fasilitas->harga ?? 0) * $bf->jumlah;
			}

			$startDate = strtotime($booking->date);
			$endDate = strtotime($booking->end_date ?? $booking->date);
			$days = (int) floor(($endDate - $startDate) / 86400) + 1;
			if ($days < 1) { $days = 1; }

			$gedungModel = $booking->gedung;
			if ($gedungModel && !empty($gedungModel->harga) && $gedungModel->harga > 0) {
				$amount = ($gedungModel->harga * $days) + $facilitiesTotal;
			} else {
				// fallback hourly rate (same default as BookingController)
				$amount = ($hours * 500000 * $days) + $facilitiesTotal;
			}

			$payment = Payment::create([
				'booking_id' => $booking->id,
				'amount' => $amount,
				'method' => 'pending',
				'proof_file' => null,
				'status' => '0',
			]);
		}

		$paymentAccounts = \App\Models\PaymentAccount::where('is_active', true)->orderBy('type')->orderBy('name')->get();
		return view('admin.booking_invoice', [
			'title' => 'Invoice Booking',
			'booking' => $booking,
			'payment' => $payment,
			'paymentAccounts' => $paymentAccounts,
		]);
	}

	public function approveBooking($id)
	{
		$booking = Booking::findOrFail($id);
		
		// Validasi: hanya status '1' (pending) yang bisa disetujui
		if ($booking->status !== '1') {
			return redirect()->back()->with('error', 'Hanya booking dengan status Menunggu yang dapat disetujui.');
		}

		$booking->update(['status' => '2']);
		
		return redirect()->back()->with('success', 'Booking "' . $booking->event_name . '" berhasil disetujui.');
	}

	public function rejectBooking($id)
	{
		$booking = Booking::findOrFail($id);
		
		// Validasi: hanya status '1' (pending) yang bisa ditolak
		if ($booking->status !== '1') {
			return redirect()->back()->with('error', 'Hanya booking dengan status Menunggu yang dapat ditolak.');
		}

		$booking->update(['status' => '3']);
		
		return redirect()->back()->with('success', 'Booking "' . $booking->event_name . '" berhasil ditolak.');
	}

	// API endpoint untuk get status booking (untuk dynamic update di schedules)
	public function apiGetStatus($id)
	{
		$booking = Booking::findOrFail($id);
		$payment = Payment::where('booking_id', $id)->first();

		return response()->json([
			'status' => $booking->status,
			'payment_status' => $payment ? $payment->status : null,
		]);
	}

	// API endpoint untuk delete booking (untuk AJAX delete di schedules)
	public function apiDeleteBooking($id)
	{
		try {
			$booking = Booking::findOrFail($id);

			// Authorize - only admin
			if (auth()->user()->role !== 'A') {
				return response()->json(['message' => 'Unauthorized'], 403);
			}

			$booking->delete();

			return response()->json([
				'success' => true,
				'message' => 'Jadwal berhasil dihapus.',
			]);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'Gagal menghapus jadwal: ' . $e->getMessage(),
			], 500);
		}
	}

	// ========== PAYMENT ACCOUNT MANAGEMENT ==========
	
	public function paymentAccountsIndex()
	{
		$accounts = \App\Models\PaymentAccount::orderBy('type')->orderBy('name')->get();
		return view('admin.payment_accounts.index', [
			'title' => 'Kelola Rekening Pembayaran',
			'accounts' => $accounts,
		]);
	}

	public function paymentAccountsCreate()
	{
		return view('admin.payment_accounts.create', [
			'title' => 'Tambah Rekening Pembayaran',
		]);
	}

	public function paymentAccountsStore(Request $request)
	{
		$data = $request->validate([
			'type' => 'required|in:bayar-ditempat,transfer-bank,e-wallet',
			'name' => 'required|string|max:255',
			'account_number' => 'required|string|max:255',
			'account_name' => 'required|string|max:255',
			'description' => 'nullable|string|max:500',
			'is_active' => 'boolean',
		]);

		\App\Models\PaymentAccount::create($data);

		return redirect()->route('admin.payment_accounts.index')
			->with('success', 'Rekening pembayaran berhasil ditambahkan.');
	}

	public function paymentAccountsEdit($id)
	{
		$account = \App\Models\PaymentAccount::findOrFail($id);
		return view('admin.payment_accounts.edit', [
			'title' => 'Edit Rekening Pembayaran',
			'account' => $account,
		]);
	}

	public function paymentAccountsUpdate(Request $request, $id)
	{
		$account = \App\Models\PaymentAccount::findOrFail($id);
		
		$data = $request->validate([
			'type' => 'required|in:bayar-ditempat,transfer-bank,e-wallet',
			'name' => 'required|string|max:255',
			'account_number' => 'required|string|max:255',
			'account_name' => 'required|string|max:255',
			'description' => 'nullable|string|max:500',
			'is_active' => 'boolean',
		]);

		$account->update($data);

		return redirect()->route('admin.payment_accounts.index')
			->with('success', 'Rekening pembayaran berhasil diperbarui.');
	}

	public function paymentAccountsDestroy($id)
	{
		$account = \App\Models\PaymentAccount::findOrFail($id);
		$account->delete();

		return redirect()->route('admin.payment_accounts.index')
			->with('success', 'Rekening pembayaran berhasil dihapus.');
	}
}

