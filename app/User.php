<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function recipes() {
        return $this->hasMany('App\Recipe');
    }
    

    public static function bind($request, User $user = null) {
        if(is_null($user)) {
            $user = new User;
        }
        if (array_key_exists('name', $request)) {
            $user->name = $request['name'];
        }
        if (array_key_exists('password', $request)) {
            $user->password = $request['password'];
        }
		
        return $user;
    }

}
