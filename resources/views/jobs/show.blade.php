@extends('layouts.app')
@section('title', $job->title)
@section('content')
<a href="{{ route('jobs.index') }}" class="text-sm text-indigo-600">&larr; Back to jobs</a>
<div class="grid md:grid-cols-3 gap-6 mt-3">
    <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-sm">
        <h1 class="text-2xl font-bold">{{ $job->title }}</h1>
        <p class="text-slate-600 mt-1">{{ $job->company->name }} &middot; {{ $job->location->name }}</p>
        <div class="flex flex-wrap gap-2 my-3 text-xs">
            <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded">{{ $job->job_type }}</span>
            <span class="bg-slate-100 px-2 py-1 rounded">{{ $job->category->name }}</span>
            @if($job->experience)<span class="bg-slate-100 px-2 py-1 rounded">Exp: {{ $job->experience }}</span>@endif
            @if($job->salary_min)<span class="bg-green-100 text-green-700 px-2 py-1 rounded">&#8377;{{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }}</span>@endif
            @if($job->deadline)<span class="bg-red-100 text-red-700 px-2 py-1 rounded">Apply by {{ $job->deadline->format('d M Y') }}</span>@endif
        </div>
        <h2 class="font-semibold mt-4 mb-1">Skills Required</h2>
        <div class="flex flex-wrap gap-1 mb-4">@foreach($job->skills as $s)<span class="text-xs bg-slate-100 px-2 py-0.5 rounded">{{ $s->name }}</span>@endforeach</div>
        <h2 class="font-semibold mb-1">Job Description</h2>
        <div class="whitespace-pre-line text-slate-700">{{ $job->description }}</div>
        @if($job->company->description)
            <h2 class="font-semibold mt-6 mb-1">About {{ $job->company->name }}</h2>
            <p class="text-slate-700">{{ $job->company->description }}</p>
        @endif
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm h-fit">
        <h2 class="font-semibold text-lg mb-3">Apply for this job</h2>
        @if($applied)
            <div class="text-sm space-y-2">
                <p>You applied on {{ $applied->created_at->format('d M Y') }}.</p>
                <span class="inline-block text-xs px-2 py-1 rounded {{ $applied->color() }}">{{ $applied->label() }}</span>
                <p><a href="{{ route('my.applications') }}" class="text-indigo-600 hover:underline">Track in My Applications &rarr;</a></p>
            </div>
        @elseif($job->deadline && $job->deadline->isPast())
            <p class="text-red-600 text-sm">Applications are closed.</p>
        @else
        <form method="POST" action="{{ route('jobs.apply', $job) }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <input name="name" value="{{ old('name', auth()->user()->name ?? '') }}" placeholder="Full name *" class="w-full border rounded px-3 py-2" required>
            <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="Email *" class="w-full border rounded px-3 py-2" required>
            <input name="phone" value="{{ old('phone') }}" placeholder="Phone" class="w-full border rounded px-3 py-2">
            <textarea name="cover_letter" rows="4" placeholder="Cover letter" class="w-full border rounded px-3 py-2">{{ old('cover_letter') }}</textarea>
            <div>
                <label class="text-xs text-slate-500">Resume (PDF/DOC, max 4MB) *</label>
                <input type="file" name="resume" accept=".pdf,.doc,.docx" class="w-full text-sm" required>
            </div>
            <button class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">Submit Application</button>
        </form>
        @endif
    </div>
</div>

@if($related->isNotEmpty())
<section class="mt-10">
    <h2 class="text-xl font-bold mb-4">Related jobs you may like</h2>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($related as $r)
        <a href="{{ route('jobs.show', $r) }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow transition block">
            <div class="font-semibold text-indigo-700">{{ $r->title }}</div>
            <div class="text-sm text-slate-600">{{ $r->company->name }}</div>
            <div class="text-xs text-slate-500 mt-1">{{ $r->location->name }} &middot; {{ $r->job_type }}</div>
            <div class="mt-2 flex flex-wrap gap-1">
                @foreach($r->skills->take(3) as $s)<span class="text-xs bg-slate-100 px-2 py-0.5 rounded">{{ $s->name }}</span>@endforeach
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif
@endsection
