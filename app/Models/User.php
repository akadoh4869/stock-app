<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','user_name', 'email', 'password', 'is_admin'];

    /**
     * ユーザーが所属しているグループを取得する
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members', 'user_id', 'group_id');
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'owner_id'); // ← user_id ではなく owner_id を指定
    }

    public function inventories()
    {
        return $this->hasMany(\App\Models\Inventory::class, 'owner_id');
    }
    

}
