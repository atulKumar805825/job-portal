<div style="font-family:Arial,sans-serif;max-width:560px;margin:auto;color:#1e293b">
    <h2 style="color:#4f46e5">Application received</h2>
    <p>Hi {{ $application->name }},</p>
    <p>Thank you for applying for <strong>{{ $application->job->title }}</strong> at <strong>{{ $application->job->company->name }}</strong>.
       We have received your application and will get back to you soon.</p>
    <p><a href="{{ route('jobs.show', $application->job) }}" style="color:#4f46e5">View job</a></p>
    <p style="color:#64748b;font-size:12px">&mdash; {{ config('app.name') }}</p>
</div>
