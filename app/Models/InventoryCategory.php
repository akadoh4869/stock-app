<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'inventory_id'];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function items()
    {
        return $this->hasMany(InventoryItem::class, 'category_id'); // ✅ 正しいカラムを指定
    }

}
