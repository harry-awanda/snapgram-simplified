<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use Illuminate\Support\Facades\Auth;

class AlbumController extends Controller {
  public function index() {
    $albums = Album::where('userID', Auth::id())->with(['photos' => function($query) {
      $query->orderBy('created_at', 'desc');
    }])->get();
    return view('albums.index', compact('albums'));
  }

  public function create() {
    return view('albums.create');
  }

  public function store(Request $request) {
    $request->validate([
      'namaAlbum' => 'required|string|max:255',
      'deskripsi' => 'required|string|max:150',
    ]);
    Album::create([
      'namaAlbum' => $request->namaAlbum,
      'deskripsi' => $request->deskripsi,
      'userID' => Auth::id(),
      'tanggalDibuat' => now(),
    ]);
    return redirect()->route('albums.index')->with('success', 'Album berhasil dibuat.');
  }

  public function edit(Album $album) {
    if ($album->userID !== Auth::id()) {
      abort(403, 'Unauthorized action.');
    }
    return view('albums.edit', compact('album'));
  }

  public function update(Request $request, Album $album) {
    if ($album->userID !== Auth::id()) {
      abort(403, 'Unauthorized action.');
    }

    $request->validate([
      'namaAlbum' => 'required|string|max:255',
      'deskripsi' => 'required|string|max:150',
    ]);

    $album->update([
      'namaAlbum' => $request->namaAlbum,
      'deskripsi' => $request->deskripsi,
    ]);

    return redirect()->route('albums.index')->with('success', 'Album berhasil diperbarui!');
  }

  public function destroy(Album $album) {
    if ($album->userID !== Auth::id()) {
      abort(403, 'Unauthorized action.');
    }

    foreach ($album->photos as $photo) {
      // Hapus file foto dari storage
      Storage::delete($photo->lokasiFile);
      $photo->delete();
    }

    $album->delete();
    return redirect()->route('albums.index')->with('success', 'Album berhasil dihapus!');
  }
}