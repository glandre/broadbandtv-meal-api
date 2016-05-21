<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $response = array(
            "Implement this to retrive a saved recipe where recipe id = $id",
        );
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
            "Implement this to retrive an specific food by NDBNO = $ndbno",
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
        $response = array(
            "Implement this to retrive a list of foods searched by NAME = $name",
        );
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
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => "http://api.nal.usda.gov/ndb/reports/?ndbno=".$food['ndbno']."&type=f&format=json&api_key=DEMO_KEY",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"postman-token: 162114bd-260d-cf0c-bb40-ea02703dcbad"
			),
			));
			
			$resp = curl_exec($curl);
			$err = curl_error($curl);
			
			curl_close($curl);
			
			if ($err) {
				$response[] = ['errror', $err];
			} else {
				
				$response[] = ['response' => $resp];
			
			}
		
		
			$response[] = [   'food_ndbno' => $food['ndbno']
							, 'food_qty'  =>   $food['qty']
							, 'food_measure' => $food['measure']
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
        $url = "http://api.nal.usda.gov/ndb/list?format=JSON&lt=f&sort=n&api_key=DEMO_KEY";
        $array = $this->curlJsonUrlToArray($url);

		$response = array(
            "Implement this to retrive the nutrional information where recipe = $id",
        );
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
