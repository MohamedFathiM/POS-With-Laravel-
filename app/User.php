<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'image'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['image_path'];

    #region scopes
    public function scopeSearch($query, $request)
    {
        return $this->whereRoleIs('admin')->where(function ($q) use ($request) {
            $q->when($request->search, function ($query) use ($request) {
                $query->where('first_name', 'like', "%$request->search%")
                    ->orWhere('last_name', 'like', "%$request->search%")
                    ->orWhere('email', 'like', "%$request->search%");
            });
        });
    }
    #endregion scopes

    #region Attributes
    public function getImagePathAttribute()
    {
        return asset('uploads/user_images/' . $this->image);
    }
    #endregion Attributes
}
