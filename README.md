# Laravel Imbalance Project

## Description
A REST API to allow frontend frameworks such as angular to use services provided.

## Server Requirements
* Composer
* PHP >= 5.5.9
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension

## Installation
1. Clone the repository to the desired location.
2. Run **_composer update_** to pull down components into the vendor folder.
3. Set storage and bootstrap/cache folders to be publicly read/write/executable **_sudo chmod -R 777 <folder name>_**.
4. Take a copy of the .env.save file and name it .env **_sudo cp .env.save .env_**, this file contains all configuration options for laravel.
5. Run the **_php artisan key:generate_** command to get a new application key, this should then be set in your .env file if it is not then set it.
6. Run the **_php artisan jwt:generate_** command to get a new key for the JWT token auth.
7. Change the database config in the .env file as well to desired database.
8. Rename your new app to something appropriate using the **_php artisan app:name <App Name>_** command.
9. If the app isn't working after the above command check the namespaces on files as it changes them.
10. You can also do a sudo tail -f /var/log/apahce2/error.log to see if there are errors.

App still not working? See laravel docs, turn on debug in the .env file and google search any errors you see.

## Live Install
1. In .env change APP_ENV to production and APP_DEBUG to false.
2. In .env setup mail setting to use your choice of mail service.

## PHPStorm
When running the composer install or update command a **_ide_helper.php_** file is generated that helps phpStorm recognise methods built by the factories.

You will also want to install the PHPStorm Laravel plugin, see [this link](http://blog.jetbrains.com/phpstorm/2015/01/laravel-development-using-phpstorm/) for more details.

You can also run the **_php artisan ide-helper:models_** command to auto generate model doc blocks.

See the link in the resources section for more info.

## Apache Config File
See the apache example folder for info on setting up apache .conf files.

Note the rewrite options used in these files, these are important and the site wont work properly without them.  
The aim of these rewrite rules is to allow the Authorization header to be picked up by apache, this header is used  
for the token based authO system.

## Useful commands
* **_php artisan cache:clear_** clears cache of app.
* **_php artisan routes_** shows apps current routes.
* **_php artisan clear-compiled_** clears complied classes, useful if you just added a new controller.
* **_php artisan optimize_** does the opposite of above.

## Resources
* [Laravel docs](http://laravel.com/docs/5.1)
* [Composer docs](https://getcomposer.org/doc/)
* [Lots of information on Laravel commands](http://laravel-recipes.com/contents)
* [JWT Auth wiki](https://github.com/tymondesigns/jwt-auth/wiki)
