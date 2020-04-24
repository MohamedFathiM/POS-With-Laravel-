<?php

namespace App;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use Translatable;

    protected $fillable = ['image', 'purchase_price', 'sale_price', 'stock', 'category_id'];
    protected $appends = ['image_path', 'profit_percent'];
    public $translatedAttributes = ['name', 'description'];

    #region Scopes
    public function scopeSearch($query, $request)
    {
        return $query->whereTranslationLike('name', '%' . $request->search . '%')
            ->when($request->category_id, function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            });
    }
    #endregion Scopes
    #region relations
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    #endregion relations

    #region Attributes
    public function getImagePathAttribute()
    {
        return asset('uploads/product_images/' . $this->image);
    }

    public function getProfitPercentAttribute()
    {
        $profit  = $this->sale_price - $this->purchase_price;
        return number_format($profit * 100 / $this->purchase_price, 2);
    }
    #endregion Attributes
}
