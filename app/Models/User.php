<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\StudentProfile;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
       return $this->hasMany(StudentProfile::class);
    }

    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id');
    }

    public function hasRole($role)
    {
        return in_array( $role, $this->role->pluck('name')->toArray() );
    }

    public function getActiveRoleAttribute()
    {
        return $this->role()->first()?->name;
    }

    public function assignRole($role)
    {
        return $this->role()->sync(Role::where('name', $role)->first());
    }

    public function getIsVendorAttribute()
    {
        return in_array('vendor', $this->role->pluck('name')->toArray());
    }

    public function getIsStudentAttribute()
    {
        return in_array('student', $this->role->pluck('name')->toArray());
    }

    public function getIsAdminAttribute()
    {
        return in_array('superadmin', $this->role->pluck('name')->toArray());
    }

    public function getIsEdcVerificationOfficerAttribute()
    {
        return in_array('edc-verification-officer', $this->role->pluck('name')->toArray());
    }
    public function registrations()
    {
        return $this->hasMany(Registration::class, 'vendor_id');
    }

    public function transactions()
    {
        return $this->hasMany(TokenTransaction::class, 'vendor_id');
    }

    public function balance($scheme)
    {
        $credit = $this->transactions()->where('type', 'credit')->where('payment_status', true)->where('scheme_id', $scheme->id)->sum('number_of_tokens');
        $debit = $this->transactions()->where('type', 'debit')->where('scheme_id', $scheme->id)->sum('number_of_tokens');

        $balance = $credit - $debit;

        if( $balance < 0 ){
            
            $latest = ($this->transactions()->where('type', 'credit')->where('payment_status', true)->where('scheme_id', $scheme->id)->latest()->first()->number_of_tokens);
            TokenTransaction::create(['type' => 'credit', 'payment_status' => true, 'scheme_id' => $scheme->id, 'number_of_tokens' => - intval( $balance ) + $latest, 'vendor_id' => $this->id, 'amount' => 0 ]);
            
        }

        return $balance;
    }

    public function createTokenTransaction(array $data)
    {
        return $this->transactions()->create(array_merge($data, ['type' => 'credit']));
    }

    public function credit($payment)
    {
        return $this->transactions()->firstWhere('reference', $payment?->reference)?->update(['payment_status' => true ]);
    }

    public function debit($scheme)
    {
        return $this->transactions()->create([
            'amount' => 0,
            'number_of_tokens' => 1,
            'scheme_id' => $scheme->id,
            'type' => 'debit'
        ]);
    }

    public function totalTokensBought($scheme)
    {
        return $this->transactions()->where('type', 'credit')->where('payment_status', true)->where('scheme_id', $scheme->id)->sum('number_of_tokens');
    }

    public function totalTokensUsed($scheme)
    {
        return $this->transactions()->where('type', 'debit')->where('scheme_id', $scheme->id)->sum('number_of_tokens');
    }


    public function totalRegistrations($scheme)
    {
        return $this->registrations()->where('scheme_id', $scheme->id)->count();
    }
    // public function getBalanceIsLowAttribute()
    // {
    //     return $this->balance() === 0;
    // }

    public function schemes()
    {
        return $this->belongsToMany(Scheme::class, 'scheme_users', 'user_id', 'scheme_id');
    }

    public function registerVendorAs($scheme)
    {
        return $this->schemes()->syncWithoutDetaching($scheme);
    }

    public function assignUserToScheme($scheme)
    {
        return $this->schemes()->syncWithoutDetaching($scheme);
    }

    public function unregisterVendorAs($scheme)
    {
        return $this->schemes()->detach($scheme);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_users', 'user_id', 'department_id');
    }

    public function faculties()
    {
        return $this->belongsToMany(Faculty::class, 'faculty_users', 'user_id', 'faculty_id');
    }

    public function assignToFaculty(Faculty $faculty)
    {
        return $this->faculties()->syncWithoutDetaching($faculty);
    }

    public function unassignFromFaculty(Faculty $faculty)
    {
        return $this->faculties()->detach($faculty);
    }

    public function verifications()
    {
        return $this->hasMany(Registration::class, 'verified_by');
    }

    public function verify(Registration $registration)
    {
        return $registration->update(['verified_by' => $this->id, 'is_verified' => true, 'verified_at' => now() ]);
    }

    public function scopeActiveSession($query)
    {
        return $query;
    }


}
