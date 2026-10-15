<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Verifikasi dua langkah</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Libre+Franklin:ital,wght@0,600;0,700;1,600" rel="stylesheet">
@vite('resources/css/app.css')
</head>
<body>
<main class="mx-auto mt-[8vh] max-w-[400px] p-4">
  {{-- Fase 2 (A07). POST route('login.2fa.verify'): code (6 digit dari aplikasi autentikator). Backend: throttle percobaan, hanya untuk peran Pemilik. --}}
  <form method="POST" action="{{ route('login.2fa.verify') }}" class="card">
    @csrf
    <h2>Verifikasi dua langkah</h2>
    <p class="text-sm text-mu">Masukkan 6 angka dari aplikasi autentikator Anda.</p>
    <div class="f"><label for="code">Kode verifikasi</label><input id="code" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus></div>
    @if($errors->any())<div class="text-[13px] text-er" role="alert">{{ $errors->first() }}</div>@endif
    <button class="btn w-full">Verifikasi</button>
    <p class="mt-3 text-sm text-mu"><a href="{{ route('login') }}">Kembali ke halaman masuk</a></p>
  </form>
</main>
</body>
</html>
