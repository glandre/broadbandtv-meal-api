<?php

use Illuminate\Database\Seeder;

/**
 * @author Geraldo B. Landre <geraldo.landre@gmail.com>
 */
class RecipesTableSeeder extends Seeder {
    public function run() {
        // Uncomment the below to wipe the table clean before populating
        DB::table('recipes')->delete();
 
        $recipes = array(
            ['id' => 1, 'name' => 'Cheese with butter', 'author' => 'me', 'created_at' => new DateTime, 'updated_at' => new DateTime]
        );
 
        // Uncomment the below to run the seeder
        DB::table('recipes')->insert($recipes);
    }

}