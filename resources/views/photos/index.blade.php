@extends('layouts.app')
@section('content')

<h3>Nama Album: {{ $album->namaAlbum }}</h3>
<p>Deskripsi: {{ $album->deskripsi }}</p>
<table>
  <thead>
    <tr>
      <th>Foto</th>
      <th>Judul</th>
      <th>Deskripsi</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    @if($album->photos->isNotEmpty())
      @foreach($album->photos as $photo)
      <tr>
        <td>
          <img loading="lazy" src="{{ asset('storage/' . $photo->lokasiFile) }}"
          alt="{{ $photo->judulFoto }}" style="width: 200px; height: auto;">
        </td>
        <td>{{ $photo->judulFoto }}</td>
        <td>{{ $photo->deskripsiFoto }}</td>
        <td>
          <a href="{{ route('photos.edit', $photo->fotoID) }}">Edit</a>
          <form action="{{ route('photos.destroy', $photo->fotoID) }}" method="POST"
          onsubmit="return confirm('Yakin ingin menghapus foto ini?');">
            @csrf
            @method('DELETE')
            <button type="submit">Hapus</button>
          </form>
        </td>
      </tr>
      @endforeach
    @else
      <tr>
        <td colspan="4">Tidak ada foto dalam album ini.</td>
      </tr>
    @endif
  </tbody>
</table>
@endsection