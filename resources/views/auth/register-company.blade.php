@extends('layouts.app')
@section('title', 'Register Company')
@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-sm">
    <h1 class="text-xl font-bold mb-1">Register your company</h1>
    <p class="text-sm text-slate-500 mb-4">Post and manage your own jobs. New companies are approved by an admin before jobs go public.</p>
    <form method="POST" action="{{ route('register.company') }}" class="space-y-3">@csrf
        <input name="company_name" value="{{ old('company_name') }}" placeholder="Company name *" class="w-full border rounded px-3 py-2" required>
        <input name="website" value="{{ old('website') }}" placeholder="Website (https://...)" class="w-full border rounded px-3 py-2">
        <textarea name="description" rows="3" placeholder="About the company" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
        <hr>
        <input name="name" value="{{ old('name') }}" placeholder="Your name *" class="w-full border rounded px-3 py-2" required>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="Login email *" class="w-full border rounded px-3 py-2" required>
        <input type="password" name="password" placeholder="Password *" class="w-full border rounded px-3 py-2" required>
        <input type="password" name="password_confirmation" placeholder="Confirm password *" class="w-full border rounded px-3 py-2" required>
        <button class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">Register Company</button>
    </form>
    <p class="text-sm mt-3">Already registered? <a href="{{ route('login') }}" class="text-indigo-600">Login</a></p>
</div>
@endsection
