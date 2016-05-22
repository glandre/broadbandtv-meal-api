<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function recipes() {
        return $this->hasMany('App\Recipe');
    }
}
