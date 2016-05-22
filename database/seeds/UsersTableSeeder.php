<?php

use Illuminate\Database\Seeder;

/**
 * @author Geraldo B. Landre <geraldo.landre@gmail.com>
 */
class UsersTableSeeder extends Seeder {
    public function run() {
        // Uncomment the below to wipe the table clean before populating
        DB::table('users')->delete();
 
        $recipes = array(
            ['name' => 'aperfonic', 'password' => str_random(60), 'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['name' => 'brunolohl', 'password' => str_random(60), 'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['name' => 'glandre', 'password' => str_random(60), 'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['name' => 'ilya', 'password' => str_random(60), 'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['name' => 'rgbatistella', 'password' => str_random(60), 'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['name' => 'rossini', 'password' => str_random(60), 'created_at' => new DateTime, 'updated_at' => new DateTime],
        );
 
        // Uncomment the below to run the seeder
        DB::table('users')->insert($recipes);
    }

}