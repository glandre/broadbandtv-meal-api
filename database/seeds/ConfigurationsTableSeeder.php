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
            ['id' => 'USDA-API-KEY' , 'value' => 'BaKxZk2ziMCjeBGPJLlN8vw3VLmf2ypZbA6InZik'],
			['id' => 'USDA_REPORT_URL' , 'value' => 'http://api.nal.usda.gov/ndb/reports/'],
			['id' => 'PREFERRED_FORMAT' , 'value' => 'json'],
			['id' => 'USDA_SEARCH_URL' , 'value' => 'http://api.nal.usda.gov/ndb/search/'],
        );
 
        // Uncomment the below to run the seeder
        DB::table('configurations')->insert($configurations);
    }

}