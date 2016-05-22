<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuration;
use App\Recipe;
use App\RecipeFood;
use App\User;
use App\RecipeStep; 
use App\RecipeTag; 

class MealController extends Controller
{
    private $request;
    private $recipe;
    private $recipeFood;
    private $recipeStep; 
    private $recipeTag; 
    private $configuration;
    private $user;

    public function __construct(Request $request, Recipe $recipe, RecipeFood $recipeFood, RecipeStep $recipeStep, RecipeTag $recipeTag, Configuration $configuration, User $user){
        //Dependecy Injection
        $this->request = $request;
        $this->recipe = $recipe;
        $this->recipeFood = $recipeFood;
        $this->recipeStep = $recipeStep; 
        $this->recipeTag = $recipeTag; 
        $this->configuration = $configuration;
        $this->user = $user;
    }

    /**
     * Saving a new recipe.
     * Address: /api/meal/recipe
     * Method: POST
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @return \Illuminate\Http\JsonResponse
     */
    public function postRecipe(){
        $status = 200;
        switch($this->contentType()) {
            case "application/json":
                $request = $this->request->all();
                
                if(count($request) == 0) {
                    $status = 501;
                    $response = array('error' => 501, 'message' => 'Only JSON is supported');
                    break;
                }
                
                $response = $this->updateRecipe($request);
                break;
            default:
                $status = 501;
                $response = array('error' => 501, 'message' => 'Only JSON is supported');
        }
        
        return response()->json($response, $status);
    }

    /**
     * Function: Retrieving a saved recipe
     * Address: /api/meal/recipe/<recipe_id>
     * Method: GET
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @param int $id Recipe id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecipe($id=0){
        $response = ($id != 0) ? $this->recipe->with('recipeSteps', 'recipeFoods')->find($id) : $this->recipe->with('recipeSteps', 'recipeFoods')->get();
        return response()->json($response);
    }

    /**
     * Retrieving all recipes from a user
     * Address: /api/meal/recipe/<user_id>
     * Method: GET
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserRecipes($user_id){
        $response = $this->user->with('recipes')->find($user_id);
        return response()->json($response);
    }
    
    /**
     * Editing a saved recipe
     * Address: /api/meal/recipe/1234
     * Method: PUT
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function putRecipe($id){
        $status = 200;
        switch($this->contentType()) {
            case "application/json" :
                $request = $this->request->all();
                $response = $this->updateRecipe($request, $id);
                break;
            default:
                $status = 501;
                $response = array('error' => 501, 'message' => 'Only JSON is supported');
        }
        return response()->json($response, $status);
    }
    
    /**
     * Deleting a saved recipe
     * Address: /api/meal/recipe/<recipe_id>
     * Method: DELETE
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteRecipe($id) {
        $recipe = $this->recipe->findOrFail($id);
        
        $response = "Could not delete recipe";
        if($recipe->delete()) {
            $response = "Recipe successfully deleted";
        }
        
        return response()->json($response);
    }
    
    /**
     * Base method for PUT and POST methods
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @author Rodrigo G Batistella <rgbatistella@gmail.com>
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return array with the following fields: message, saved_recipe, saved_foods, invalid_foods, saved_steps, invalid_steps
     */
    private function updateRecipe($request, $id = 0) {
        
        list($editingRecipe, $foods, $invalid, $steps, $invalidSteps, $tags, $invalidTags) = $this->bindRecipeData($request, $id);
        
        $message = "Could not save recipe";
        if(count($foods)) {
            if ($editingRecipe->save()) {
                
		$editingRecipe->recipeFoods()->delete();
                $editingRecipe->recipeFoods()->saveMany($foods);
                if(count($steps)) {
                    $editingRecipe->recipeSteps()->delete();
                    $editingRecipe->recipeSteps()->saveMany($steps);
                }

                if(count($tags)) {
                    $editingRecipe->recipeTags()->delete();
                    $editingRecipe->recipeTags()->saveMany($tags);
                }
				
                $message = (count($invalid) > 0)||(count($invalidSteps) > 0)||(count($invalidTags) > 0) ? "Some data are not valid" : "Saved successfully";
            }
			

        }
        else {
            $message = "Invalid data";
            // TODO: bad request
        }

		
        return array(
            'message' => $message,
            'saved_recipe' => $editingRecipe,
            'saved_foods' => $foods,
            'invalid_foods' => $invalid,
            'saved_steps' => $steps,
            'invalid_steps' => $invalidSteps,
            'saved_tags' => $tags,
            'invalid_tags' => $invalidTags
        );
        
    }

