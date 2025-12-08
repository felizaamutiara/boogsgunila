<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Gedung;
use App\Models\Fasilitas;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        try {
            $user = auth()->user();
            
            $data = [
                'title' => 'Dashboard',
                'stats' => [
                    'total_gedung' => Gedung::count(),
                    'total_fasilitas' => Fasilitas::count(),
                    'total_bookings' => $user->role === 'A' ? Booking::count() : Booking::where('user_id', $user->id)->count(),
                ],
                'recent_bookings' => $user->role === 'A' 
                    ? Booking::with(['user', 'gedung'])->latest()->limit(5)->get()
                    : Booking::with(['gedung'])->where('user_id', $user->id)->latest()->limit(5)->get(),
                'booking_stats' => $user->role === 'A'
                    ? DB::table('bookings')
                        ->select('status', DB::raw('count(*) as total'))
                        ->groupBy('status')
                        ->get()
                    : DB::table('bookings')
                        ->where('user_id', $user->id)
                        ->select('status', DB::raw('count(*) as total'))
                        ->groupBy('status')
                        ->get(),
                'facilities' => Fasilitas::all()->take(6),
            ];

            return view('dashboard', $data);
        } catch (\Exception $e) {
            // Fallback jika ada error
            return view('dashboard', [
                'title' => 'Dashboard',
                'stats' => [
                    'total_gedung' => 0,
                    'total_fasilitas' => 0,
                    'total_bookings' => 0,
                ],
                'recent_bookings' => collect(),
                'booking_stats' => collect(),
                'facilities' => collect(),
            ]);
        }
    }
}