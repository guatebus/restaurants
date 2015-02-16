Restaurants (Byteland)
======================

This is a Symfony 2.6 application that provides REST services for Restaurant, Person and Reservation entities.

Find a [Postman collection] (doc/restaurants.json.postman_collection) of crafted http requests at ./doc/restaurants.json.postman_collection. Every call in this collection works if performed immediately after the fixtures have been loaded.

The tests bootstrap script performs an automated install of the application.

Before performing the tests, the run-tests script will set up the project and environment for the test suite (it performs a composer install and loads fixtures to the db).

Run the test bootstrap script with:

    ./bin/run-tests

The entire test suite is executed by this script and all tests pass. This test suite, however, does not have 100% code coverage. It has some unit and functional tests to exemplify/serve as reference (see the Tests dir in each bundle for details).

For setting up in a non-web root dir, please refer to step 4 below.

To get a list of all of the application's endpoints (routes) perform:

    app/console router:debug

The application expects a JSON payload in POST and PUT requests, and will always return JSON.


Manual installation instructions are as follows:

1. Unpack the zip.
2. Composer is used to manage the project's dependencies. If you don't have composer installed, install it
   on the application's root dir (https://getcomposer.org/download/)
3. From the application's root dir run:

    'php composer.phar install'

4. If you did not install the application inside your web directory's document root, add a vhost config, restart
   your server service and edit your /etc/hosts file (eg. add a new host 'restaurants.dev')
5. Make sure your web server has write permissions in the app/cache and app/logs directories
6. Update the database-related parameters on the app/config/parameters.yml file (the project uses the mysql db)
7. Install the db and fixtures by running on the application root folder the following:

    'app/console doctrine:database:drop --force && app/console doctrine:database:create && app/console doctrine:schema:create && app/console doctrine:fixtures:load --append'

8. Access the application (http://restaurants.dev/app_dev.php/v1/YOUR_REST_ENDPOINT)

** Access prod environment through app.php (app developed in dev environment, app.php should work but not fully tested!)
