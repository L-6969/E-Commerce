{{-- Partial form akun Staf. Variabel: $user (null jika baru), $action, $method (POST|PUT) --}}
@php $u = $user ?? null; @endphp
<form method="POST" action="{{ $action }}" class="card">
  @csrf @if(($method ?? 'POST') !== 'POST') @method($method) @endif
  <div class="f"><label for="name">Nama</label><input id="name" name="name" value="{{ old('name', $u->name ?? '') }}" required minlength="2" autocomplete="name"><span class="text-[13px] text-er">{{ $errors->first('name') }}</span></div>
  <div class="f"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $u->email ?? '') }}" required autocomplete="email"><span class="text-[13px] text-er">{{ $errors->first('email') }}</span></div>
  <div class="f"><label for="password">Kata sandi{{ $u ? ' (kosongkan jika tidak diubah)' : '' }}</label><input id="password" type="password" name="password" autocomplete="new-password" {{ $u ? '' : 'required' }} minlength="8"><span class="text-[13px] text-er">{{ $errors->first('password') }}</span></div>
  <div class="f"><label for="password_confirmation">Konfirmasi kata sandi</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"><span class="text-[13px] text-er">{{ $errors->first('password_confirmation') }}</span></div>
  @if($u)
  <input type="hidden" name="is_active" value="0">
  <label class="flex flex-wrap items-center gap-2 mb-3"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $u->is_active ?? true))> Akun aktif</label>
  @endif
  <div class="flex flex-wrap items-center gap-2"><button class="btn">{{ $u ? 'Simpan perubahan' : 'Buat akun Staf' }}</button><a class="btn s" href="{{ route('admin.users') }}">Batal</a></div>
</form>
