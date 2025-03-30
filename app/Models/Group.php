<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'invite_only', 'max_members'];

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function invitations()
    {
        return $this->hasMany(GroupInvitation::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
   // Group.php
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id');
    }

}
