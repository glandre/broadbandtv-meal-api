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
            ['nbno' => 1009, 'name' => 'Cheese, cheddar', 'weight' => 132.0, 'measure' => '1.0 cup, diced', 'recipe_id' => 1],
        );
 
        // Uncomment the below to run the seeder
        DB::table('recipe_foods')->insert($recipe_foods);
    }

}