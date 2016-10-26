<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    public function comments()
    {
        return $this->hasMany('App\Comment', 'uid', 'uid');
    }
}
