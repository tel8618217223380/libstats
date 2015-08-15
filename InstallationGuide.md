## Requirements ##

You may be able to get around these, but your life will be way easier if you're running:
  * [PHP 5.x](http://www.php.net/)
  * [MySQL 5.x](http://www.mysql.org/downloads/mysql/5.0.html#downloads)
  * [Pear::DB](http://pear.php.net/package/DB)
  * [Apache](http://httpd.apache.org/) with [mod\_rewrite](http://httpd.apache.org/docs/2.0/mod/mod_rewrite.html)

## Installation Instructions ##
### Installing Libstats on a LAMP or WAMP platform ###
**Introduction:**

Libstats is a Web application that can be run on Linux or Windows. This installation guide applies to a LAMP or WAMP server, Apache on Linux or Windows. If you want to run a stand alone local version or have a test version on your desktop you can set up a WAMP stack on a Windows computer. A good WAMP setup is the Web Developer Server Suite. The community edition is free and can be downloaded from http://www.devside.net/server/webdeveloper. NOTE: This WAMP package would not be appropriate for a production server unless significant security configurations were made. The default installation is for development
purposes. Configuring the package for production is certainly possible, but is beyond the scope of this instruction.

### 1. Environment ###
Make sure your server environment is ready to run Libstats. You will need:

  * PHP 5.x
  * MySQL 5.x
  * Pear::DB
  * Apache with mod\_rewrite

Let’s take a look at the necessary configurations for each one of these.

**Apache**

  * On your web root, find your Apache config folder (apache\config). Find the httpd.conf file and open it in notepad or whatever text editor you use. In the list of Loadmodules
> find
```
LoadModule rewrite_module modules/mod_rewrite.so
```
Make sure the line is uncommented.

  * Make sure you have symlinks turned on in the correct directory where libstats will be living. An example of a safe way to do this: where directory is your main directory…and Libstats is living in the libroot directory
```
<Directory />
    Options FollowSymLinks
   AllowOverride Limit
    Order deny,allow
    Deny from all
    Satisfy all
</Directory>

<Directory "/htdocs/libroot/">
    Options -Indexes -FollowSymLinks SymLinksIfOwnerMatch 
    Order allow,deny
    Allow from all
    AllowOverride All
    AddType text/html .php
    AddHandler application/x-httpd-php .php
</Directory>
```
That should take care of Apache for now.

**MySQL 5**

Fine as it is.

**PHP5 and PEAR::DB**

Most PHP5 installations include PEAR but some do not. Look in your PHP5 directory for PEAR. If you have it check for PEAR::DB. If you don’t have these you will need to install them. PEAR packages are located at http://pear.php.net/index.php. You download PEAR packages as tarballs and install them using a PEAR package manager, which also may or may not be part of your PHP5 installation.  Instructions for using (getting) the PEAR package manager and installing PEAR packages is located at http://pear.php.net/manual/en/installation.php.

### 2. Unzip the files ###

Download the latest version of Libstats, unpack it and Save all the files somewhere so you'll be able to see them with a web browser. I'll call the file location libroot.

### 3. Set up the database ###

This will prepare the database server to store all of your menu values and questions. You'll also want to set up a special database user, so that if there's a bug in this software, hackers won't be able to do as much damage. In our example we will name our database libstats and our user is libuser.

  * Create the database.
If  using the command line, phpmyadmin or a graphical utility by using its Create Database feature.

The SQL you'll want is:
```
CREATE DATABASE libstats;
```

  * Create the database user.
You'll probably want to make sure only your web server can do things to the database, and also make sure that the statistics software can't do anything nefarious. The user will need SELECT, UPDATE, INSERT, and DELETE privileges.

The SQL to do this command is:
```
GRANT SELECT, UPDATE, DELETE, INSERT ON libstats.* TO libuser@'webserver.example.edu' IDENTIFIED BY 'libpass';
```

  * Create tables and data.
Import the libstats.sql file to your database of choice. The way you'll do this depends on the program that you're using to make changes. With the command-line program:
```
mysql -u root -p libstats < libstats.sql
```

  * With phpmyadmin or a graphical utility, choose the libstats database and then use the “import” function to create the tables by uploading the libstats.sql file which is located in the libstats folder. If you receive a sql syntax error message you many need to clean up the file before importing. Simply open the libstats.sql file in a text editor and delete the first couple lines of code up to
```
 CREATE TABLE admin
```

Your import should now work.

### 4. Edit the Init.php file ###
Use a text editor to edit the Init.php file, and change the values of $dbUser, $dbPass, $dbHost, and $dbName to the values you used when creating the database and granting user access.

### 5. Set up URL rewriting ###
The stats program uses a feature called URL rewriting to help the programmers make our PHP code easier to debug. Essentially, it needs to send everyone looking for a page ending in ".do" to Controller.php. Change libstats to the place your installation will live.

  * In the libstats directory find the .htaccess file and open it in a text editor.
  * Point the rewrite base to the directory where you placed libstats in relation to your Web root, for example:
```
RewriteBase /libstats/
```

Or as in our example:
```
RewriteBase /libroot/libstats/
```

### 6. Restart Apache ###
> Restart the Web server so that all your configuration changes take up.

### 7. Start using it! ###
Go to the new database in your web browser! For example: http://localhost/libstats or http://localhost/libroot/libstats
Depending on where you installed the application.

Your admin logon has been set up; the login is: Username: admin Password: changeme.
At this point, you should be up and running. You should change the admin password as soon as you log in, so other people can't get in and create problems or bogus data. From the admin interface, create a normal user, change the values for the pulldown menus, create separate libraries if you like and start logging stats!








