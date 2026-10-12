@extends('layouts.admin')
@section('title', 'Companies')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Companies</h1>
    <a href="{{ route('admin.companies.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Add Company</a>
</div>
<div class="bg-white rounded shadow-sm overflow-x-auto">
<table class="w-full text-sm">
    <thead class="bg-slate-50 text-left"><tr><th class="p-3">Logo</th><th class="p-3">Name</th><th class="p-3">Login</th><th class="p-3">Status</th><th class="p-3">Jobs</th><th class="p-3">Actions</th></tr></thead>
    @foreach($companies as $c)
    <tr class="border-t">
        <td class="p-3">@if($c->logo)<img src="{{ asset('storage/'.$c->logo) }}" class="h-8 w-8 object-cover rounded">@else<div class="h-8 w-8 bg-slate-200 rounded"></div>@endif</td>
        <td class="p-3 font-medium">{{ $c->name }}</td>
        <td class="p-3 text-xs">{{ $c->users->pluck('email')->join(', ') ?: '—' }}</td>
        <td class="p-3">
            <form method="POST" action="{{ route('admin.companies.approve', $c) }}">@csrf @method('PATCH')
                <button class="text-xs px-2 py-1 rounded {{ $c->is_approved ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}" title="Click to toggle">{{ $c->is_approved ? 'Approved' : 'Pending' }}</button>
            </form>
        </td>
        <td class="p-3">{{ $c->jobs_count }}</td>
        <td class="p-3 flex gap-3">
            <a href="{{ route('admin.companies.edit', $c) }}" class="text-indigo-600">Edit</a>
            <form method="POST" action="{{ route('admin.companies.destroy', $c) }}" onsubmit="return confirm('Delete company and ALL its jobs?')">@csrf @method('DELETE')<button class="text-red-600">Delete</button></form>
        </td>
    </tr>
    @endforeach
</table></div>
<div class="mt-4">{{ $companies->links() }}</div>
@endsection
