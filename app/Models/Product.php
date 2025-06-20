<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model
{
    protected $fillable = ['seller_id', 'name', 'description'];
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
    public function brands()
    {
        return $this->hasMany(Brand::class);
    }
}
