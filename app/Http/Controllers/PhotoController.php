<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Photo;
use App\Models\LikePhoto;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller {

  public function index($albumID) {
    $album = Album::with('photos')->findOrFail($albumID);
    return view('photos.index', compact('album'));
  }
  // Menampilkan form untuk mengunggah foto
  public function create() {
    $albums = Album::where('userID', auth()->id())->get();
    return view('photos.create', compact('albums'));
  }
  // Menyimpan foto ke database
  public function store(Request $request) {
    $request->validate([
      'photo' => 'required|image|max:2048',
      'judulFoto' => 'required|string|max:255',
      'description' => 'nullable|string|max:255',
      'albumID' => 'required|exists:albums,albumID',
    ]);
    $photo = $request->file('photo');
    $path = $photo->store('photos', 'public');

    Photo::create([
      'userID' => auth()->id(),
      'lokasiFile' => $path,
      'judulFoto' => $request->judulFoto,
      'deskripsiFoto' => $request->description,
      'tanggalUnggah' => now(),
      'albumID' => $request->albumID,
    ]);
    return redirect()->route('home');
  }
  // Menampilkan detail foto
  public function show($photoID) {
    $photo = Photo::with(['user', 'comments.user', 'likes'])->findOrFail($photoID);
    return view('photo', compact('photo'));
  }
  
  public function edit($photoID) {
    $photo = Photo::findOrFail($photoID);
    // Pastikan hanya user yang membuat foto bisa mengedit
    if ($photo->userID !== Auth::id()) {
      abort(403, 'Unauthorized action.');
    }
    // Ambil semua album milik user untuk ditampilkan di dropdown
    $albums = Album::where('userID', Auth::id())->get();
    
    return view('photos.edit', compact('photo', 'albums'));
  }
  
  public function update(Request $request, $photoID) {
    $photo = Photo::findOrFail($photoID);
    // Pastikan hanya user yang membuat foto dapat mengeditnya
    if ($photo->userID !== Auth::id()) {
      abort(403, 'Unauthorized action.');
    }
    // Validasi input
    $request->validate([
      'judulFoto' => 'required|string|max:255',
      'description' => 'nullable|string|max:255',
    ]);
    // Jika ada foto baru, hapus yang lama dan upload yang baru
    if ($request->hasFile('photo')) {
      $request->validate(['photo' => 'image|max:2048']);
      // Hapus file lama
      Storage::delete($photo->lokasiFile);
      // Upload file baru
      $path = $request->file('photo')->store('photos', 'public');
      $photo->lokasiFile = $path;
    }
    // Update data foto
    $photo->judulFoto = $request->judulFoto;
    $photo->deskripsiFoto = $request->description;
    $photo->save();
    
    return redirect()->route('albums.photos', $photo->albumID);
  }

  public function destroy($photoID) {
    $photo = Photo::findOrFail($photoID);
    // Pastikan hanya user yang membuat foto dapat menghapusnya
    if ($photo->userID !== Auth::id()) {
      abort(403, 'Unauthorized action.');
    }
    // Hapus file foto dari storage
    Storage::delete($photo->lokasiFile);
    // Hapus entri di database
    $photo->delete();
    
    return redirect()->route('albums.photos', $photo->albumID);
  }
  // Fungsi like
  public function like($photo) {
    // Cari foto berdasarkan primary key fotoID
    $photos = Photo::findOrFail($photo);
    // Cek apakah foto sudah di-like oleh pengguna
    if ($photos->isLikedByAuthUser()) {
      // Hapus like
      $photos->likes()->where('userID', Auth::user()->userID)->delete();
    } else {
      // Tambahkan like dengan 'tanggalLike'
      $photos->likes()->create([
        'userID' => Auth::user()->userID,
        'fotoID' => $photo,
        'tanggalLike' => now(), // Menambahkan tanggalLike secara manual
      ]);
    }
    // Kembali ke halaman home setelah melakukan aksi like
    return redirect()->route('home');
  }
  
  public function showComments($photo) {
    $photo = Photo::with(['comments.user'])->findOrFail($photo);
    return view('photos.comment', compact('photo'));
  }
  
  public function storeComment(Request $request, $photo) {
    $request->validate([
      'isiKomentar' => 'required|string|max:200',
    ]);
    Comment::create([
      'isiKomentar' => $request->isiKomentar,
      'fotoID' => $photo,
      'userID' => Auth::id(),
    ]);
    return redirect()->route('photos.comments', $photo);
  }
}