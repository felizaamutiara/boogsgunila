<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fasilitas;
use App\Models\Booking;
use App\Models\Gedung;

class PublicController extends Controller
{
    private const BASE_RATE_PER_HOUR = 500000;

    public function home(Request $request)
    {
        $fasilitas = Fasilitas::all();
        // Map to a shape the public/home view expects (id, name, price)
        $facilitiesForView = $fasilitas->map(function ($it) {
            return (object) [
                'id' => $it->id,
                'nama' => $it->nama,
                'harga' => $it->harga,
                'stok' => $it->stok,
            ];
        });

        return view('home', [
            'title' => 'Booking GSG Unila',
            'facilities' => $facilitiesForView,
        ]);
    }

    public function availability(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $overlap = Booking::where('date', $request->input('date'))
            ->whereIn('status', ['1','2'])
            ->where(function($q) use ($request) {
                $q->where('start_time', '<', $request->input('end_time'))
                  ->where('end_time', '>', $request->input('start_time'));
            })
            ->exists();

        return response()->json(['available' => !$overlap]);
    }

    public function quote(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'facilities' => 'array',
            'facilities.*.id' => 'exists:fasilitas,id',
            'facilities.*.qty' => 'integer|min:1',
        ]);

        $start = strtotime($request->input('start_time'));
        $end = strtotime($request->input('end_time'));
        $hours = max(1, (int) ceil(($end - $start) / 3600));
        $base = $hours * self::BASE_RATE_PER_HOUR;

        $facilitiesTotal = 0;
        foreach ((array) $request->input('facilities', []) as $f) {
            $item = Fasilitas::find($f['id'] ?? null);
            if ($item) {
                $qty = (int)($f['qty'] ?? 1);
                $facilitiesTotal += $item->harga * $qty;
            }
        }

        return response()->json([
            'hours' => $hours,
            'base' => $base,
            'facilities' => $facilitiesTotal,
            'total' => $base + $facilitiesTotal,
        ]);
    }

    public function jadwal(Request $request)
    {
        $type = $request->input('type'); // Semua, Wisuda, Konser, dll (mengacu ke event_type)
        $date = $request->input('date');

        $query = Booking::with('gedung')
            ->whereIn('status', ['2']) // tampilkan yang disetujui
            ->orderBy('date', 'asc');

        if ($type && $type !== 'all') {
            $query->where('event_type', $type);
        }
        if ($date) {
            $query->whereDate('date', $date);
        }

        $items = $query->get();

        return view('public.jadwal', [
            'title' => 'Jadwal Acara di GSG Unila',
            'items' => $items,
            'filter_type' => $type ?? 'all',
            'filter_date' => $date,
        ]);
    }

    /**
     * Show the facilities listing page.
     */
    public function fasilitas(Request $request)
    {
        $fasilitas = Fasilitas::all();
        return view('sewa.fasilitas', [
            'title' => 'Fasilitas GSG',
            'fasilitas' => $fasilitas,
        ]);
    }

    /**
     * Show the gedung (location) page.
     */
    public function gedung(Request $request)
    {
        // Try to load first gedung for dynamic info; view has sensible defaults
        $gedung = Gedung::first();
        return view('sewa.gedung', [
            'title' => 'Sewa Gedung',
            'gedung' => $gedung,
        ]);
    }
}


