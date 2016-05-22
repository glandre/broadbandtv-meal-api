<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    public function recipeFoods() {
        return $this->hasMany('App\RecipeFood');
    }
    
    public function user() {
        return $this->belongsTo('App\User');
    }

    public function validate() {
        return strlen($this->name) >= 1
            && intval($this->user_id) > 0;
    }

    public static function bind($request) {
        $recipe = new Recipe;
        $recipe->name = (array_key_exists('name', $request)) ? $request['name'] : '';
        $recipe->user_id = (array_key_exists('user_id', $request)) ? $request['user_id'] : '';
        $recipe->difficulty = (array_key_exists('difficulty', $request)) ? $request['difficulty'] : '';
        $recipe->comments = (array_key_exists('comments', $request)) ? $request['comments'] : '';
        
        return $recipe;
    }

}
