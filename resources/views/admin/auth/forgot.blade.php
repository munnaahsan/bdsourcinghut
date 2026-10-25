@extends('admin.layouts.app')
@section('title','Reset Password')
@section('content')<div class="page-heading"><div><span class="mini-label">ACCOUNT RECOVERY</span><h1>Reset access</h1><p>Enter your administrator email address for reset instructions.</p></div></div><div class="panel narrow"><form method="POST" action="{{ route('admin.password.email') }}" class="admin-form">@csrf<label>Email address<input type="email" name="email" required autofocus value="{{ old('email') }}"></label><button class="admin-button">Send reset link ↗</button></form></div>@endsection
