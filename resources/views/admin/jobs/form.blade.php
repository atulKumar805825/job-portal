@extends('layouts.admin')
@section('title', $job->exists ? 'Edit Job' : 'Add Job')
@section('content')
<h1 class="text-2xl font-bold mb-4">{{ $job->exists ? 'Edit' : 'Add' }} Job</h1>
<form method="POST" action="{{ $job->exists ? route('admin.jobs.update', $job) : route('admin.jobs.store') }}" class="bg-white p-6 rounded shadow-sm grid md:grid-cols-2 gap-4 max-w-4xl">
    @csrf @if($job->exists) @method('PUT') @endif
    <div class="md:col-span-2"><label class="text-sm font-medium">Title *</label>
        <input name="title" value="{{ old('title', $job->title) }}" class="w-full border rounded px-3 py-2" required></div>

    @php
        $fields = [['category_id','Category',$categories],['location_id','Location',$locations]];
        if (auth()->user()->isAdmin()) array_unshift($fields, ['company_id','Company',$companies]);
    @endphp
    @if(auth()->user()->isCompany())
        <div class="md:col-span-2 text-sm text-slate-600">Posting as <strong>{{ auth()->user()->company->name }}</strong></div>
    @endif
    @foreach($fields as [$f,$label,$list])
    <div><label class="text-sm font-medium">{{ $label }} *</label>
        <select name="{{ $f }}" class="w-full border rounded px-3 py-2" required>
            <option value="">-- Select --</option>
            @foreach($list as $o)<option value="{{ $o->id }}" @selected(old($f, $job->$f) == $o->id)>{{ $o->name }}</option>@endforeach
        </select></div>
    @endforeach

    <div><label class="text-sm font-medium">Job Type *</label>
        <select name="job_type" class="w-full border rounded px-3 py-2">
            @foreach($types as $t)<option @selected(old('job_type', $job->job_type) == $t)>{{ $t }}</option>@endforeach
        </select></div>
    <div><label class="text-sm font-medium">Salary Min (monthly)</label>
        <input type="number" name="salary_min" value="{{ old('salary_min', $job->salary_min) }}" class="w-full border rounded px-3 py-2"></div>
    <div><label class="text-sm font-medium">Salary Max</label>
        <input type="number" name="salary_max" value="{{ old('salary_max', $job->salary_max) }}" class="w-full border rounded px-3 py-2"></div>
    <div><label class="text-sm font-medium">Experience</label>
        <input name="experience" value="{{ old('experience', $job->experience) }}" placeholder="e.g. 1-3 years" class="w-full border rounded px-3 py-2"></div>
    <div><label class="text-sm font-medium">Application Deadline</label>
        <input type="date" name="deadline" value="{{ old('deadline', optional($job->deadline)->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2"></div>

    <div class="md:col-span-2"><label class="text-sm font-medium">Skills</label>
        <div class="flex flex-wrap gap-3 border rounded p-3">
            @foreach($skills as $s)
                <label class="text-sm flex items-center gap-1"><input type="checkbox" name="skills[]" value="{{ $s->id }}" @checked(in_array($s->id, old('skills', $selected)))> {{ $s->name }}</label>
            @endforeach
            @if($skills->isEmpty())<span class="text-sm text-slate-500">No skills yet. <a class="text-indigo-600" href="{{ route('admin.skills.index') }}">Add skills</a></span>@endif
        </div></div>

    <div class="md:col-span-2"><label class="text-sm font-medium">Description *</label>
        <textarea name="description" rows="8" class="w-full border rounded px-3 py-2" required>{{ old('description', $job->description) }}</textarea></div>

    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $job->is_active))> Active (visible on site)</label>

    <div class="md:col-span-2 flex gap-3">
        <button class="bg-indigo-600 text-white px-5 py-2 rounded hover:bg-indigo-700">Save</button>
        <a href="{{ route('admin.jobs.index') }}" class="px-5 py-2 border rounded">Cancel</a>
    </div>
</form>
@endsection
