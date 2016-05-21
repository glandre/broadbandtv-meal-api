<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MealController extends Controller
{
    private $request;

    public function __construct(Request $request){
        $this->request = $request; //Dependency Injection
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
     * Implemented by:
     */
    public function postNutritionalInformation(){
        $response = array(
            "Implement this to retrive the nutrional information of a food or a list of foods",
        );
        return response()->json($response);
    }

    /*
     * Function: Retrieving a recipe nutritional information
     * Address: /api/meal/nutritional-information/123
     * Method: GET
     * Implemented by: @brunolohl
     */
    public function getNutritionalInformation($id){
        $response = array(
            "Implement this to retrive the nutrional information where recipe = $id",
        );
        return response()->json($response);
    }
}
