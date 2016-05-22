<?php

/* Example of content: 
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