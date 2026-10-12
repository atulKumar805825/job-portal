@extends('layouts.admin')
@section('title', 'Company Profile')
@section('content')
<h1 class="text-2xl font-bold mb-4">Company Profile</h1>
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.profile.update') }}" class="bg-white p-6 rounded shadow-sm max-w-xl space-y-4">
    @csrf @method('PUT')
    <div><label class="text-sm font-medium">Company name *</label><input name="name" value="{{ old('name', $company->name) }}" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="text-sm font-medium">Website</label><input name="website" value="{{ old('website', $company->website) }}" placeholder="https://" class="w-full border rounded px-3 py-2"></div>
    <div><label class="text-sm font-medium">About the company</label><textarea name="description" rows="5" class="w-full border rounded px-3 py-2">{{ old('description', $company->description) }}</textarea></div>
    <div><label class="text-sm font-medium">Logo</label>
        @if($company->logo)<img src="{{ asset('storage/'.$company->logo) }}" class="h-12 w-12 object-cover rounded mb-2">@endif
        <input type="file" name="logo" accept="image/*" class="w-full text-sm"></div>
    <button class="bg-indigo-600 text-white px-5 py-2 rounded hover:bg-indigo-700">Save</button>
</form>
@endsection
