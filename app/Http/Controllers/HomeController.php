<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Menampilkan landing page publik KOMPLAIN.
     */
    public function index(): View
    {
        $statistik = [
            'total' => Pengajuan::count(),
            'selesai' => Pengajuan::where('status', 'selesai')->count(),
            'diproses' => Pengajuan::where('status', 'diproses')->count(),
        ];

        return view('publik.home', [
            'statistik' => $statistik,
        ]);
    }
}
