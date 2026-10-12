@extends('layouts.admin')
@section('title', $title . 's')
@section('content')
<h1 class="text-2xl font-bold mb-4">{{ $title }}s</h1>
<form method="POST" action="{{ route($route . '.store') }}" class="flex gap-2 mb-4 max-w-md">@csrf
    <input name="name" placeholder="New {{ strtolower($title) }} name" class="flex-1 border rounded px-3 py-2" required>
    <button class="bg-indigo-600 text-white px-4 py-2 rounded">Add</button>
</form>
<div class="bg-white rounded shadow-sm max-w-2xl">
<table class="w-full text-sm">
    <thead class="bg-slate-50 text-left"><tr><th class="p-3">Name</th><th class="p-3">Jobs</th><th class="p-3">Actions</th></tr></thead>
    @foreach($items as $item)
    <tr class="border-t">
        <td class="p-3">
            <form method="POST" action="{{ route($route . '.update', $item->id) }}" class="flex gap-2">@csrf @method('PUT')
                <input name="name" value="{{ $item->name }}" class="border rounded px-2 py-1 flex-1">
                <button class="text-indigo-600">Save</button>
            </form>
        </td>
        <td class="p-3">{{ $item->jobs_count }}</td>
        <td class="p-3">
            <form method="POST" action="{{ route($route . '.destroy', $item->id) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-red-600">Delete</button></form>
        </td>
    </tr>
    @endforeach
</table></div>
<div class="mt-4">{{ $items->links() }}</div>
@endsection
