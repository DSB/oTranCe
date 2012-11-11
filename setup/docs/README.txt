README
======

Installing instructions:

- Copy the folder "public" and all file in it to your web sever.
Attention: include the folder "public" because other folders will be created on the same level.
Do not only copy the files inside this folder!
- Configure a vhost or an alias that has defined the "public" folder as it's root web folder.
- Start the setup of the application in your browser by calling the url http://yourVhost/setup/
- Follow the instructions of the setup guide.

----------------------------------------------
How to configure a vhost:

1. Using and Setting Up a VHOST
===============================

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
----------------------------------------------
How to setup an alias:

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

