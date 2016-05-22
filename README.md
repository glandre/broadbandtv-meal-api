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

##Requistes
- PHP5.6 or higher
- Composer
- SQL Database (MySQL is preferred, but you can use any PDO possibility)

Important: For the above tutorial, we are going to use MySQL Database

##How to Install
Download the zip file and unzip it in your local machine.

###Windows
After unziping the content file, open command prompt</b>

Access the API's source code directory
```
cd broadbandtv-meal-api
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

Using any text editor of your preference, edit file **/broadbandtv-meal-api/.env** according to your environment.

Close your text editor and go back to command prompt. At this point your application is well configured, but you need a few important information inside your database.
```
php artisan migrate --seed
```

Excellent! It's all done. Now you can use your Web Server Application to run this API.

You don't have any Web Server in your machine? No problem. Make sure you are inside **/broadbandtv-meal-api** directory and, using your command prompt, run the following command:
```
php artisan serve
```

