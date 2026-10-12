<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Job Portal')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">
<nav class="bg-white border-b">
    <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
        <a href="{{ route('jobs.index') }}" class="font-bold text-xl text-indigo-600">JobPortal</a>
        <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('jobs.index') }}" class="hover:text-indigo-600">All Jobs</a>
            @auth
                @if(auth()->user()->isAdmin() || auth()->user()->isCompany())
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600">{{ auth()->user()->isAdmin() ? 'Admin Panel' : 'Company Panel' }}</a>
                @endif
                <a href="{{ route('my.applications') }}" class="hover:text-indigo-600">My Applications</a>
                <span class="text-slate-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="text-red-600 hover:underline">Logout</button></form>
            @else
                <a href="{{ route('login') }}" class="hover:text-indigo-600">Login</a>
                <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700">Register</a>
            @endauth
        </div>
    </div>
</nav>
<main class="flex-1 max-w-6xl w-full mx-auto px-4 py-6">
    @include('layouts.flash')
    @yield('content')
</main>
<footer class="text-center text-xs text-slate-400 py-4">&copy; {{ date('Y') }} JobPortal</footer>
</body>
</html>
