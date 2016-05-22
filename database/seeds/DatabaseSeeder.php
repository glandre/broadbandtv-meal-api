<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ConfigurationsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(RecipesTableSeeder::class);
        $this->call(RecipeFoodsTableSeeder::class);
        $this->call(RecipeStepsTableSeeder::class);
        $this->call(RecipeTagsTableSeeder::class);
    }
}
