If you want to run the tests in a local test environment, do the following:

MySQL:
- create a database "phpunuit_otc"
- create a MySQL user "phpunit" with the password "phpunit" and grant all rights for the database "phpunit_otc"
- import the file db_schema.sql into the database "phpunit_otc"

In your shell you can now cd to the tests folder and run:

phpunit

Phpunit will find and use the configuration file "phpunit.xml" and will run all found test cases.