    /**
     * Retrieving a food by its NDBNO
     * Address: /api/meal/food-ndbno/<food_id>
     * Method: GET
     * 
     * Function will retrieve a json with
     * food information using food ID.
     * However, if food ID is invalid
     * the function will retrieve a json
     * with error message : Invalid food ID.
     * Example of error
     *      [
     *              {
     *              erro: "Invalid food name."
     *              }
     *      ]
     * 
     * @author Renan Rossini <rossini_pc@hotmail.com>
     * @param string $ndbno USDA database food's number 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFoodNdbno($ndbno){
        
		$usda_url = $this->configuration->find('USDA_REPORT_URL');
		$format = $this->configuration->find('PREFERRED_FORMAT');
		$api_key = $this->configuration->find("USDA-API-KEY")->value;
		
        $url = $usda_url . "?ndbno=" . $ndbno . "&type=f&format=" . $format . "&api_key= ". $api_key;
        
		$array = $this->curlJsonUrlToArray($url);
        
		$response = array();
        
		if(isset($array["errors"])){
			
			$response[] = array(
				"erro" => "Invalid food ID."
			);
			
		}else{
		
			foreach($array["report"]->food->nutrients as $nutrient){
				$response[] = array(
					"name"=>$nutrient->name,
					"unit"=>$nutrient->unit,
					"value"=>$nutrient->value,
					"measure"=>$nutrient->measures
				);
			}
			
		}
        return response()->json($response);
    }

    /**
     * Retrieving a list of foods by its name
     * Address: /api/meal/food-name/butter
     * 
     * 
     *  Example of content
     * Message format : Json
     * Function will retrieve a json with
     * food information using food Name.
     * However, if food ID is invalid
     * the function will retrieve a json
     * with error message : Invalid food Name.
     * [
     *              {
     *              ndbno: "09037",
     *              name: "Avocados, raw, all commercial varieties"
     *              },
     *              {
     *              ndbno: "09038",
     *              name: "Avocados, raw, California"
     *              },
     *              {
     *              ndbno: "09039",
     *              name: "Avocados, raw, Florida"
     *              },
     *              {
     *              ndbno: "04581",
     *              name: "Oil, avocado"
     *              }
     *      ]
     *  Example of error
     *      [
     *              {
     *              erro: "Invalid food name."
     *              }
     *      ]
     * 
     * @author Renan Rossini <rossini_pc@hotmail.com>
     * @param string $name Pattern to filter by name
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFoodName($name){
        $usda_url = $this->configuration->find('USDA_SEARCH_URL');
		$format = $this->configuration->find('PREFERRED_FORMAT');
		$api_key = $this->configuration->find("USDA-API-KEY")->value;
		
        $url = $usda_url . "?format=". $format ."&q=". $name ."&sort=n&max=100&offset=0&api_key=" .$api_key. "";
        
		$array = $this->curlJsonUrlToArray($url);
        
		$response = array();
        
		if(isset($array["errors"])){
			
			$response[] = array(
				"erro" => "Invalid food name."
			);
			
		}else{
			
			foreach($array["list"]->item as $food){
				$response[] = array(
					"ndbno" => $food->ndbno,
					"name"=>$food->name
				);
			}
		}
        return response()->json($response);
    }
    
    /**
     * Retrieve the nutritional information of a list of foods
     * Address: /api/meal/nutritional-information/
     * Method: POST
     * @example Examples/nutritional-information.php This file provides Reference information and content examples
     * @author Rodrigo G Batistella <rgbatistella@gmail.com>
     * @return \Illuminate\Http\JsonResponse
     */
    public function postNutritionalInformation(){
        switch($this->contentType()) {
            case "application/json":
				$array = $this->request->all();
				$json = json_encode($array);
                
                if(count($array) == 0) {
                    app()->abort(501, 'Only JSON is supported');
                    break;
                }
                                
				$response = $this->calculate($json);
                break;
            default:
                app()->abort(501, 'Only JSON is supported');
        }

        return response()->json($response);
    }
    
