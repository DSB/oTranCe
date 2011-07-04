README
======

Installaing instructions:
 
- Create a MySQL database e.g. "translations" using PhpMyAdmin, MySQLDumper or the shell
- Import the db_schema.sql file (located in this folder) into the database using any of the above mentioned programs)
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

- start the application in your browser by calling the url of your vhost
- log in using the username "Tester" and the password "test"
