<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    #region scopes
    public function scopeSearch($query, $request)
    {
        $query->when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%');
            $query->orWhere('address', 'like', '%' . $request->search . '%');
            $query->orWhere('phone', 'like', '%' . $request->search . '%');
        });
    }
    #endregion scopes
    protected $guarded = [];

    protected $casts = [
        'phone' => 'array',
    ];

    #region relations
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    #endregion relations

    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }
}