    /**
     * Retrieving a recipe nutritional information
     * Address: /api/meal/nutritional-information/<recipe_id>
     * @author Bruno Henrique <bruno@lohl.com.br>
     * @param int $id Recipe id to retrieve the information
     * @return \Illuminate\Http\JsonResponse
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
    
    /**
     * Calculates the nutritional information of a list of foods
     * @author Rodrigo G Batistella <rgbatistella@gmail.com>
     * @param array $foodlist
     * @return \Illuminate\Http\JsonResponse
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
				$usda_url = $this->configuration->find('USDA_REPORT_URL');
				if ($usda_url == null) {
					$usda_url = 'http://api.nal.usda.gov/ndb/reports/';
				}
				$format = $this->configuration->find('PREFERRED_FORMAT');
				if ($format == null) {
					$format = 'json';
				}
				$api_key = $this->configuration->find("USDA-API-KEY")->value;				
				$url = $usda_url . "?ndbno=" . $food['ndbno'] . "&type=f&format=" . $format . "&api_key=" . $api_key;
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

    /**
     * Retriving Json content from a URL and convert the response to an array
     * @author Bruno Henrique <bruno@lohl.com.br>
     * @param string $url Insert a valid url to get Json
     * @return array
     */
    private function curlJsonUrlToArray($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return (array)json_decode($result);
    }
    
    /**
     * Binds the data from a request content to the right objects
     * Recipe, array of valid RecipeFood, array of invalid RecipeFood, as well as RecipeStep
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @author Rodrigo G Batistella <rgbatistella@gmail.com>
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return array with the following fields: recipe, valid_foods, invalid_foods, valid_steps, invalid_steps
     */
    private function bindRecipeData($request, $id=0) {
        
        $editingRecipe = ($id == 0) ? new Recipe : $this->recipe->findOrFail($id);
        
		if (array_key_exists('recipe',$request)) {
			$request = $request['recipe'];
		}

        // bind recipe from request
        $this->recipe->bind($request, $editingRecipe);
        $foodsToSave = array();
		
		$stepsToSave = array(); 
		$tagsToSave = array(); 
		
        $invalid = array();
        
        $invalidSteps = array(); 
        $invalidTags = array(); 

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
			
            // @rgbatistella: bind each step by request
			if (isset($request['steps'])) {
				foreach($request['steps'] as $recipeStep) {                    
					$recipeStep = $this->recipeStep->bind($recipeStep);
					if($recipeStep->validate()) {
						$stepsToSave[] = $recipeStep;
					}
					else {
						$invalidSteps[] = $recipeStep;
					}
				}
			}

            // @rgbatistella: bind each tag by request
			if (isset($request['tags'])) {
				foreach($request['tags'] as $recipeTag) {                    
					$recipeTag = $this->recipeTag->bind($recipeTag);
					if($recipeTag->validate()) {
						$tagsToSave[] = $recipeTag;
					}
					else {
						$invalidTags[] = $recipeTag;
					}
				}
			}
        }
        
        return array($editingRecipe, $foodsToSave, $invalid, $stepsToSave, $invalidSteps, $tagsToSave, $invalidTags); 
        
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
    
    /**
     * Get the current request's content-type 
     * Default content-type is application/json
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @return string content-type chosen
     */
    private function contentType() {
        $cType = $this->request->header('Content-Type');
        if(strlen($cType) == 0 || $cType == "text/plain;charset=UTF-8") {
            $cType = "application/json";
        }
        return $cType;
    }

}
