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
    private $configuration;

    public function __construct(Request $request, Recipe $recipe, RecipeFood $recipeFood, Configuration $configuration){
        //Dependecy Injection
        $this->request = $request;
        $this->recipe = $recipe;
        $this->recipeFood = $recipeFood;
        $this->configuration = $configuration;
    }

    /*
     * Function: Saving a new recipe
     * Address: /api/meal/recipe
     * Method: POST
     * Implemented by: @glandre
     */
    public function postRecipe(){
        
        switch($this->contentType()) {
            case "application/json":
                $request = $this->request->all();
                
                if(count($request) == 0) {
                    app()->abort(501, 'Only JSON is supported');
                    break;
                }
                
                $response = $this->updateRecipe($request);
                break;
            default:
                app()->abort(501, 'Only JSON is supported');
        }
        
        return response()->json($response);
    }

    /*
     * Function: Retrieving a saved recipe
     * Address: /api/meal/recipe/<recipe_id>
     * Method: GET
     * Implemented by: @glandre
     */
    public function getRecipe($id){
        $response = $this->recipe->find($id);
        return response()->json($response);
    }

    /*
     * Function: Retrieving all recipes from a user
     * Address: /api/meal/recipe/<user_id>
     * Method: GET
     * Implemented by: @glandre
     */
    public function getUserRecipes($user_id){
        $response = $this->recipe->where('user_id', $user_id)->get();
        return response()->json($response);
    }

    /*
     * Function: Editing a saved recipe
     * Address: /api/meal/recipe/1234
     * Method: PUT
     * Implemented by: @glandre
     */
    public function putRecipe($id){
        switch($this->contentType()) {
            case "application/json" :
                $request = $this->request->all();
                $response = $this->updateRecipe($request, $id);
                break;
            default:
                app()->abort(501, 'Only JSON is supported');
        }
        return response()->json($response);
    }
    
    /*
     * Function: Deleting a saved recipe
     * Address: /api/meal/recipe/1234
     * Method: DELETE
     * Implemented by: @glandre
     */
    public function deleteRecipe($id) {
        $recipe = $this->recipe->findOrFail($id);
        
        $response = "Could not delete recipe";
        if($recipe->delete()) {
            $response = "Recipe successfully deleted";
        }
        
        return response()->json($response);
    }
    
    /*
     * Base method for PUT and POST methods
     * Implemented by: @glandre
     */
    private function updateRecipe($request, $id = 0) {
        
        list($editingRecipe, $foods, $invalid) = $this->bindRecipeData($request, $id);
        
        $message = "Could not save recipe";
        if(count($foods)) {
            if ($editingRecipe->save()) {
                
                $editingRecipe->recipeFoods()->saveMany($foods);
                $message = (count($invalid) > 0) ? "Some data are not valid" : "Saved successfully";
            }
        }
        else {
            $message = "Invalid data";
            // bad request
        }

        return array(
            'message' => $message,
            'saved_recipe' => $editingRecipe,
            'saved_foods' => $foods,
            'invalid_foods' => $invalid
        );
        
    }

    /*
     * Function: Retrieving a food by its NDBNO
     * Address: /api/meal/food-ndbno/1234
     * Method: GET
     * Implemented by: @eossinu
     */
    public function getFoodNdbno($ndbno){
        $api_key = $this->configuration->find("USDA-API-KEY")->value;
        $url = "http://api.nal.usda.gov/ndb/reports/?ndbno={$ndbno}&type=f&format=json&api_key={$api_key}";
        $array = $this->curlJsonUrlToArray($url);
        $response = array();
        foreach($array["report"]->food->nutrients as $nutrient){
            $response[] = array(
                "name"=>$nutrient->name,
                "unit"=>$nutrient->unit,
                "value"=>$nutrient->value,
                "measure"=>$nutrient->measures
            );
        }
        return response()->json($response);
    }

    /*
     * Function: Retrieving a list of foods by its name
     * Address: /api/meal/food-name/butter
     * Method: GET
     * Implemented by: @rossini
     */
    public function getFoodName($name){
        $api_key = $this->configuration->find("USDA-API-KEY")->value;
        $url = "http://api.nal.usda.gov/ndb/search/?format=json&q={$name}&sort=n&max=100&offset=0&api_key={$api_key}";
        $array = $this->curlJsonUrlToArray($url);
        $response = array();
        foreach($array["list"]->item as $food){
            $response[] = array(
                "ndbno" => $food->ndbno,
                "name"=>$food->name
            );
        }
        return response()->json($response);
    }

     /*
     * Function: Retrieve the nutritional information of a list of foods
     * Address: /api/meal/nutritional-information/
     * Method: POST
     * Implemented by: @rgbatistella
	 * message format : json
	 * Example of content:
		{
			"recipe": {
				"name": "My new recipe",
				"foods": [
					{
						"ndbno": "43205",
						"qty": "4.87",
						"measure": "tbsp"
					},
					{
						"ndbno": "05070",
						"qty": "1",
						"measure": "cup, chopped or diced"
					}
				]
		}
}
	 * Reference information:
		[tag], [Cardinality], meaning
		"recipe", [1], recipe information
		"recipe"."name", optional, name of the recipe for reference only
		"recipe"."foods",[1..*], food list from the recipe
		"recipe"."foods"."ndbno", [1], id of the food from usda
		"recipe"."foods"."qty", [1], quantity of the unit of measure specified 
		"recipe"."foods"."measure", [1], unit of measure for the quantity specified, must match one of the measures accepted by usda 
		
		* Example of the return message:
            {
              "foods": [    
                {
                  "food_ndbno": "05070",
                  "food_qty": "1",
                  "food_measure": "cup, chopped or diced",
                  "food_nutrients": [
                    {
                      "nutrient_id": 255,
                      "nutrient_group": "Proximates",
                      "nutrient_name": "Water",
                      "nutrient_unit": "g",
                      "measure_value": 91.17,
                      "measure_label": "cup, chopped or diced"
                    }
                  ]
                }
              ],
              "sumary": [
                {
                  "nutrient_id": 255,
                  "group": "Proximates",
                  "name": "Water",
                  "unit": "g",
                  "value": 93.8485
                }
              ]
            }		
	 * Reference information:
		[tag], [Cardinality], meaning
		"foods", [1], food nutrition information
		"foods"."food_ndbno", [1], id of the food from usda
		"foods"."food_qty", [1], quantity of the unit of measure specified 
		"foods"."food_measure", [1], unit of measure for the quantity specified, must match one of the measures accepted by usda 
		"foods"."food_nutrients", [1..*], nutrients of the food for the specified unit of measure
        "foods"."food_nutrients"."nutrient_id", [1], id of the nutrient from usda
        "foods"."food_nutrients"."nutrient_group", [1], group of the nutrient from usda
        "foods"."food_nutrients"."nutrient_name", [1], name of the nutrient from usda
        "foods"."food_nutrients"."nutrient_unit", [1], unit of measure of the nutrient from usda
        "foods"."food_nutrients"."measure_value", [1], base value of the nutrient from usda
        "foods"."food_nutrients"."measure_label", [1], unit of measure for the base balue of the nutrient from usda
		"sumary", [1], summary of the nutrients considering all foods in the recipe
		"sumary"."nutrient_id", [1], id of the nutrient from usda
        "sumary"."group", [1], group of the nutrient from usda
        "sumary"."name", [1], name of the nutrient from usda
        "sumary"."unit", [1], unit of measure of the nutrient from usda
        "sumary"."value", [1], value of the nutrient considering all foods from recipe                        
		
     */
    public function postNutritionalInformation(){
		$array = $this->request->all();
        $json = json_encode($array);
        $response = $this->calculate($json);
        return response()->json($response);
    }

    /*
     * Function: Retrieving a recipe nutritional information
     * Address: /api/meal/nutritional-information/123
     * Method: GET
     * Implemented by: @brunolohl
     */
    public function getNutritionalInformation($id){
        $recipe_foods = $this->recipeFood->select('ndbno', 'qty', 'measure')->where('recipe_id', $id)->get();
        $array = array(
            'recipe' => array(
                'foods' => $recipe_foods,
            ),
        );
        $json = json_encode($array);
        $response = $this->calculate($json);
        return response()->json($response);
    }

    /*
     * Function: Calculates the nutritional information of a list of foods
     * Implemented by: @rgbatistella
	 * message format : json
     */
    private function calculate($foodlist){
        $foodlist = json_decode($foodlist,true);
        $response = array();
        // start a dummy value on summary to facilitate search
        $summary = [['nutrient_id'=>'-1']];
        // loop through food list from recipe
        foreach ($foodlist['recipe']['foods'] as $food) {
			
			$nut = array();
			if (!is_numeric($food['qty'])) {
				$response[] = ['error','qty invalid'];
			}
			else {
				
				// calls usda api
				$api_key = $this->configuration->find("USDA-API-KEY")->value;
				$url = "http://api.nal.usda.gov/ndb/reports/?ndbno=".$food['ndbno']."&type=f&format=json&api_key=".$api_key; 
				$ch = curl_init(); 
				curl_setopt($ch, CURLOPT_URL, $url); 
				curl_setopt($ch, CURLOPT_HEADER, false);  // don't return headers
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$res = curl_exec($ch); 
				$err = curl_error($ch);
				curl_close($ch);			
					
				$resp = array();
				$resp = json_decode($res,true);
	
				if ($err) {
					$response[] = ['error', $err];
				} else {			
					$nut = array();					
					// loop through nutrients
					foreach ($resp['report']['food']['nutrients'] as $nutrient) {
						// loop through nutrient´s units of measure
						foreach($nutrient['measures'] as $measure) { 
							// if label matches unit of measure from recipe
							if ($measure['label'] == $food['measure']) { 
								// store nutrient information for this food             						
								$nut[] = [  'nutrient_id'    => $nutrient['nutrient_id']
										, 'nutrient_group' => $nutrient['group']
										, 'nutrient_name'  => $nutrient['name']
										, 'nutrient_unit'  => $nutrient['unit']
										, 'measure_value'  => $measure['value'] 
										, 'measure_label'  => $measure['label']
										];
															
								// search for nutrient_id
								$key = (int)array_search($nutrient['nutrient_id'], array_column($summary, 'nutrient_id'), true);							
								// if not found on sumary
								if ($key == 0) {
									// add to summary
									$summary[] = ['nutrient_id' => $nutrient['nutrient_id']
									, 'group'  => $nutrient['group']
									, 'name' => $nutrient['name']
									, 'unit' => $nutrient['unit']
									, 'value' => $measure['value'] * $food['qty']
										];
								}
								else {							
									// sum qty							
									$summary[$key]['value'] += $measure['value'];
								}
							}
						}		
					}							
				}
			}	
			// adds food information to the summary
			if (empty($nut)){
				$response[] = ['error', 'No nutrients retrieved. Probably invalid measure!'];
			}
			else {
				$response[] = [   'food_ndbno' => $food['ndbno']
								, 'food_qty'  =>   $food['qty']
								, 'food_measure' => $food['measure']
								, 'food_nutrients' => $nut					
							]; 
			}
		}
		
		// removes dummy first position	
		array_shift($summary);

        // response is an array of foods along with their nutritrients and nutrients summary
        $response = array('foods' => $response, 'sumary' => $summary);

        // returns response
        return $response;
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

    /*
     * Implemented by: @glandre
     */
    private function bindRecipeData($request, $id=0) {
        
        $editingRecipe = ($id == 0) ? new Recipe : $this->recipe->findOrFail($id);
        
        // bind recipe from request
        $this->recipe->bind($request, $editingRecipe);
        $foodsToSave = array();
        $invalid = array();
        
        if($editingRecipe->validate()) {
            // bind each food by request
            foreach($request['foods'] as $recipeFood) {    
                
                $recipeFood = $this->recipeFood->bind($recipeFood);
                if($recipeFood->validate()) {
                    $foodsToSave[] = $recipeFood;
                }
                else {
                    $invalid[] = $recipeFood;
                }

            }
        }
        
        return array($editingRecipe, $foodsToSave, $invalid);
        
    }

    /*
     * Function: Show content from POST Requests. Usefull for testing.
     * Address: /api/meal/teste
     * Implemented by: @brunolohl
     */
    public function postTeste(){
        $response = $this->request->all();
        return response()->json($response);
    }

    /*
     * Function: Returns request's content-type (JSON is default)
     * Implemented by: @glandre
     */
    private function contentType() {
        $cType = $this->request->header('Content-Type');
        if(strlen($cType) == 0 || $cType == "text/plain;charset=UTF-8") {
            $cType = "application/json";
        }
        return $cType;
    }

}
