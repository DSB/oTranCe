README
======

Installing instructions:

- open the file application/configs/defaultConfig.dist.ini, change params to your needs and save it as defaultConfig.ini to the same folder
- Create a MySQL database e.g. "translations" using PhpMyAdmin, MySQLDumper or the shell
- Import the db_schema.sql file (located in this folder) into the database using any of the above mentioned programs)
(Note: if you changed the table names in defaultConfig.ini you must edit the file and change the table names here too!)

- Create a vhost

Setting Up Your VHOST
=====================

The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "E:/PHP/oTranCe/public"
   ServerName .local

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV production

   <Directory "E:/PHP/oTranCe/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>

- open the file public/.htaccess.dir, change the rewrite base line to your alias and save it as .htaccess to the same folder
e.g.:

RewriteBase /oTranCe

- start the application in your browser by calling the url of your vhost
- log in using the username "Tester" and the password "test"
