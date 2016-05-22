<?php

use Illuminate\Database\Seeder;

/**
 * @author Geraldo B. Landre <geraldo.landre@gmail.com>
 */
class RecipeFoodsTableSeeder extends Seeder {
    public function run() {
        // Uncomment the below to wipe the table clean before populating
        DB::table('recipe_foods')->delete();
 
        $recipe_foods = array(
            ['ndbno' => "43205", 'name' => 'I don´t know', 'qty' => 4.87, 'measure' => "tbsp", 'recipe_id' => 1],
            ['ndbno' => "05070", 'name' => 'I don´t know either', 'qty' => 2, 'measure' => "cup, chopped or diced", 'recipe_id' => 1],
        );
 
        // Uncomment the below to run the seeder
        DB::table('recipe_foods')->insert($recipe_foods);
    }

}