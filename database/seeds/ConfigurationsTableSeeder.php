<?php

use Illuminate\Database\Seeder;

/**
 * @author Geraldo B. Landre <geraldo.landre@gmail.com>
 */
class ConfigurationsTableSeeder extends Seeder {
    public function run() {
        // Uncomment the below to wipe the table clean before populating
        DB::table('configurations')->delete();
 
        $configurations = array(
            ['key' => 'USDA-API-KEY' , 'value' => 'BaKxZk2ziMCjeBGPJLlN8vw3VLmf2ypZbA6InZik']
        );
 
        // Uncomment the below to run the seeder
        DB::table('configurations')->insert($configurations);
    }

}