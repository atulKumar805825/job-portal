<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = ['job_listing_id','user_id','name','email','phone','cover_letter','resume_path','status'];

    public const STATUSES = ['pending', 'viewed', 'under_review', 'shortlisted', 'rejected', 'cancelled'];

    public const LABELS = [
        'pending'      => 'Application Reached',
        'viewed'       => 'Application Viewed',
        'under_review' => 'Under Review',
        'shortlisted'  => 'Shortlisted',
        'rejected'     => 'Rejected',
        'cancelled'    => 'Cancelled',
    ];

    public const COLORS = [
        'pending'      => 'bg-blue-100 text-blue-700',
        'viewed'       => 'bg-indigo-100 text-indigo-700',
        'under_review' => 'bg-amber-100 text-amber-700',
        'shortlisted'  => 'bg-green-100 text-green-700',
        'rejected'     => 'bg-red-100 text-red-700',
        'cancelled'    => 'bg-slate-200 text-slate-600',
    ];

    // Progress steps shown on the "My Applications" page
    public const STEPS = ['pending', 'viewed', 'under_review', 'shortlisted'];

    public function job() { return $this->belongsTo(JobListing::class, 'job_listing_id'); }

    public function label(): string { return self::LABELS[$this->status] ?? ucfirst($this->status); }
    public function color(): string { return self::COLORS[$this->status] ?? 'bg-slate-100 text-slate-700'; }
    public function canCancel(): bool { return in_array($this->status, ['pending', 'viewed', 'under_review']); }

    public function scopeForUser($q, User $user)
    {
        return $q->where(fn ($x) => $x->where('user_id', $user->id)->orWhere('email', $user->email));
    }
}
