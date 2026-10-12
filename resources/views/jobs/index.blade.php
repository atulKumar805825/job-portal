@extends('layouts.app')
@section('title', 'Find Jobs')
@section('content')
<form method="GET" class="bg-white p-4 rounded-lg shadow-sm mb-6 grid gap-3 md:grid-cols-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Job title, skill, company..." class="border rounded px-3 py-2 md:col-span-2">
    <select name="category" class="border rounded px-3 py-2">
        <option value="">All Categories</option>
        @foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <select name="location" class="border rounded px-3 py-2">
        <option value="">All Locations</option>
        @foreach($locations as $l)<option value="{{ $l->id }}" @selected(request('location') == $l->id)>{{ $l->name }}</option>@endforeach
    </select>
    <select name="company" class="border rounded px-3 py-2">
        <option value="">All Companies</option>
        @foreach($companies as $c)<option value="{{ $c->id }}" @selected(request('company') == $c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <select name="skill" class="border rounded px-3 py-2">
        <option value="">All Skills</option>
        @foreach($skills as $s)<option value="{{ $s->id }}" @selected(request('skill') == $s->id)>{{ $s->name }}</option>@endforeach
    </select>
    <select name="type" class="border rounded px-3 py-2">
        <option value="">All Job Types</option>
        @foreach($types as $t)<option @selected(request('type') == $t)>{{ $t }}</option>@endforeach
    </select>
    <div class="flex gap-2">
        <button class="flex-1 bg-indigo-600 text-white rounded px-4 py-2 hover:bg-indigo-700">Search</button>
        <a href="{{ route('jobs.index') }}" class="px-4 py-2 border rounded text-center hover:bg-slate-100">Reset</a>
    </div>
</form>

<p class="text-sm text-slate-500 mb-3">{{ $jobs->total() }} job(s) found</p>

<div class="space-y-4">
@forelse($jobs as $job)
    <div class="bg-white p-5 rounded-lg shadow-sm hover:shadow transition">
        <div class="flex justify-between flex-wrap gap-2">
            <div>
                <a href="{{ route('jobs.show', $job) }}" class="text-lg font-semibold text-indigo-700 hover:underline">{{ $job->title }}</a>
                <div class="text-sm text-slate-600">{{ $job->company->name }} &middot; {{ $job->location->name }} &middot; {{ $job->category->name }}</div>
            </div>
            <span class="self-start text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded">{{ $job->job_type }}</span>
        </div>
        <div class="mt-2 flex flex-wrap gap-1">
            @foreach($job->skills as $s)
                <a href="{{ route('jobs.index', ['skill' => $s->id]) }}" class="text-xs bg-slate-100 px-2 py-0.5 rounded hover:bg-slate-200">{{ $s->name }}</a>
            @endforeach
        </div>
        <div class="mt-3 flex justify-between items-center text-sm text-slate-500">
            <span>
                @if($job->salary_min) &#8377;{{ number_format($job->salary_min) }} - &#8377;{{ number_format($job->salary_max) }} / month &middot; @endif
                Posted {{ $job->created_at->diffForHumans() }}
            </span>
            <a href="{{ route('jobs.show', $job) }}" class="bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700">View &amp; Apply</a>
        </div>
    </div>
@empty
    <div class="bg-white p-8 rounded text-center text-slate-500">No jobs match your filters.</div>
@endforelse
</div>
<div class="mt-6">{{ $jobs->links() }}</div>
@endsection
