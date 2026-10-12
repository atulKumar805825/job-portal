@extends('layouts.admin')
@section('title', 'Manage Jobs')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Jobs</h1>
    <a href="{{ route('admin.jobs.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Add Job</a>
</div>
<form class="mb-4"><input name="q" value="{{ request('q') }}" placeholder="Search title..." class="border rounded px-3 py-2 w-64"> <button class="px-3 py-2 bg-slate-800 text-white rounded">Search</button></form>
<div class="bg-white rounded shadow-sm overflow-x-auto">
<table class="w-full text-sm">
    <thead class="bg-slate-50 text-left"><tr>
        <th class="p-3">Title</th><th class="p-3">Company</th><th class="p-3">Category</th><th class="p-3">Location</th>
        <th class="p-3">Applicants</th><th class="p-3">Status</th><th class="p-3">Actions</th></tr></thead>
    @foreach($jobs as $job)
    <tr class="border-t">
        <td class="p-3 font-medium">{{ $job->title }}</td>
        <td class="p-3">{{ $job->company->name }}</td>
        <td class="p-3">{{ $job->category->name }}</td>
        <td class="p-3">{{ $job->location->name }}</td>
        <td class="p-3"><a class="text-indigo-600" href="{{ route('admin.applications.index') }}">{{ $job->applications_count }}</a></td>
        <td class="p-3">
            <form method="POST" action="{{ route('admin.jobs.toggle', $job) }}">@csrf @method('PATCH')
                <button class="text-xs px-2 py-1 rounded {{ $job->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-200 text-slate-600' }}">{{ $job->is_active ? 'Active' : 'Inactive' }}</button>
            </form>
        </td>
        <td class="p-3 flex gap-3">
            <a href="{{ route('admin.jobs.edit', $job) }}" class="text-indigo-600">Edit</a>
            <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" onsubmit="return confirm('Delete this job?')">@csrf @method('DELETE')
                <button class="text-red-600">Delete</button></form>
        </td>
    </tr>
    @endforeach
</table></div>
<div class="mt-4">{{ $jobs->links() }}</div>
@endsection
