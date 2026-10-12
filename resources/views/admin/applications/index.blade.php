@extends('layouts.admin')
@section('title', 'Applications')
@section('content')
<h1 class="text-2xl font-bold mb-4">Applications</h1>
<form class="flex gap-2 mb-4">
    <input name="q" value="{{ request('q') }}" placeholder="Name or email" class="border rounded px-3 py-2">
    <select name="status" class="border rounded px-3 py-2"><option value="">All statuses</option>
        @foreach(\App\Models\Application::STATUSES as $s)<option value="{{ $s }}" @selected(request('status') == $s)>{{ \App\Models\Application::LABELS[$s] }}</option>@endforeach
    </select>
    <button class="px-4 py-2 bg-slate-800 text-white rounded">Filter</button>
</form>
<div class="bg-white rounded shadow-sm overflow-x-auto">
<table class="w-full text-sm">
    <thead class="bg-slate-50 text-left"><tr><th class="p-3">Applicant</th><th class="p-3">Job</th><th class="p-3">Cover Letter</th><th class="p-3">Resume</th><th class="p-3">Status</th><th class="p-3">Date</th><th class="p-3"></th></tr></thead>
    @forelse($applications as $a)
    <tr class="border-t align-top">
        <td class="p-3 font-medium">{{ $a->name }}<div class="text-xs text-slate-500">{{ $a->email }}<br>{{ $a->phone }}</div></td>
        <td class="p-3">{{ $a->job->title }}<div class="text-xs text-slate-500">{{ $a->job->company->name }}</div></td>
        <td class="p-3 max-w-xs text-slate-600">{{ \Illuminate\Support\Str::limit($a->cover_letter, 120) }}</td>
        <td class="p-3"><a class="text-indigo-600" href="{{ route('admin.applications.resume', $a) }}">Download</a></td>
        <td class="p-3">
            <form method="POST" action="{{ route('admin.applications.update', $a) }}">@csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1">
                    @foreach(\App\Models\Application::STATUSES as $s)<option value="{{ $s }}" @selected($a->status == $s)>{{ \App\Models\Application::LABELS[$s] }}</option>@endforeach
                </select>
            </form>
        </td>
        <td class="p-3 text-slate-500">{{ $a->created_at->format('d M Y') }}</td>
        <td class="p-3"><form method="POST" action="{{ route('admin.applications.destroy', $a) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-red-600">Delete</button></form></td>
    </tr>
    @empty <tr><td class="p-4 text-slate-500" colspan="7">No applications.</td></tr> @endforelse
</table></div>
<div class="mt-4">{{ $applications->links() }}</div>
@endsection
