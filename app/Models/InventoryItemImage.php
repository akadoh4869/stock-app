<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItemImage extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'image_path'];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}

