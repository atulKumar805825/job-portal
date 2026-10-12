@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<h1 class="text-2xl font-bold mb-4">Dashboard</h1>
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    @foreach($stats as $label => $n)
        <div class="bg-white p-4 rounded shadow-sm"><div class="text-2xl font-bold text-indigo-600">{{ $n }}</div><div class="text-xs text-slate-500">{{ $label }}</div></div>
    @endforeach
</div>
<div class="bg-white rounded shadow-sm">
    <div class="p-4 font-semibold border-b">Latest Applications</div>
    <table class="w-full text-sm">
        @forelse($latest as $a)
            <tr class="border-b"><td class="p-3">{{ $a->name }}<div class="text-xs text-slate-500">{{ $a->email }}</div></td>
                <td class="p-3">{{ $a->job->title }}</td><td class="p-3"><span class="text-xs px-2 py-1 rounded {{ $a->color() }}">{{ $a->label() }}</span></td>
                <td class="p-3 text-slate-500">{{ $a->created_at->diffForHumans() }}</td></tr>
        @empty <tr><td class="p-4 text-slate-500">No applications yet.</td></tr> @endforelse
    </table>
</div>
@endsection
