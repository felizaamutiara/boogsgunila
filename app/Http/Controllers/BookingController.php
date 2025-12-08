<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use App\Models\Fasilitas;
use App\Models\BookingFasilitas;
use App\Models\Gedung;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }
    private const BASE_RATE_PER_HOUR = 500000; 
    public function index()
    {
        $query = Booking::with(['user', 'gedung']);
        
        // If user is not admin, only show their own bookings
        if (auth()->user()->role !== 'A') {
            $query->where('user_id', auth()->id());
        }
        
        $data = [
            'title' => 'Booking Saya',
            'items' => $query->latest()->get(),
        ];
        return view('booking.index', $data);
    }

    public function create()
    {
        $gedung = Gedung::orderBy('nama')->get();
        $fasilitas = Fasilitas::orderBy('nama')->get();
        
        if ($gedung->count() === 0) {
            return redirect()->route('booking.index')
                ->with('warning', 'Belum ada gedung tersedia. Silakan hubungi admin untuk menambahkan gedung terlebih dahulu.');
        }
        
        return view('create_booking', [
            'title' => 'Buat Booking Gedung',
            'gedung' => $gedung,
            'fasilitas' => $fasilitas,
        ]);
    }

    public function store(Request $request)
    {
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
            'proposal_file' => 'nullable|file|mimes:pdf,doc,docx',
            'fasilitas' => 'nullable|array',
            'fasilitas.*.id' => 'required|exists:fasilitas,id',
            'fasilitas.*.jumlah' => 'required|integer|min:1',
        ]);

        // Validasi stock fasilitas
        $fasilitasItems = $request->input('fasilitas', []);
        foreach ($fasilitasItems as $item) {
            $fasilitas = Fasilitas::find($item['id']);
            if (!$fasilitas) {
                continue;
            }
            
            // Check if stock is empty or zero
            if (!$fasilitas->stok || $fasilitas->stok <= 0) {
                return back()
                    ->withErrors(['fasilitas.*.id' => "Stok fasilitas '{$fasilitas->nama}' kosong, tidak dapat dipilih."])
                    ->withInput();
            }
            
            // Check if requested quantity exceeds stock
            $jumlah = $item['jumlah'] ?? 1;
            if ($jumlah > $fasilitas->stok) {
                return back()
                    ->withErrors(['fasilitas.*.jumlah' => "Jumlah '{$fasilitas->nama}' melebihi stok tersedia (stok: {$fasilitas->stok})."])
                    ->withInput();
            }
        }

        // Cek bentrok jadwal pada gedung
        // Hanya booking dengan status 2 (disetujui) yang memblokir jadwal
        $startDate = $request->input('date');
        $endDate = $request->input('end_date') ?? $startDate;
        
        $overlap = Booking::where('gedung_id', $request->input('gedung_id'))
            ->where('status', '2') // Hanya yang sudah disetujui (pembayaran terverifikasi)
            ->where(function($q) use ($startDate, $endDate, $request) {
                $q->where(function($query) use ($startDate, $endDate) {
                    // Check if booking date range overlaps
                    $query->whereBetween('date', [$startDate, $endDate])
                          ->orWhereBetween('end_date', [$startDate, $endDate])
                          ->orWhere(function($q2) use ($startDate, $endDate) {
                              $q2->where('date', '<=', $startDate)
                                 ->where(function($q3) use ($endDate) {
                                     $q3->whereNull('end_date')
                                        ->orWhere('end_date', '>=', $endDate);
                                 });
                          });
                })
                ->where(function($timeQuery) use ($request) {
                    $timeQuery->where('start_time', '<', $request->input('end_time'))
                              ->where('end_time', '>', $request->input('start_time'));
                });
            })
            ->exists();
        if ($overlap) {
            return back()->withErrors(['date' => 'Jadwal bentrok pada tanggal/jam tersebut. Gedung sudah dipesan untuk waktu tersebut.'])->withInput();
        }

        $filePath = null;
        if ($request->hasFile('proposal_file')) {
            $filePath = $request->file('proposal_file')->store('proposals', 'public');
        }

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'gedung_id' => $request->input('gedung_id'),
            'event_name' => $request->input('event_name'),
            'event_type' => $request->input('event_type'),
            'capacity' => $request->input('capacity'),
            'phone' => $request->input('phone'),
            'date' => $request->input('date'),
            'end_date' => $request->input('end_date'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'proposal_file' => $filePath,
            'status' => '1',
        ]);

        $fasilitasItems = $request->input('fasilitas', []);
        $facilitiesTotal = 0;
        foreach ($fasilitasItems as $item) {
            $fasilitas = Fasilitas::find($item['id']);
            if (!$fasilitas) {
                continue;
            }

            $jumlah = $item['jumlah'] ?? 1;
            $facilitiesTotal += ($fasilitas->harga ?? 0) * $jumlah;

            BookingFasilitas::create([
                'booking_id' => $booking->id,
                'fasilitas_id' => $item['id'],
                'jumlah' => $jumlah,
            ]);
        }

        // Estimasi biaya: durasi (jam) * BASE_RATE + total fasilitas
        // Calculate duration and amount. Prefer gedung->harga (per day) when available.
        $start = strtotime($request->input('start_time'));
        $end = strtotime($request->input('end_time'));
        $hours = max(1, ceil(($end - $start) / 3600));
        $startDate = strtotime($request->input('date'));
        $endDate = strtotime($request->input('end_date') ?? $request->input('date'));
        $days = (int) floor(($endDate - $startDate) / 86400) + 1;
        if ($days < 1) { $days = 1; }

        $gedungModel = Gedung::find($request->input('gedung_id'));
        if ($gedungModel && !empty($gedungModel->harga) && $gedungModel->harga > 0) {
            // harga is treated as per-day price
            $amount = ($gedungModel->harga * $days) + $facilitiesTotal;
        } else {
            // fallback to hourly base rate
            $amount = ($hours * self::BASE_RATE_PER_HOUR * $days) + $facilitiesTotal;
        }

        $payment = \App\Models\Payment::create([
            'booking_id' => $booking->id,
            'amount' => $amount,
            'method' => 'pending',
            'proof_file' => null,
            'status' => '0', // pending
        ]);

        // Redirect user directly to payment upload form so they can pay immediately
        return redirect()->route('payments.upload.form', $payment->id)->with('success', 'Booking berhasil dibuat. Silakan lakukan pembayaran.');
    }

    public function edit($id)
    {
        $booking = Booking::with('bookingFasilitas.fasilitas')->findOrFail($id);
        
        // Check authorization - user can only edit their own bookings unless admin
        if (auth()->user()->role !== 'A' && $booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $gedung = Gedung::orderBy('nama')->get();
        $fasilitas = Fasilitas::orderBy('nama')->get();
        return view('booking.edit', [
            'title' => 'Edit Booking',
            'item' => $booking,
            'gedung' => $gedung,
            'fasilitas' => $fasilitas,
        ]);
    }

    public function invoice($id)
    {
        $booking = Booking::with(['user', 'gedung', 'bookingFasilitas.fasilitas'])->findOrFail($id);
        
        // Check authorization - user can only view their own invoice unless admin
        if (auth()->user()->role !== 'A' && $booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $payment = \App\Models\Payment::where('booking_id', $id)->first();
        
        // If payment doesn't exist, create one
        if (!$payment) {
            // Calculate amount
            $start = strtotime($booking->start_time);
            $end = strtotime($booking->end_time);
            $hours = max(1, ceil(($end - $start) / 3600));

            $facilitiesTotal = 0;
            foreach ($booking->bookingFasilitas as $bf) {
                $facilitiesTotal += ($bf->fasilitas->harga ?? 0) * $bf->jumlah;
            }

            // days between date and end_date inclusive
            $startDate = strtotime($booking->date);
            $endDate = strtotime($booking->end_date ?? $booking->date);
            $days = (int) floor(($endDate - $startDate) / 86400) + 1;
            if ($days < 1) { $days = 1; }

            $gedungModel = $booking->gedung;
            if ($gedungModel && !empty($gedungModel->harga) && $gedungModel->harga > 0) {
                $amount = ($gedungModel->harga * $days) + $facilitiesTotal;
            } else {
                $amount = ($hours * self::BASE_RATE_PER_HOUR * $days) + $facilitiesTotal;
            }
            
            $payment = \App\Models\Payment::create([
                'booking_id' => $booking->id,
                'amount' => $amount,
                'method' => 'pending',
                'proof_file' => null,
                'status' => '0',
            ]);
        }
        
        $paymentAccounts = \App\Models\PaymentAccount::where('is_active', true)->orderBy('type')->orderBy('name')->get();
        return view('booking.invoice', [
            'title' => 'Invoice Booking',
            'booking' => $booking,
            'payment' => $payment,
            'paymentAccounts' => $paymentAccounts,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validationRules = [
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
        ];
        
        // Only admin can change status
        if (auth()->user()->role === 'A') {
            $validationRules['status'] = 'required|in:1,2,3,4';
        }
        
        $request->validate($validationRules);

        // Cek bentrok saat update (kecuali dirinya sendiri)
        // Hanya booking dengan status 2 (disetujui) yang memblokir jadwal
        $startDate = $request->input('date');
        $endDate = $request->input('end_date') ?? $startDate;

        $overlap = Booking::where('gedung_id', $request->input('gedung_id'))
            ->where('id', '!=', $id)
            ->where('status', '2') // Hanya yang sudah disetujui (pembayaran terverifikasi)
            ->where(function($q) use ($startDate, $endDate, $request) {
                $q->where(function($query) use ($startDate, $endDate) {
                    // Check if booking date range overlaps
                    $query->whereBetween('date', [$startDate, $endDate])
                          ->orWhereBetween('end_date', [$startDate, $endDate])
                          ->orWhere(function($q2) use ($startDate, $endDate) {
                              $q2->where('date', '<=', $startDate)
                                 ->where(function($q3) use ($endDate) {
                                     $q3->whereNull('end_date')
                                        ->orWhere('end_date', '>=', $endDate);
                                 });
                          });
                })
                ->where(function($timeQuery) use ($request) {
                    $timeQuery->where('start_time', '<', $request->input('end_time'))
                              ->where('end_time', '>', $request->input('start_time'));
                });
            })
            ->exists();
        if ($overlap) {
            return back()->withErrors(['date' => 'Jadwal bentrok pada tanggal/jam tersebut. Gedung sudah dipesan untuk waktu tersebut.'])->withInput();
        }

        $booking = Booking::findOrFail($id);
        
        // Check authorization - user can only update their own bookings unless admin
        if (auth()->user()->role !== 'A' && $booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Only admin can change status
        $updateData = $request->only([
            'gedung_id', 'event_name', 'event_type', 'capacity', 'phone', 'date', 'end_date', 'start_time', 'end_time'
        ]);
        
        if (auth()->user()->role === 'A') {
            $updateData['status'] = $request->input('status');
        }
        
        $booking->update($updateData);

        // Update fasilitas
        $booking->bookingFasilitas()->delete(); // Remove old items
        foreach ($request->input('fasilitas', []) as $item) {
            BookingFasilitas::create([
                'booking_id' => $booking->id,
                'fasilitas_id' => $item['id'],
                'jumlah' => $item['jumlah'] ?? 1,
            ]);
        }

        return redirect()->route('booking.index')->with('success', 'Booking berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        
        // Check authorization - user can only delete their own bookings unless admin
        if (auth()->user()->role !== 'A' && $booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Prevent deleting approved bookings (status 2) unless admin
        if ($booking->status === '2' && auth()->user()->role !== 'A') {
            return redirect()->route('booking.index')
                ->with('error', 'Tidak dapat menghapus booking yang sudah disetujui. Silakan hubungi admin.');
        }
        
        $booking->delete();
        return redirect()->route('booking.index')->with('success', 'Booking berhasil dihapus.');
    }

    /**
     * User-initiated cancellation (soft logical cancel) - sets status to '3' and updates payment
     */
    public function cancel($id)
    {
        $booking = Booking::with('user')->findOrFail($id);

        // Check authorization - user can only cancel their own bookings unless admin
        if (auth()->user()->role !== 'A' && $booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Allow cancellation for non-completed bookings
        if ($booking->status === '4') {
            return redirect()->route('booking.index')->with('error', 'Tidak dapat membatalkan booking yang sudah selesai.');
        }

        $booking->update(['status' => '3']); // 3 = canceled

        // If there is a payment, mark it canceled as well
        $payment = \App\Models\Payment::where('booking_id', $booking->id)->first();
        if ($payment) {
            $payment->update(['status' => '3']);
        }

        return redirect()->route('booking.index')->with('success', 'Booking berhasil dibatalkan.');
    }
}


