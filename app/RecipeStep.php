<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecipeStep extends Model
{
	// @rgbatistella
    public function recipe() {
        return $this->belongsTo('App\Recipe');
    }
	
	// @rgbatistella
    public function validate() {
        return intval($this->number) > 0
            && strlen($this->description) >= 1;
    }

	// @rgbatistella
    public static function bind($data) {
        $recipeStep = new recipeStep;
        $recipeStep->number = (array_key_exists('number', $data)) ? $data['number'] : '';
        $recipeStep->description = (array_key_exists('description', $data)) ? $data['description'] : '';
        $recipeStep->time = (array_key_exists('time', $data)) ? $data['time'] : '';

        return $recipeStep;
    }
	
}
