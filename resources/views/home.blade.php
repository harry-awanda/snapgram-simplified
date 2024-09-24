@extends('layouts.app')

@section('content')
<h2 style="text-align: center;">Snapgram</h2>
<table>
  <tr>
    <th>Judul</th>
    <th>Deskripsi</th>
    <th>Gambar</th>
    <th>Aksi</th>
  </tr>
  @foreach($photos as $photo)
  <tr>
    <td>{{ $photo->judulFoto }}</td>
    <td>{{ $photo->deskripsiFoto }}</td>
    <td>
      <img loading="lazy" src="{{ asset('storage/' . $photo->lokasiFile) }}"
      alt="{{ $photo->judulFoto }}" style="width: 200px; height: auto; aspect-ratio: 1/1; object-fit: cover;">
    </td>
    <td>
      <!-- Form untuk like/unlike -->
      <form action="{{ route('photos.like', $photo->fotoID) }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit">
          @if($photo->isLikedByAuthUser())
            Unlike
          @else
            Like
          @endif
        </button>
      </form>
      <!-- Tombol untuk komentar -->
      <a href="{{ route('photos.comments', $photo->fotoID) }}" style="margin-left: 10px;">Komentar</a>
    </td>
  </tr>
  @endforeach
</table>
@endsection