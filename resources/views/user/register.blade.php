@extends('layout')

@section('content')
<h3>Registrasi</h3>
<form id="registerForm">
  <input type="text" id="username" placeholder="Username" required><br><br>
  <input type="email" id="email" placeholder="Email" required><br><br>
  <input type="date" id="tanggal_lahir" required><br><br>
  <button type="submit">Daftar</button>
</form>
<div id="message"></div>
@endsection

@push('scripts')
<script src="{{ asset('js/user.js') }}"></script>
@endpush
