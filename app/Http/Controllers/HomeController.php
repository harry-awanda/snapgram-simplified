<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller {
  
  // Fungsi untuk menampilkan halaman home
  public function index() {
    // Mengambil semua foto untuk ditampilkan di halaman home
    $photos = Photo::all();
    return view('home', compact('photos'));
  }
  
  // Fungsi like
  public function like($fotoID) {
    // Cari foto berdasarkan primary key fotoID
    $photo = Photo::findOrFail($fotoID);
    // Cek apakah foto sudah di-like oleh pengguna
    if ($photo->isLikedByAuthUser()) {
      // Hapus like
      $photo->likes()->where('userID', Auth::user()->userID)->delete();
    } else {
      // Tambahkan like dengan 'tanggalLike'
      $photo->likes()->create([
        'userID' => Auth::user()->userID,
        'fotoID' => $fotoID,
        'tanggalLike' => now(), // Menambahkan tanggalLike secara manual
      ]);
    }
    // Kembali ke halaman home setelah melakukan aksi like
    return redirect()->route('home')->with('success', 'Aksi berhasil!');
  }
}