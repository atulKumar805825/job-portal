<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['name'];
    public function jobs() { return $this->belongsToMany(JobListing::class, 'job_listing_skill'); }
}
