<?php

use Illuminate\Database\Seeder;

class RecipeStepsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Uncomment the below to wipe the table clean before populating
        DB::table('recipe_steps')->delete();
 
        $recipe_steps= array(
            ['number' => "1", 'description' => 'mix them', 'time' => "00:01", 'recipe_id' => 1],
            ['number' => "2", 'description' => 'eat', 'time' => "00:01", 'recipe_id' => 1]
        );
 
        // Uncomment the below to run the seeder
        DB::table('recipe_steps')->insert($recipe_steps);
    }
}
