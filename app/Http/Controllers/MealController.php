<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuration;
use App\Recipe;
use App\RecipeFood;
use App\User;
use App\RecipeStep;
use App\RecipeTag;

/**
 * Class MealController
 * @package App\Http\Controllers
 * @author Bruno Henrique <bruno@lohl.com.br>
 */
class MealController extends Controller {

    private $request;
    private $recipe;
    private $recipeFood;
    private $recipeStep;
    private $recipeTag;
    private $configuration;
    private $user;

    /**
     * MealController constructor with Dependency Injection
     * @param Request $request
     * @param Recipe $recipe
     * @param RecipeFood $recipeFood
     * @param RecipeStep $recipeStep
     * @param RecipeTag $recipeTag
     * @param Configuration $configuration
     * @param User $user
     * @author Bruno Henrique <bruno@lohl.com.br>
     */
    public function __construct(Request $request, Recipe $recipe, RecipeFood $recipeFood, 
            RecipeStep $recipeStep, RecipeTag $recipeTag, Configuration $configuration, User $user) {
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
    public function postRecipe() {
	$success = true;
        $status = 200;
		$errors = array();
        switch ($this->contentType()) {
            case "application/json":
                $request = $this->request->all();

                if (count($request) == 0) {
                    $success = false;
                    $response = [];
                    $status = 501;
                    $errors[] = array('error' => 501, 'message' => 'Only JSON is supported');					
                    break;
                }
                
                $response = $this->updateRecipe($request,$errors);
                if (!$response['success']) {
                        $errors[] = ['errors'=>$response['message']];
                }
                break;

            default:
		$success = false;
                $status = 501;
				$response = [];
                $errors[] = array('error' => 501, 'message' => 'Only JSON is supported');
        }

        return $this->responseMsgJson($success, $response, $errors, $status);
    }

    /**
     * Function: Retrieving a saved recipe
     * Address: /api/meal/recipe/<recipe_id>
     * Method: GET
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @param int $id Recipe id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecipe($id = 0) {
        $response = (is_numeric($id) && intval($id) != 0) ?
                $this->recipe->with('recipeSteps', 'recipeFoods', 'recipeTags')->find($id) :
                $this->recipe->with('recipeSteps', 'recipeFoods', 'recipeTags')->get();
        return response()->json($response);
    }

    /**
     * Retrieving all recipes from a user
     * Address: /api/meal/user-recipes/<user_id>
     * Method: GET
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserRecipes($user_id) {
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
    public function putRecipe($id) {
        $success = true;
        $status = 200;
        $errors = array();

        switch ($this->contentType()) {
            case "application/json" :
                $request = $this->request->all();
                $response = $this->updateRecipe($request, $id);
                if (!$response['success']) {
                        $errors[] = ['errors'=>$response['message']];
                }
                break;
            default:
                $success = false;
                $status = 501;
                $response = [];
                $errors[] = array('error' => 501, 'message' => 'Only JSON is supported');					
        }
        return $this->responseMsgJson($success, $response, $errors, $status);
    }

    /**
     * Editing a saved user
     * Address: /api/meal/user/<user_id>
     * Method: PUT
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function putUser($id) {
        $status = 200;
        switch ($this->contentType()) {
            case "application/json" :
                $request = $this->request->all();
                $response = $this->updateUser($request, $id);
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
	$success = true;
        $status = 200;
        $errors = array();
        $recipe = $this->recipe->findOrFail($id);

        if ($recipe->delete()) {
            $response = "Recipe successfully deleted";
        } else {
            $response = "Could not delete recipe";
            $status = 400;
            $success = false;
        }
			
        return $this->responseMsgJson($success, $response, $errors, $status);
    }

    /**
     * Deleting a saved user
     * Address: /api/meal/recipe/<user_id>
     * Method: DELETE
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser($id) {
        $success = true;
        $errors = [];
        $status = 200;
        $user = $this->user->findOrFail($id);

        $response = "Could not delete recipe";
        if ($user->delete()) {
            $response = "User successfully deleted";
			$status = 400;
        } else {
			$success = false;
            $response = "User NOT deleted";
		}

	return $this->responseMsgJson($success, $response, $errors, $status);
    }

    /**
     * Base method for PUT and POST recipe methods
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @author Rodrigo G Batistella <rgbatistella@gmail.com>
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return array with the following fields: message, saved_recipe, saved_foods, invalid_foods, saved_steps, invalid_steps
     */
    private function updateRecipe($request, $id = 0) {
	$success = false;
        list($editingRecipe, $foods, $invalid, $steps, $invalidSteps, $tags, $invalidTags) = $this->bindRecipeData($request, $id);

        $message = "Could not save recipe";
        if (count($foods)) {
            if ($editingRecipe->save()) {

                $editingRecipe->recipeFoods()->delete();
                $editingRecipe->recipeFoods()->saveMany($foods);
                if (count($steps)) {
                    $editingRecipe->recipeSteps()->delete();
                    $editingRecipe->recipeSteps()->saveMany($steps);
                }

                if (count($tags)) {
                    $editingRecipe->recipeTags()->delete();
                    $editingRecipe->recipeTags()->saveMany($tags);
                }

				$success = !((count($invalid) > 0) || (count($invalidSteps) > 0) || (count($invalidTags) > 0));
                $message = (count($invalid) > 0) || (count($invalidSteps) > 0) || (count($invalidTags) > 0) ? "Some data are not valid" : "Saved successfully";
            }


        } else {
			$success = false;
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
            'invalid_tags' => $invalidTags,
			'success' => $success 
        );
    }
    
    /**
     * Base method for PUT and POST user methods
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return array with the following fields: message, user
     */
    private function updateUser($request, $id = 0) {
        
        if($id == 0) {
            $editingUser = new User;
            $editingUser->password = str_random(60);
        }
        
        $editingUser = ($id == 0) ? new User : $this->user->findOrFail($id);
        
        
        
        $this->user->bind($request, $editingUser);
        
        $message = "Could not save user";
        if($editingUser->save()) {
            $message = "Saved successfully";
        }     

        return array(
            'message' => $message,
            'user' => $editingUser
        );
        
    }
    
    
    /**
     * Function: Retrieving a saved user
     * Address: /api/meal/recipe/<user_id>
     * Method: GET
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @param int $id User id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser($id = 0) {
        $response = (is_numeric($id) && intval($id) != 0) ? $this->user->find($id) : $this->user->all();
        return response()->json($response);
    }

    /**
     * Saving a new user.
     * Address: /api/meal/user
     * Method: POST
     * @author Geraldo B. Landre <geraldo.landre@gmail.com>
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUser() {
        $status = 200;
        switch ($this->contentType()) {
            case "application/json":
                $request = $this->request->all();

                if (count($request) == 0) {
                    $status = 501;
                    $response = array('error' => 501, 'message' => 'Only JSON is supported');
                    break;
                }

                $response = $this->updateUser($request);
                break;
            default:
                $status = 501;
                $response = array('error' => 501, 'message' => 'Only JSON is supported');
        }

        return response()->json($response, $status);
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
    public function getFoodNdbno($ndbno) {
        //setting variable
        $usda_url = $this->configuration->find('USDA_REPORT_URL');

        $format = $this->configuration->find('PREFERRED_FORMAT');
        $api_key = $this->configuration->find("USDA-API-KEY")->value;

        //building url
<<<<<<< HEAD
        $url = $usda_url . "?ndbno=" . $ndbno . "&type=f&format=" . $format . "&api_key= " . $api_key;

=======
        $url = "http://api.nal.usda.gov/ndb/reports/?ndbno=".$ndbno."&type=f&format=json&api_key=BaKxZk2ziMCjeBGPJLlN8vw3VLmf2ypZbA6InZik";
		
>>>>>>> 1400ab978dbb9b41e02a5195b5717248527475f8
        //Setting array with USDA API result
        $array = $this->curlJsonUrlToArray($url);

        //Setting Arrays
        $response = array();
        $error = array();

        if (isset($array["errors"])){

        $error[] = array('code' => '404',
                                                     'headers' => 'Food name invalid or not found');

        $response[] = responseMsgJson(false,"USDA API didn't find any result",$error, 400);

        } else{

            foreach ($array["report"]->food->nutrients as $nutrient) {
                $response[] = array(
                    "name" => $nutrient->name,
                    "unit" => $nutrient->unit,
                    "value" => $nutrient->value,
                    "measure" => $nutrient->measures
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
    public function getFoodName($name) {
        //setting variable
        $usda_url = $this->configuration->find('USDA_SEARCH_URL');
        $format = $this->configuration->find('PREFERRED_FORMAT');
        $api_key = $this->configuration->find("USDA-API-KEY")->value;
		
        //Chaging $name format to USDA API format
        $name = trim($name);
        $name = str_replace(" ",",",$name);

        //building url
        $url = "http://api.nal.usda.gov/ndb/search/?format=json&q=".$name."&sort=n&max=100&offset=0&api_key=".$api_key."";
		
        //Setting array with USDA API result
        $array = $this->curlJsonUrlToArray($url);

        //Setting Arrays
        $response = array();
        $error = array();

        //Checking if have some error
        if (isset($array["errors"])){
			
            $error[] = array('code' => '404',
                            'headers' => 'Food name invalid or not found');
			
            $response[] = responseMsgJson(false,"USDA API didn't find any result",$error);

        } else{
			
            foreach ($array["list"]->item as $food) {
                $response[] = array(
                    "ndbno" => $food->ndbno,
                    "name" => $food->name
                );
            }
                }
        return response()->json($response);
    }

    /**
     * Retrieve the nutritional information of a list of foods
     * Address: /api/meal/nutritional-information/
     * Method: POST
     * 
     * Example of content: 
     * <code>
     * 		{
     * 			"recipe": {
     * 				"name": "My new recipe",
     * 				"foods": [
     * 					{
     * 						"ndbno": "43205",
     * 						"qty": "4.87",
     * 						"measure": "tbsp"
     * 					},
     * 					{
     * 						"ndbno": "05070",
     * 						"qty": "1",
     * 						"measure": "cup, chopped or diced"
     * 					}
     * 				]
     *      }
     *    }
     * </code>
     *
     * Reference information:
     * 		[tag], [Cardinality], meaning
     * 		"recipe", [1], recipe information
     * 		"recipe"."name", optional, name of the recipe for reference only
     * 		"recipe"."foods",[1..*], food list from the recipe
     * 		"recipe"."foods"."ndbno", [1], id of the food from usda
     * 		"recipe"."foods"."qty", [1], quantity of the unit of measure specified 
     * 		"recipe"."foods"."measure", [1], unit of measure for the quantity specified, must match one of the measures accepted by usda 
     * 		
     * Example of the return message:
     *    {
     *      "foods": [    
     *        {
     *          "food_ndbno": "05070",
     *          "food_qty": "1",
     *          "food_measure": "cup, chopped or diced",
     *          "food_nutrients": [
     *            {
     *              "nutrient_id": 255,
     *              "nutrient_group": "Proximates",
     *              "nutrient_name": "Water",
     *              "nutrient_unit": "g",
     *              "measure_value": 91.17,
     *              "measure_label": "cup, chopped or diced"
     *            }
     *          ]
     *        }
     *      ],
     *      "sumary": [
     *        {
     *          "nutrient_id": 255,
     *          "group": "Proximates",
     *          "name": "Water",
     *          "unit": "g",
     *          "value": 93.8485
     *        }
     *      ]
     *    }		
     * Reference information:
     * 		[tag], [Cardinality], meaning
     * 		"foods", [1], food nutrition information
     * 		"foods"."food_ndbno", [1], id of the food from usda
     * 		"foods"."food_qty", [1], quantity of the unit of measure specified 
     * 		"foods"."food_measure", [1], unit of measure for the quantity specified, must match one of the measures accepted by usda 
     * 		"foods"."food_nutrients", [1..*], nutrients of the food for the specified unit of measure
     *    "foods"."food_nutrients"."nutrient_id", [1], id of the nutrient from usda
     *    "foods"."food_nutrients"."nutrient_group", [1], group of the nutrient from usda
     *    "foods"."food_nutrients"."nutrient_name", [1], name of the nutrient from usda
     *    "foods"."food_nutrients"."nutrient_unit", [1], unit of measure of the nutrient from usda
     *    "foods"."food_nutrients"."measure_value", [1], base value of the nutrient from usda
     *    "foods"."food_nutrients"."measure_label", [1], unit of measure for the base balue of the nutrient from usda
     * 		"sumary", [1], summary of the nutrients considering all foods in the recipe
     * 		"sumary"."nutrient_id", [1], id of the nutrient from usda
     *    "sumary"."group", [1], group of the nutrient from usda
     *    "sumary"."name", [1], name of the nutrient from usda
     *    "sumary"."unit", [1], unit of measure of the nutrient from usda
     *    "sumary"."value", [1], value of the nutrient considering all foods from recipe
     * 
     * @example Examples/nutritional-information.php This file provides Reference information and content examples
     * @author Rodrigo G Batistella <rgbatistella@gmail.com>
     * @author Bruno Henrique <bruno@lohl.com.br>
     * @return \Illuminate\Http\JsonResponse
     */
    public function postNutritionalInformation() {
        $status = 200;
        $success = true;
        $errors = [];
        $response = [];

        switch ($this->contentType()) {
            case "application/json":
                $array = $this->request->all();
                $json = json_encode($array);

                if (count($array) == 0) {                    
					$success = false;
					$response = [];
                    $status = 501;
                    $errors[] = array('error' => 501, 'message' => 'Only JSON is supported');					
                    break;
                }

                $response = $this->calculate($json);
                break;
            default:
				$success = false;
				$response = [];
                $status = 501;
                $errors[] = array('error' => 501, 'message' => 'Only JSON is supported');					
        }

        return $this->responseMsgJson($success, $response, $errors, $status);
    }

    /**
     * Retrieving a recipe nutritional information
     * Address: /api/meal/nutritional-information/<recipe_id>
     * @author Bruno Henrique <bruno@lohl.com.br>
     * @param int $id Recipe id to retrieve the information
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNutritionalInformation($id) {
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
    private function calculate($foodlist) {
        $foodlist = json_decode($foodlist, true);
        $response = array();
        // start a dummy value on summary to facilitate search
        $summary = [['nutrient_id' => '-1']];
        // loop through food list from recipe
        foreach ($foodlist['recipe']['foods'] as $food) {

            $nut = array();
            if (!is_numeric($food['qty'])) {
                $response[] = ['error', 'qty invalid'];
                        } else {
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
                $resp = json_decode($res, true);

                if ($err) {
                    $response[] = ['error', $err];
                                } else {
                    $nut = array();
                    // loop through nutrients
                    foreach ($resp['report']['food']['nutrients'] as $nutrient) {
                        // loop through nutrient´s units of measure
                        foreach ($nutrient['measures'] as $measure) {
                            // if label matches unit of measure from recipe
                            if ($measure['label'] == $food['measure']) {
                                // store nutrient information for this food             						
                                $nut[] = [ 'nutrient_id' => $nutrient['nutrient_id']
                                    , 'nutrient_group' => $nutrient['group']
                                    , 'nutrient_name' => $nutrient['name']
                                    , 'nutrient_unit' => $nutrient['unit']
                                    , 'measure_value' => $measure['value']
                                    , 'measure_label' => $measure['label']
                                ];

                                // search for nutrient_id
                                $key = (int) array_search($nutrient['nutrient_id'], array_column($summary, 'nutrient_id'), true);
                                // if not found on sumary
                                if ($key == 0) {
                                    // add to summary
                                    $summary[] = ['nutrient_id' => $nutrient['nutrient_id']
                                        , 'group' => $nutrient['group']
                                        , 'name' => $nutrient['name']
                                        , 'unit' => $nutrient['unit']
                                        , 'value' => $measure['value'] * $food['qty']
                                    ];
                                                                } else {
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
                        } else {
                $response[] = [ 'food_ndbno' => $food['ndbno']
                    , 'food_qty' => $food['qty']
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
    private function curlJsonUrlToArray($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return (array) json_decode($result);
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
    private function bindRecipeData($request, $id = 0) {

        $editingRecipe = (intval($id) == 0) ? new Recipe : $this->recipe->findOrFail($id);
        if (array_key_exists('recipe', $request)) {
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

        if ($editingRecipe->validate()) {
            // bind each food by request
            foreach ($request['foods'] as $recipeFood) {
                $recipeFood = $this->recipeFood->bind($recipeFood);
                if ($recipeFood->validate()) {
                    $foodsToSave[] = $recipeFood;
                } else {
                    $invalid[] = $recipeFood;
                }
            }

            // @rgbatistella: bind each step by request
            if (isset($request['steps'])) {
                foreach ($request['steps'] as $recipeStep) {
                    $recipeStep = $this->recipeStep->bind($recipeStep);
                    if ($recipeStep->validate()) {
                        $stepsToSave[] = $recipeStep;
                                        } else {
                        $invalidSteps[] = $recipeStep;
                                        }
                }
                        }

            // @rgbatistella: bind each tag by request
            if (isset($request['tags'])) {
                foreach ($request['tags'] as $recipeTag) {
                    $recipeTag = $this->recipeTag->bind($recipeTag);
                    if ($recipeTag->validate()) {
                        $tagsToSave[] = $recipeTag;
                                        } else {
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

    public function postTest() {
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
        if (strlen($cType) == 0 || $cType == "text/plain;charset=UTF-8") {
            $cType = "application/json";
        }
        return $cType;
    }

    /**
     * Generates a default JSON message to return
     * @param $success
     * @param $generalMessage
     * @param $errors
     * @author Bruno Henrique <bruno@lohl.com.br>
     * @return \Illuminate\Http\JsonResponse
     */
    private function responseMsgJson($success, $generalMessage, $errors, $status = 200){
        return response()->json(
                    array(
                            'success' => $success,
                            'general_message' => $generalMessage,
                            'errors' => $errors
                    )
              , $status	
            );
    }

}
