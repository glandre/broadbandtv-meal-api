<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuration;
use App\Recipe;
use App\RecipeFood;

class MealController extends Controller
{
    private $request;
    private $recipe;
    private $recipeFood;

    public function __construct(Request $request, Recipe $recipe, RecipeFood $recipeFood){
        //Dependecy Injection
        $this->request = $request;
        $this->recipe = $recipe;
        $this->recipeFood = $recipeFood;
    }

    /*
     * Function: Saving a new recipe
     * Address: /api/meal/recipe
     * Method: POST
     * Implemented by:
     */
    public function postRecipe(){
        $response = array(
            'Implement this to save a new recipe',
        );
        return response()->json($response);
    }

    /*
     * Function: Retrieving a saved recipe
     * Address: /api/meal/recipe/1234
     * Method: GET
     * Implemented by:
     */
    public function getRecipe($id){
        $response = Recipe::find($id);
        return response()->json($response);
    }

    /*
     * Function: Editing a saved recipe
     * Address: /api/meal/recipe/1234
     * Method: PUT
     * Implemented by:
     */
    public function putRecipe($id){
        $response = array(
            "Implement this to edit a saved recipe where recipe id = $id",
        );
        return response()->json($response);
    }

    /*
     * Function: Retrieving a food by its NDBNO
     * Address: /api/meal/food-ndbno/1234
     * Method: GET
     * Implemented by:
     */
    public function getFoodNdbno($ndbno){
        $response = array(
            
        );
        return response()->json($response);
    }

    /*
     * Function: Retrieving a list of foods by its name
     * Address: /api/meal/food-name/butter
     * Method: GET
     * Implemented by:
     */
    public function getFoodName($name){
        
        $api_key = Configuration::find("USDA-API-KEY")->value;
        
        $url = "http://api.nal.usda.gov/ndb/search/?format=json&q=".$name."&sort=n&max=100&offset=0&api_key=".$api_key."";
        $array = $this->curlJsonUrlToArray($url);
        $response = array();

        foreach($array["list"]->item as $food){

            $response[] = array("ndbno" => $food->ndbno,
                                "name"=>$food->name
                                );

        }
        return response()->json($response);
    }

        /*
     * Function: Retrieving the nutritional information of a food or a list of foods
     * Address: /api/meal/nutritional-information
     * Method: POST
     * Implemented by: @rgbatistella
{
    "recipe": {
        "name": "My new recipe",
        "foods": [
            {
                "ndbno": "43205",
                "qty": "4.87",
                "measure": "tbsp"
            }
        ]
    }
}
     */
    public function postNutritionalInformation(){
		$foodlist = $this->request->all();
		$response = array();
		
        foreach ($foodlist['recipe']['foods'] as $food) {
								
			$url = "http://api.nal.usda.gov/ndb/reports/?ndbno=".$food['ndbno']."&type=f&format=json&api_key=BaKxZk2ziMCjeBGPJLlN8vw3VLmf2ypZbA6InZik"; 
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $url); 
			curl_setopt($ch, CURLOPT_HEADER, false);  // don't return headers
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$res = curl_exec($ch); 
			$err = curl_error($ch);
            curl_close($ch);			
				
			$nut = array();
			$resp = array();
			$resp = $res;
			$resp = json_decode($resp);

			if ($err) {
				$response[] = ['error', $err];
			} else {
				
//				$response[] = ['response' => $resp];
				$nut = array();					
                foreach ($resp->report->food->nutrients as $nutrient) {
			        
					foreach($nutrient->measures as $measure) { 
						if ($measure->label = $food['measure']) { 
							$nut[] = [   'nutrient_id' => $nutrient->nutrient_id
								   , 'nutrient_group'  => $nutrient->group
								   , 'nutrient_name' => $nutrient->name
								   , 'measure_value' => $measure->value
								   , 'measure_label' => $measure->label
									  ]; 
						}
					}
                }							
			}
				
			$response[] = [   'food_ndbno' => $food['ndbno']
							, 'food_qty'  =>   $food['qty']
							, 'food_measure' => $food['measure']
							, 'nutrients' => $nut
							, 'response' => $resp
						]; 
		}
        $response[] = ['msg', "Implement this to retrive the nutrional information of a food or a list of foods"];
        return response()->json($response);
    }

    /*
     * Function: Retrieving a recipe nutritional information
     * Address: /api/meal/nutritional-information/123
     * Method: GET
     * Implemented by: @brunolohl
     */
    public function getNutritionalInformation($id){
        $url = 'http://api.nal.usda.gov/ndb/reports/?ndbno=43205&type=f&format=json&api_key=BaKxZk2ziMCjeBGPJLlN8vw3VLmf2ypZbA6InZik';
        $array = (array)$this->curlJsonUrlToArray($url);
//        dd($array['report']);

        $recipe_foods = $this->recipeFood->select('nbno', 'weight')->where('recipe_id', $id)->get();

		$response = $recipe_foods;
        return response()->json($response);
    }

    /*
     * Function: Retriving Json content from a URL and convert the response to an array
     * url: insert a valid url to get Json
     * Implemented by: @brunolohl
     */
    private function curlJsonUrlToArray($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return (array)json_decode($result);
    }
}
