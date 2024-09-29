<!DOCTYPE html>
<html lang="en">
  <!-- Add this meta tag to make CSS works with nrog -->
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<head>
  <title>Snapgram</title>
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>
<body>
  <ul>
    <li><a href="{{ route('home') }}">Home</a></li>
    <li><a href="{{ route('albums.index') }}">Albums</a></li>
    <li><a href="{{ route('photos.create') }}">Upload</a></li>
    <li><a href="{{ route('profile.index') }}">Profile</a></li>
  </ul>
    @yield('content')
</body>
</html>