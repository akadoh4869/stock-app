<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'owner_id', 'group_id'];

    public function categories()
    {
        return $this->hasMany(InventoryCategory::class);
    }

    public function items()
    {
        return $this->hasManyThrough(InventoryItem::class, InventoryCategory::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
