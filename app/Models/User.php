<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    protected $fillable = ['name', 'email', 'password', 'role', 'company_id'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array { return ['email_verified_at' => 'datetime', 'password' => 'hashed']; }

    public function company() { return $this->belongsTo(Company::class); }
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isCompany(): bool { return $this->role === 'company' && $this->company_id !== null; }
}
