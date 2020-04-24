<?php

namespace App;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use Translatable;

    public $translatedAttributes = ['name'];
    protected $guarded = [];

    #region Scopes
    public function scopeSearch($query, $request)
    {
        return $query->whereTranslationLike('name', '%' . $request->search . '%');
    }
    #endregion Scopes

    #region relations
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    #endregion relations
}
