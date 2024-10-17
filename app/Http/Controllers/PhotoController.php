<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Photo;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller {
  
  // Menampilkan daftar foto dari album yang dipilih
  public function index(Album $album) {
    // Memuat relasi photos dari album
    $album->load('photos');
    // Mengembalikan tampilan daftar foto dari album
    return view('photos.index', compact('album'));
  }
  
  // Menampilkan form untuk membuat foto baru
  public function create() {
    // Mengambil daftar album milik pengguna yang sedang login
    $albums = Album::where('userID', auth()->id())->get();
    // Mengembalikan tampilan form untuk menambah foto
    return view('photos.create', compact('albums'));
  }
  
  // Menyimpan foto baru ke database
  public function store(Request $request) {
    // Validasi input dari form
    $request->validate([
      'photo' => 'required|image|max:2048', // Foto harus ada dan harus berformat gambar dengan ukuran maksimal 2048KB
      'judulFoto' => 'required|string|max:255', // Judul foto harus ada dan maksimal 255 karakter
      'description' => 'nullable|string|max:255', // Deskripsi foto bersifat opsional, maksimal 255 karakter
      'albumID' => 'required|exists:albums,albumID', // Album ID harus ada dan valid
    ]);
    // Menyimpan foto ke storage dan mendapatkan path-nya
    $photo = $request->file('photo');
    $path = $photo->store('photos', 'public');

    // Membuat entri foto baru di database
    Photo::create([
      'userID' => auth()->id(),
      'lokasiFile' => $path,
      'judulFoto' => $request->judulFoto,
      'deskripsiFoto' => $request->description,
      'tanggalUnggah' => now(),
      'albumID' => $request->albumID,
    ]);
    // Mengalihkan pengguna ke halaman utama setelah berhasil
    return redirect()->route('home');
  }
  
  // Menampilkan detail foto berdasarkan model yang dipassing
  public function show(Photo $photo) {
    // Memuat foto beserta relasi user, komentar, dan like
    $photo->load(['user', 'comments.user', 'likes']);
    // Mengembalikan tampilan detail foto
    return view('photo', compact('photo'));
  }
  
  // Menampilkan form untuk mengedit foto
  public function edit(Photo $photo) {
    // Memastikan hanya pemilik foto yang dapat mengedit
    if ($photo->userID !== Auth::id()) {
      abort(403, 'Unauthorized action.'); // Menghentikan eksekusi jika pengguna tidak berwenang
    }
    // Mengambil daftar album milik pengguna
    $albums = Album::where('userID', Auth::id())->get();
    
    // Mengembalikan tampilan form edit foto
    return view('photos.edit', compact('photo', 'albums'));
  }
  
  // Mengupdate informasi foto
  public function update(Request $request, Photo $photo) {
    // Memastikan hanya pemilik foto yang dapat mengupdate
    if ($photo->userID !== Auth::id()) {
      abort(403, 'Unauthorized action.'); // Menghentikan eksekusi jika pengguna tidak berwenang
    }
    // Validasi input dari form
    $request->validate([
      'judulFoto' => 'required|string|max:255', // Judul foto harus ada dan maksimal 255 karakter
      'description' => 'nullable|string|max:255', // Deskripsi foto bersifat opsional, maksimal 255 karakter
    ]);
    // Jika ada foto baru, validasi dan simpan foto baru
    if ($request->hasFile('photo')) {
      $request->validate(['photo' => 'image|max:2048']); // Validasi foto baru
      Storage::delete($photo->lokasiFile); // Menghapus foto lama dari storage
      $path = $request->file('photo')->store('photos', 'public'); // Menyimpan foto baru
      $photo->lokasiFile = $path; // Mengupdate path foto
    }
    // Mengupdate informasi judul dan deskripsi foto
    $photo->judulFoto = $request->judulFoto;
    $photo->deskripsiFoto = $request->description;
    $photo->save(); // Menyimpan perubahan di database
    
    // Mengalihkan pengguna kembali ke album foto setelah berhasil diupdate
    return redirect()->route('albums.photos', $photo->albumID);
  }

  // Menghapus foto
  public function destroy(Photo $photo) {
    // Memastikan hanya pemilik foto yang dapat menghapus
    if ($photo->userID !== Auth::id()) {
      abort(403, 'Unauthorized action.'); // Menghentikan eksekusi jika pengguna tidak berwenang
    }
    Storage::delete($photo->lokasiFile); // Menghapus foto dari storage
    $photo->delete(); // Menghapus entri foto dari database
    
    // Mengalihkan pengguna kembali ke halaman album setelah foto dihapus
    return redirect()->route('albums.photos', $photo->albumID);
  }

  // Menyukai atau membatalkan like pada foto
  public function like(Photo $photo) {
    // Memeriksa apakah foto sudah disukai oleh pengguna
    if ($photo->isLikedByAuthUser()) {
      // Jika sudah disukai, hapus like dari database
      $photo->likes()->where('userID', Auth::user()->userID)->delete();
    } else {
      // Jika belum disukai, buat entri like baru di database
      $photo->likes()->create([
        'userID' => Auth::user()->userID,
        'fotoID' => $photo->fotoID,
        'tanggalLike' => now(),
      ]);
    }
    // Mengalihkan pengguna kembali ke halaman utama
    return redirect()->route('home');
  }
  
  // Menampilkan komentar pada foto
  public function showComments(Photo $photo) {
    // Memuat foto beserta relasi komentar dan user
    $photo->load('comments.user');
    // Mengembalikan tampilan komentar foto
    return view('photos.comment', compact('photo'));
  }
  
  // Menyimpan komentar baru
  public function storeComment(Request $request, Photo $photo) {
    // Validasi input komentar
    $request->validate([
      'isiKomentar' => 'required|string|max:200', // Komentar harus ada dan maksimal 200 karakter
    ]);
    // Membuat entri komentar baru di database
    Comment::create([
      'isiKomentar' => $request->isiKomentar,
      'fotoID' => $photo->fotoID, // Mengaitkan komentar dengan foto yang bersangkutan
      'userID' => Auth::id(), // Mengaitkan komentar dengan pengguna yang sedang login
    ]);
    // Mengalihkan pengguna kembali ke halaman komentar foto setelah berhasil
    return redirect()->route('photos.comments', $photo);
  }
}