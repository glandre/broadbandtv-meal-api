<?php

use Illuminate\Database\Seeder;

class RecipeTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Uncomment the below to wipe the table clean before populating
        DB::table('recipe_tags')->delete();
 
        $recipe_tags= array(
            ['tag' => 'fast food', 'recipe_id' => 1],
            ['tag' => 'apitisers', 'recipe_id' => 1]
        );
 
        // Uncomment the below to run the seeder
        DB::table('recipe_tags')->insert($recipe_tags);
    }
}
