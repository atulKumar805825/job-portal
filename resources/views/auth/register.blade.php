@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div class="max-w-sm mx-auto bg-white p-6 rounded-lg shadow-sm">
    <h1 class="text-xl font-bold mb-4">Create account</h1>
    <form method="POST" action="{{ route('register') }}" class="space-y-3">@csrf
        <input name="name" value="{{ old('name') }}" placeholder="Name" class="w-full border rounded px-3 py-2" required>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="w-full border rounded px-3 py-2" required>
        <input type="password" name="password" placeholder="Password" class="w-full border rounded px-3 py-2" required>
        <input type="password" name="password_confirmation" placeholder="Confirm password" class="w-full border rounded px-3 py-2" required>
        <button class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">Register</button>
    </form>
</div>
@endsection
