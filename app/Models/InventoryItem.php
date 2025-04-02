<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'category_id', 'quantity', 'description', 'purchase_date', 'expiration_date', 'owner_id'];

    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id'); // 🔹 inventory_category_id ではなく category_id
    }

    // InventoryItem.php
    public function images()
    {
        return $this->hasMany(InventoryItemImage::class, 'item_id'); // ← 正しい外部キーを指定
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
