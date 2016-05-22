<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecipeTag extends Model
{
    public function recipe() {
        return $this->belongsTo('App\Recipe');
    }

    public function validate() {
        return strlen($this->tag) >= 1;
    }

    public static function bind($data) {
        $recipeTag = new RecipeTag;
        $recipeTag->tag = (array_key_exists('tag', $data)) ? $data['tag'] : '';
        return $recipeTag;
    }
	
}
