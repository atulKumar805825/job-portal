@extends('layouts.app')
@section('title', 'My Applications')
@section('content')
@php use App\Models\Application; @endphp
<h1 class="text-2xl font-bold mb-4">My Applications</h1>

<div class="flex flex-wrap gap-2 mb-5 text-sm">
    <a href="{{ route('my.applications') }}" class="px-3 py-1.5 rounded border {{ request('status') ? 'bg-white' : 'bg-indigo-600 text-white border-indigo-600' }}">All ({{ $counts->sum() }})</a>
    @foreach(Application::STATUSES as $s)
        <a href="{{ route('my.applications', ['status' => $s]) }}" class="px-3 py-1.5 rounded border {{ request('status') == $s ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white' }}">
            {{ Application::LABELS[$s] }} ({{ $counts[$s] ?? 0 }})
        </a>
    @endforeach
</div>

<div class="space-y-4">
@forelse($applications as $a)
    <div class="bg-white p-5 rounded-lg shadow-sm">
        <div class="flex flex-wrap justify-between gap-2">
            <div>
                <a href="{{ route('jobs.show', $a->job) }}" class="text-lg font-semibold text-indigo-700 hover:underline">{{ $a->job->title }}</a>
                <div class="text-sm text-slate-600">{{ $a->job->company->name }} &middot; {{ $a->job->location->name }} &middot; {{ $a->job->job_type }}</div>
                <div class="text-xs text-slate-500 mt-1">Applied on {{ $a->created_at->format('d M Y, h:i A') }}</div>
            </div>
            <span class="self-start text-xs px-2 py-1 rounded {{ $a->color() }}">{{ $a->label() }}</span>
        </div>

        {{-- progress tracker --}}
        @if(in_array($a->status, Application::STEPS))
            @php $current = array_search($a->status, Application::STEPS); @endphp
            <div class="mt-4 flex items-center text-xs">
                @foreach(Application::STEPS as $i => $step)
                    <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                        <div class="flex flex-col items-center">
                            <div class="h-6 w-6 rounded-full flex items-center justify-center text-white {{ $i <= $current ? 'bg-indigo-600' : 'bg-slate-300' }}">{{ $i <= $current ? '✓' : $i + 1 }}</div>
                            <span class="mt-1 text-[11px] text-center w-20 {{ $i <= $current ? 'text-indigo-700' : 'text-slate-400' }}">{{ Application::LABELS[$step] }}</span>
                        </div>
                        @if(!$loop->last)<div class="flex-1 h-0.5 mx-1 mb-5 {{ $i < $current ? 'bg-indigo-600' : 'bg-slate-300' }}"></div>@endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-4 flex gap-4 text-sm">
            <a href="{{ route('jobs.show', $a->job) }}" class="text-indigo-600 hover:underline">View job</a>
            @if($a->canCancel())
                <form method="POST" action="{{ route('my.applications.cancel', $a) }}" onsubmit="return confirm('Cancel this application?')">
                    @csrf @method('PATCH')
                    <button class="text-red-600 hover:underline">Cancel application</button>
                </form>
            @endif
        </div>
    </div>
@empty
    <div class="bg-white p-8 rounded text-center text-slate-500">
        No applications yet. <a href="{{ route('jobs.index') }}" class="text-indigo-600">Browse jobs</a>
    </div>
@endforelse
</div>
<div class="mt-6">{{ $applications->links() }}</div>
@endsection
