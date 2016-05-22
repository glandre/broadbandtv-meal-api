# VanHackathon BroadBandTV-meal-API

## Back-end challenge: Meal Nutrition API

Challenge date: from May 20th to May 22nd, 2016.

Hiring for: Back-end developer
Programming language: PHP
Tools to be used: Up to you. Symfony Framework is preferred.
Skills to demonstrate:
Ability to design & build RESTful APIs
Good project structure
Modern PHP practices
Bonus: Dependency Injection

Objective
The goal of this challenge is to build a small RESTful API allowing users to save recipes and to get their aggregate nutrition information based on the nutrition information of each ingredient, using the USDA Nutrient Database API (https://ndb.nal.usda.gov/ndb/doc/index). The API should be accessible from any API client (cli/curl, Postman, etc).

Deliver your submission in a .zip file, with instructions on how to run & use it in a README file. Make sure it can be run as easily as possible.
Bonus
Any related feature you think would be cool or useful.

#Instructions

##Information
- BroadbandTV-Meal-API
- RESTfull JSON API
- Version 1.0.0.0

##Requirements
- PHP5.6 or higher
- Composer
- SQL Database (MySQL is preferred, but you can use any PDO's possibility)

> Important: For the above tutorial, we are going to use MySQL Database.

> Important: Be sure to create a new database before iniciate install process

##How to Install
Download the zip file and unzip it in your local machine.

###Windows
After unziping the content file, open command prompt</b>

Access the API's source code directory
```
cd broadbandtv-meal-api-master
```

Run composer to install and update all necessary depencencies (it may take some time, so be patient..)
```
composer update
```

Now, that you have everything you need to run this API, you must configure it.
```
copy .env.example .env
```

You also have to generate a valid app key. The following command will do this to you:
```
php artisan key:generate
```

Using any text editor of your preference, edit file **/broadbandtv-meal-api-master/.env** according to your environment.

Close your text editor and go back to command prompt. At this point your application is well configured, but you need a few important information inside your database.
```
php artisan migrate --seed
```

Excellent! It's all done. Now you can use your Web Server Application to run this API.

You don't have any Web Server in your machine? No problem. Make sure you are inside **/broadbandtv-meal-api-master** directory and, using your command prompt, run the following command:
```
php artisan serve
```

###Linux
Execute the following steps using a terminal</b>

Go to the directory you have downloaded API's zip file and unzip it
```
unzip broadbandtv-meal-api-master.zip
```

After unziping the content file, access the API's source code directory
```
cd broadbandtv-meal-api-master
```

Run composer to install and update all necessary depencencies (it may take some time, so be patient..)
```
composer update
```

Now, that you have everything you need to run this API, you must configure it.
```
cp .env.example .env
```

You also have to generate a valid app key. The following command will do this to you:
```
php artisan key:generate
```

Using any text editor of your preference, edit file **/broadbandtv-meal-api-master/.env** according to your environment.

Close your text editor and go back to command prompt. At this point your application is well configured, but you need a few important information inside your database.
```
php artisan migrate --seed
```

Excellent! It's all done. Now you can use your Web Server Application to run this API.

You don't have any Web Server in your machine? No problem. Make sure you are inside **/broadbandtv-meal-api-master** directory and run the following command:
```
php artisan serve
```

##Available API Routes

###Recipe

####Saving a new recipe
- Address: http://domain:port/api/meal/recipe
- Method: POST
- Return: JSON
- Post Content:
```php
{
"name" : "One Recipe with Two Valid Foods123",
"user_id" : 3,
"foods" : [
{
"ndbno" : "01005",
"qty" : 2,
"measure" : "cup, diced", 
"name" : "First Food"
},
{
"ndbno" : "01009",
"qty" : 1,
"measure" : "cup, melted",
"name" : "Another ingredient"
}
],
"auth" : {
       "name" : "ilya",
       "password" : "rOuUb06Qp0EEv1jH1toQbUbHeXL4AFZHLfO184rt8CVW3jWCQdQoFn4ADTl6"
}
}
```

####Editing a saved recipe
- Address: http://domain:port/api/meal/recipe/\<recipe_id\>
- Method: PUT
- Return: JSON
- Post Content:
```php
//
```

####Deleting a saved recipe
- Address: http://domain:port/api/meal/recipe/\<recipe_id\>
- Method: DELETE
- Return: JSON

####Retrieving a saved recipe
- Address: http://domain:port/api/meal/recipe/\<recipe_id\>
- Method: GET
- Return: JSON

####Retrieving all recipes from a user
- Address: http://domain:port/api/meal/user-recipes/\<user_id\>
- Method: GET
- Return: JSON

___

###User

####Saving a new user
- Address: http://domain:port/api/meal/user
- Method: POST
- Return: JSON
- Post Content:
```php
//
```

####Editing a saved user
- Address: http://domain:port/api/meal/user/\<user_id\>
- Method: PUT
- Return: JSON
- Post Content:
```php
//
```

####Deleting a saved user
- Address: http://domain:port/api/meal/user/\<user_id\>
- Method: DELETE
- Return: JSON

####Retrieving a saved user
- Address: http://domain:port/api/meal/user/\<user_id\>
- Method: GET
- Return: JSON

####Retrieving all saved users
- Address: http://domain:port/api/meal/user/
- Method: GET
- Return: JSON

___

###Food Search

####Retrieving a food name and its measures by its NDBNO
- Address: http://domain:port/api/meal/food-ndbno/\<food_id\>
- Method: GET
- Return: JSON

####Retrieving a list of foods by its name
- Address: http://domain:port/api/meal/food-name/\<food_name\>
- Method: GET
- Return: JSON

___

###Calculating Nutritional Information

####Retrieving nutritional information of a list of foods
- Address: http://domain:port/api/meal/nutritional-information/
- Method: POST
- Return: JSON
- Post Content:
```php
//
```

####Retrieving a recipe nutritional information
- Address: http://domain:port/api/meal/nutritional-information/\<recipe_id\>
- Method: GET
- Return: JSON
- Post Content:
```php
//
```

##Error Response
When a method finds an error during execution, it will return an Error Response.
- success = [true|false]
- general_message = returns a string showing the error description
- errors = can show one or alist of errors

```json
{
    success:false,
    general_message:"You have reached your max number of Foos for the day",
    errors: {
        last_name:"This field is required",
        mrn:"Either SSN or MRN must be entered",
        zipcode:"996852 is not in Bernalillo county. Only Bernalillo residents are eligible"
    }
} 
```
##Troubleshooting
###$HTTP_RAW_POST_DATA
Depending on the PHP version, it could happen during POST requests.

- Error message:

> Automatically populating $HTTP_RAW_POST_DATA is deprecated and will be removed in a future version. To avoid this warning set ‘always_populate_raw_post_data’ to ‘-1’ in php.ini and use the php://input stream instead. in Unknown on line 0

- How to solve it:
Configure your php.ini file as follows:
```
; Always populate the $HTTP_RAW_POST_DATA variable. PHP's default behavior is
; to disable this feature and it will be removed in a future version.
; If post reading is disabled through enable_post_data_reading,
; $HTTP_RAW_POST_DATA is *NOT* populated.
; http://php.net/always-populate-raw-post-data
always_populate_raw_post_data = -1
```
