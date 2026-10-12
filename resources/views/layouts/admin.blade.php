<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - JobPortal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
<div class="flex min-h-screen">
    <aside class="w-56 bg-slate-900 text-slate-200 p-4 shrink-0">
        <div class="text-xl font-bold text-white">{{ auth()->user()->isAdmin() ? 'Admin Panel' : 'Company Panel' }}</div>
        @if(auth()->user()->isCompany())<div class="text-xs text-slate-400 mb-6 mt-1">{{ auth()->user()->company->name }}</div>@else<div class="mb-6"></div>@endif
        @php
            $u = auth()->user();
            $links = [['admin.dashboard', 'Dashboard', 'admin.dashboard'], ['admin.jobs.index', $u->isAdmin() ? 'Jobs' : 'My Jobs', 'admin.jobs.*']];
            if ($u->isAdmin()) {
                $links[] = ['admin.companies.index', 'Companies', 'admin.companies.*'];
                $links[] = ['admin.categories.index', 'Categories', 'admin.categories.*'];
                $links[] = ['admin.locations.index', 'Locations', 'admin.locations.*'];
                $links[] = ['admin.skills.index', 'Skills', 'admin.skills.*'];
            }
            $links[] = ['admin.applications.index', 'Applications', 'admin.applications.*'];
            if ($u->isCompany()) $links[] = ['admin.profile.edit', 'Company Profile', 'admin.profile.*'];
        @endphp
        <nav class="space-y-1 text-sm">
            @foreach($links as [$r, $label, $pattern])
                <a href="{{ route($r) }}" class="block px-3 py-2 rounded {{ request()->routeIs($pattern) ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800' }}">{{ $label }}</a>
            @endforeach
            <a href="{{ route('jobs.index') }}" target="_blank" class="block px-3 py-2 rounded hover:bg-slate-800">View Site &nearr;</a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="w-full text-left px-3 py-2 rounded hover:bg-slate-800 text-red-300">Logout</button></form>
        </nav>
    </aside>
    <main class="flex-1 p-6 overflow-x-auto">
        @if(auth()->user()->isCompany() && !auth()->user()->company->is_approved)
            <div class="mb-4 p-3 rounded bg-amber-100 text-amber-800 text-sm">Your company is <strong>pending admin approval</strong>. You can complete your profile now; you can post jobs and they go public once approved.</div>
        @endif
        @include('layouts.flash')
        @yield('content')
    </main>
</div>
</body>
</html>
