<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'website', 'logo', 'description', 'is_approved'];
    protected $casts = ['is_approved' => 'boolean'];
    public function jobs()  { return $this->hasMany(JobListing::class); }
    public function users() { return $this->hasMany(User::class); }
}
