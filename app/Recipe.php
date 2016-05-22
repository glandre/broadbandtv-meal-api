<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model {

    public function recipeFoods() {
        return $this->hasMany('App\RecipeFood');
    }

	// @rgbatistella
    public function recipeSteps() {
        return $this->hasMany('App\RecipeStep');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function validate() {
        return strlen($this->name) >= 1 && intval($this->user_id) > 0;
    }

    public static function bind($request, Recipe $recipe = null) {
        if(is_null($recipe)) {
            $recipe = new Recipe;
        }
        if (array_key_exists('name', $request)) {
            $recipe->name = $request['name'];
        }
        if (array_key_exists('user_id', $request)) {
            $recipe->user_id = $request['user_id'];
        }
        
        if (array_key_exists('difficulty', $request)) {
            $recipe->difficulty = $request['difficulty'];
        }
        
        if (array_key_exists('comments', $request)) {
            $recipe->comments = $request['comments'];
        }
		
        return $recipe;
    }

}
