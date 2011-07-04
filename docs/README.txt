README
======

Installing instructions:

- Copy the file application/configs/defaultConfig.dist.ini to defaultConfig.ini, in the same folder, and change params to your needs.
- Create a MySQL database e.g. "translations" using PhpMyAdmin, MySQLDumper or the shell
- Import the db_schema.sql file (located in this folder) into the database using any of the above mentioned programs)
(Note: if you changed the table names in defaultConfig.ini you must edit the file and change the table names here too!)

There are two special types of getting access to oTranCe on your webserver.

1. Using and Setting Up a VHOST
=====================

First enable virtual hosts in Apache by setting the value of "NameVirtualHost" to "*:80".
This is done in the Apache configuration files (default location: /etc/apache2/listen.conf) and looks like:

NameVirtualHost *:80


The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "E:/PHP/oTranCe/public"
   ServerName otrance.local

   # Set application enviroenment can be development, staging, testing or production
   SetEnv APPLICATION_ENV production

   <Directory "E:/PHP/oTranCe/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>

In this case you have to add an entry to /etc/hosts file for this VHOST, using
the name of the "ServerName" configuration var (see above).

E.g.: 191.168.1.2  otrance.local


Setting Up an ALIAS
===================

If you won't use a VHOST, you can also use an alias.

Here is a sample Apache configuration for this case.

Alias /oTranCe "/srv/www/otc/public/"

<Directory "/srv/www/otc/public/">
        Options FollowSymlinks +ExecCGI
        AllowOverride All
        Order allow,deny
        Allow from all
</Directory>

Copy the file public/.htaccess.dist to .htaccess, in the same folder, and change the RewriteBase line to your alias.
In our example the RewriteBase looks like:

RewriteBase /oTranCe



- start the application in your browser by calling the url of your vhost
- log in using the username "Tester" and the password "test"
