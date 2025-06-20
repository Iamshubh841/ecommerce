<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Seller extends Model
{
    protected $fillable = ['user_id', 'mobile_no', 'country', 'state', 'skills'];
    protected $casts = ['skills' => 'array'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
