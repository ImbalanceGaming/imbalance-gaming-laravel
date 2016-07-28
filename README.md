# Laravel Imbalance Project

## Description
A REST API to allow frontend frameworks such as angular to use services provided.

## Server Requirements
* OpenSSL
* Composer
* PHP >= 5.5.9
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* Envoy task runner **_composer global require "laravel/envoy=~1.0"_**

## Installation
1. Clone the repository to the desired location.
2. Run **_composer install_** to pull down components into the vendor folder.
3. Set storage and bootstrap/cache folders to be publicly read/write/executable **_sudo chmod -R 777 <folder name>_**.
4. Take a copy of the .env.save file and name it .env **_sudo cp .env.save .env_**, this file contains all configuration options for laravel.
5. Run the **_php artisan key:generate_** command to get a new application key, this should then be set in your .env file if it is not then set it.
6. Run the **_php artisan jwt:generate_** command to get a new key for the JWT token auth.
7. Change the database config in the .env file as well to desired database.
8. Rename your new app to something appropriate using the **_php artisan app:name <App Name>_** command.
9. If the app isn't working after the above command check the namespaces on files as it changes them.
10. You can also do a sudo tail -f /var/log/apahce2/error.log to see if there are errors.

App still not working? See laravel docs, turn on debug in the .env file and google search any errors you see.

## Setup Envoy
Envoy is used to run console tasks via SSH, for it to do this it need some setup first.  
All commands will be run through www-data user so this user needs to be setup with SSH access to what ever server it wants to run the commands on.  

1. First setup a user called envoy on the servers to be ssh'ed to and make give them sudo rights.
2. Create a .ssh directory in the new users home directory and create a file in that folder called authorized_keys.
3. On the server that runs envoy run **_sudo su_** to become root then run **_su -s /bin/bash www-data_** to become the www_data user.
4. Run **_composer global require "laravel/envoy=~1.0"_** to install envoy for the www-data user.
5. Create a .ssh directory in the home folder (will be /var/www for www-data).
6. Move into the .ssh folder and run **_ssh-keygen -t rsa_**, press enter at each prompt.
7. Open the id_rsa.pub file that was created and copy the key line.
8. Paste this key line into each of the authorized_key files for the envoy user (Make sure you got the whole line).
9. Run **_sudo chmod 600 authorized_keys_** on each of the authorized_key files.
10. On the server that runs envoy run **_ssh envoy@ServerAddress_** to make sure the connection works and authorize the connections as a known host.
11. If the above dosn't work run the **_ssh -v envoy@ServerAddress_** to get more output about why it is failing.

The envoy user on the server will need to be added to the www-data group and this group will need read, write and execute permissions to the /var/www/html folder so that projects can be deployed.

The above steps will get SSH setup for Envoy the next step outlined below will need to be taken to allow certain sudo commands to be run without requiring a password.

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
