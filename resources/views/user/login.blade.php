@extends('layout')

@section('content')
<h3>Login User</h3>
<form id="loginForm">
  <input type="email" id="username" placeholder="Email" required><br><br>
  <input type="password" id="password" placeholder="Password (YYYYMMDD)" required><br><br>
  <button type="submit">Login</button>
</form>
<p>Belum punya akun? <a href="/register">Register</a></p>
<div id="message"></div>
@endsection

@push('scripts')
<script src="{{ asset('js/user.js') }}"></script>
@endpush
