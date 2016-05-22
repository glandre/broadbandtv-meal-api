<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecipeFood extends Model
{
    public function recipe() {
        return $this->belongsTo('App\Recipe');
    }

    public function validate() {
        return strlen($this->ndbno) >= 1 && strlen($this->ndbno) <= 10
            && intval($this->qty) > 0
            && strlen($this->measure) >= 1;
    }

    public static function bind($data) {
        $recipeFood = new RecipeFood;
        $recipeFood->ndbno = (array_key_exists('ndbno', $data)) ? $data['ndbno'] : '';
        $recipeFood->name = (array_key_exists('name', $data)) ? $data['name'] : '';
        $recipeFood->measure = (array_key_exists('measure', $data)) ? $data['measure'] : '';
        $recipeFood->qty = (array_key_exists('qty', $data)) ? $data['qty'] : '';
        return $recipeFood;
    }

}
