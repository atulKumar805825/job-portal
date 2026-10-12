@extends('layouts.admin')
@section('title', $company->exists ? 'Edit Company' : 'Add Company')
@section('content')
<h1 class="text-2xl font-bold mb-4">{{ $company->exists ? 'Edit' : 'Add' }} Company</h1>
<form method="POST" enctype="multipart/form-data" action="{{ $company->exists ? route('admin.companies.update', $company) : route('admin.companies.store') }}" class="bg-white p-6 rounded shadow-sm max-w-xl space-y-4">
    @csrf @if($company->exists) @method('PUT') @endif
    <div><label class="text-sm font-medium">Name *</label><input name="name" value="{{ old('name', $company->name) }}" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="text-sm font-medium">Website</label><input name="website" value="{{ old('website', $company->website) }}" placeholder="https://" class="w-full border rounded px-3 py-2"></div>
    <div><label class="text-sm font-medium">Description</label><textarea name="description" rows="4" class="w-full border rounded px-3 py-2">{{ old('description', $company->description) }}</textarea></div>
    <div><label class="text-sm font-medium">Logo</label><input type="file" name="logo" accept="image/*" class="w-full text-sm"></div>
    @unless($company->exists)
    <div class="border-t pt-4">
        <p class="text-sm font-medium mb-1">Company login (optional)</p>
        <p class="text-xs text-slate-500 mb-2">Fill these so the company can log in and post its own jobs.</p>
        <input type="email" name="login_email" value="{{ old('login_email') }}" placeholder="Login email" class="w-full border rounded px-3 py-2 mb-2">
        <input type="text" name="login_password" value="{{ old('login_password') }}" placeholder="Login password (min 6)" class="w-full border rounded px-3 py-2">
    </div>
    @endunless
    <div class="flex gap-3"><button class="bg-indigo-600 text-white px-5 py-2 rounded">Save</button><a href="{{ route('admin.companies.index') }}" class="px-5 py-2 border rounded">Cancel</a></div>
</form>
@endsection
