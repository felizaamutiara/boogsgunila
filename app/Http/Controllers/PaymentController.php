<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\Booking;

class PaymentController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('role:A')->only(['adminIndex', 'adminMark']);
	}

	public function adminIndex()
	{
		$req = request();
		$query = Payment::with(['booking' => function ($q) { $q->with('user','gedung'); }])->latest();
		if ($q = $req->input('q')) {
			$query->where(function($qq) use ($q) {
				$qq->where('amount', 'like', "%$q%")
				   ->orWhere('status', 'like', "%$q%");
			})
			->orWhereHas('booking', function($b) use ($q) {
				$b->where('event_name', 'like', "%$q%")
				  ->orWhereHas('user', function($u) use ($q){ $u->where('name', 'like', "%$q%"); });
			});
		}
		$payments = $query->get();
		return view('admin.payments', [
			'title' => 'Verifikasi Pembayaran',
			'payments' => $payments,
			'q' => $q ?? null,
		]);
	}

	public function index(Request $request)
	{
		// status: 0 pending, 1 processing, 2 success, 3 canceled
		$statusFilter = $request->input('status');

		$query = Payment::with(['booking' => function ($q) {
			$q->with('gedung');
		}])->whereHas('booking', function ($q) {
			$q->where('user_id', Auth::id());
		});

		if ($statusFilter !== null && $statusFilter !== '') {
			$query->where('status', $statusFilter);
		}

		$payments = $query->latest()->get();

		return view('payments.index', [
			'title' => 'Pembayaran',
			'payments' => $payments,
			'active' => (string)($statusFilter ?? ''),
		]);
	}

	public function showUploadForm($paymentId)
	{
		$payment = Payment::with('booking')->where('id', $paymentId)
			->whereHas('booking', function ($q) {
				$q->where('user_id', Auth::id());
			})->firstOrFail();

		$booking = $payment->booking;
		$paymentAccounts = \App\Models\PaymentAccount::where('is_active', true)->orderBy('type')->orderBy('name')->get();

		return view('payments.upload', [
			'title' => 'Upload Bukti Pembayaran',
			'payment' => $payment,
			'booking' => $booking,
			'paymentAccounts' => $paymentAccounts,
		]);
	}

	public function uploadProof(Request $request, $paymentId)
	{
		$request->validate([
			'proof' => 'required_if:selected_method,transfer-bank,e-wallet|file|mimes:jpg,jpeg,png,pdf|max:4096',
			'selected_method' => 'required|in:bayar-ditempat,transfer-bank,e-wallet',
			'payment_account_id' => 'required_if:selected_method,transfer-bank,e-wallet|nullable|exists:payment_accounts,id',
		]);

		$payment = Payment::with('booking')->where('id', $paymentId)
			->whereHas('booking', function ($q) {
				$q->where('user_id', Auth::id());
			})->firstOrFail();

		$path = null;
		if ($request->hasFile('proof')) {
			$path = $request->file('proof')->store('payment_proofs', 'public');
		}
		
		$paymentAccountNumber = null;
		if ($request->filled('payment_account_id')) {
			$paymentAccount = \App\Models\PaymentAccount::find($request->payment_account_id);
			$paymentAccountNumber = $paymentAccount ? $paymentAccount->account_number : null;
		}

		$updateData = [
			'method' => 'manual-transfer',
			'selected_method' => $request->selected_method,
			'payment_account_number' => $paymentAccountNumber,
		];

		// Untuk bayar ditempat, tidak perlu upload bukti, langsung status 1 (menunggu verifikasi admin)
		// Untuk transfer/e-wallet, perlu upload bukti
		if ($request->selected_method === 'bayar-ditempat') {
			$updateData['status'] = '1'; // processing - menunggu admin verifikasi di tempat
		} else {
			if ($path) {
				$updateData['proof_file'] = $path;
			}
			$updateData['status'] = '1'; // processing
		}

		$payment->update($updateData);

		return redirect()->route('payments.index')->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
	}

	// Admin only quick status updates
	public function adminMark(Request $request, $paymentId)
	{
		$request->validate([
			'status' => 'required|in:1,2,3',
		]);
		$payment = Payment::with('booking')->findOrFail($paymentId);
		$newStatus = $request->input('status');
		
		$payment->update(['status' => $newStatus]);
		
		// If payment is verified (status = 2), automatically approve the booking
		if ($newStatus === '2' && $payment->booking) {
			$payment->booking->update(['status' => '2']); // 2 = approved
		}
		
		return back()->with('success', 'Status pembayaran diperbarui.');
	}
}



