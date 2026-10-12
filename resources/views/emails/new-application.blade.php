<div style="font-family:Arial,sans-serif;max-width:560px;margin:auto;color:#1e293b">
    <h2 style="color:#4f46e5">New job application</h2>
    <p><strong>Job:</strong> {{ $application->job->title }} ({{ $application->job->company->name }})</p>
    <p><strong>Name:</strong> {{ $application->name }}<br>
       <strong>Email:</strong> {{ $application->email }}<br>
       <strong>Phone:</strong> {{ $application->phone ?: '-' }}</p>
    @if($application->cover_letter)<p><strong>Cover letter:</strong><br>{!! nl2br(e($application->cover_letter)) !!}</p>@endif
    <p><a href="{{ route('admin.applications.index') }}" style="color:#4f46e5">Open in admin panel</a></p>
</div>
