@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="max-w-sm mx-auto bg-white p-6 rounded-lg shadow-sm">
    <h1 class="text-xl font-bold mb-4">Login</h1>
    <form method="POST" action="{{ route('login') }}" class="space-y-3">@csrf
        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="w-full border rounded px-3 py-2" required>
        <input type="password" name="password" placeholder="Password" class="w-full border rounded px-3 py-2" required>
        <label class="text-sm flex items-center gap-2"><input type="checkbox" name="remember"> Remember me</label>
        <button class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">Login</button>
    </form>
    <p class="text-sm mt-3">No account? <a href="{{ route('register') }}" class="text-indigo-600">Register as job seeker</a></p>
    <p class="text-sm mt-1">Hiring? <a href="{{ route('register.company') }}" class="text-indigo-600">Register your company</a></p>
</div>
@endsection
