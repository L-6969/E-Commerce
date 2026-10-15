<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk admin</title>
<meta name="robots" content="noindex,nofollow">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Libre+Franklin:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
@vite('resources/css/app.css')
</head>
<body>
<main class="mx-auto max-w-[1000px] p-4" style="max-width:400px;margin-top:8vh">
  {{-- POST route('login.store'): email, password. Backend: throttle login (mis. 5x/menit), regenerasi sesi. --}}
  <form method="POST" action="{{ route('login.store') }}" class="card">
    @csrf
    <h2>Masuk admin</h2>
    <div class="f"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus></div>
    <div class="f"><label for="password">Kata sandi</label><input id="password" type="password" name="password" autocomplete="current-password" required></div>
    @if($errors->any())<div class="text-[13px] text-er" role="alert">{{ $errors->first() }}</div>@endif
    <button class="btn" style="width:100%">Masuk</button>
    <p class="text-sm text-mu"><a href="{{ route('home') }}">Kembali ke website</a></p>
  </form>
</main>
</body>
</html>
