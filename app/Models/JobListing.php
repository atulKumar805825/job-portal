<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    protected $table = 'job_listings';
    protected $fillable = ['title','slug','company_id','category_id','location_id','job_type',
        'salary_min','salary_max','experience','description','deadline','is_active'];
    protected $casts = ['deadline' => 'date', 'is_active' => 'boolean'];

    public const TYPES = ['Full Time', 'Part Time', 'Contract', 'Internship', 'Remote'];

    public function company()  { return $this->belongsTo(Company::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function location() { return $this->belongsTo(Location::class); }
    public function skills()   { return $this->belongsToMany(Skill::class, 'job_listing_skill'); }
    public function applications() { return $this->hasMany(Application::class); }
    public function getRouteKeyName() { return 'slug'; }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)
                 ->whereHas('company', fn ($c) => $c->where('is_approved', true))
                 ->where(fn ($x) => $x->whereNull('deadline')->orWhere('deadline', '>=', today()));
    }

    public function scopeFilter($q, array $f)
    {
        return $q
            ->when(!empty($f['q']), fn ($x) => $x->where(function ($y) use ($f) {
                $y->where('title', 'like', "%{$f['q']}%")
                  ->orWhere('description', 'like', "%{$f['q']}%")
                  ->orWhereHas('company', fn ($c) => $c->where('name', 'like', "%{$f['q']}%"))
                  ->orWhereHas('skills', fn ($s) => $s->where('name', 'like', "%{$f['q']}%"));
            }))
            ->when(!empty($f['category']), fn ($x) => $x->where('category_id', $f['category']))
            ->when(!empty($f['location']), fn ($x) => $x->where('location_id', $f['location']))
            ->when(!empty($f['company']),  fn ($x) => $x->where('company_id', $f['company']))
            ->when(!empty($f['type']),     fn ($x) => $x->where('job_type', $f['type']))
            ->when(!empty($f['skill']),    fn ($x) => $x->whereHas('skills', fn ($s) => $s->where('skills.id', $f['skill'])));
    }
}
