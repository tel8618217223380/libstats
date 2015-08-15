

# FAQs and other tips #

## Why is libstats automatically logging me in and redirecting to the Add Question page? ##

In /Init.php, there is an auto admin login setting.
```
define('AUTO_ADMIN_LOGON', false);
```
false, requires a login; true automatically logs the admin in. Change this to false and users must log in then.

## libstats is unable to connect to the database on install. It takes forever to load and then get Library Stats: Error "I messed up and I'm sorry." ##
Depending on the host name of the server, the SQL statement assigning privileges to the account my need the hostname changed to "localhost":
```
GRANT SELECT, UPDATE, DELETE, INSERT ON libstats.* TO libstats_account_name@'localhost' IDENTIFIED
BY 'libstats_account_name_password';
```

## MySQL is reporting corrupt tables, what do I do? ##
Errors messages such as the following are a problem with MySQL, not libstats:

> Apr 13 18:20:17 `[server name]` /etc/mysql/debian-start`[5263]`: WARNING: mysqlcheck has found corrupt tables Mar 13 18:20:17 `[server name]` /etc/mysql/debian-start`[5263]`: libstats.cookie\_logins Mar 13 18:20:17 `[server name]` /etc/mysql/debian-start`[5263]`: warning  : 1 client is using or hasn't closed the table properly Mar 13 18:20:17 `[server name]` /etc/mysql/debian-start`[5263]`: libstats.questions Mar 13 18:20:17 `[server name]` /etc/mysql/debian-start`[5263]`: warning  : 1 client is using or hasn't closed the table properly


One way to resolve the above error is to run the following command:
```
mysqlcheck --repair --user=username --password=password databasename
```

For more information about mysqlcheck, please refer to http://dev.mysql.com/doc/refman/5.0/en/mysqlcheck.html.

As always, **please backup the database BEFORE running mysqlcheck**.

## In reporting, is there a way to group certain areas under a category? ##
In the question\_types table there is a column called parent\_list. This column can be used to group question types into similar categories. The parent\_list column also is in question\_formats, patron\_types and locations tables. When you create a report, pull back the parent\_list and manage it that way. To organize and assign rows to the parent\_list manually add them to the database or use a web face administration interface, such as phpMyAdmin.

## I'm trying to search for a three (3) character string. Why is it not returning results? ##
The fulltext minimum word length default value is four (4) characters. To find what the current fulltext minimum word length default value (ft\_min\_word\_len), run the following command as an SQL statement:
```
SHOW VARIABLES like 'ft_min_word_len';
```

**NOTE** This will change the settings for the entire database server, thus effecting every database.

To change the ft\_min\_word\_len value:

  1. Locate the my.cnf in Linux or my.ini for Windows.
  1. Open my.`[cnf/ini]`
  1. In the `[mysqld]` stanza, add the following:
```
##
# This is for modification of the database running libstats
#
# Default Fulltext minimum word lenght is four (4) characters. This file
# modifies the settings and changes it to three (3) characters.
#
# In a linux installation, this file should go in the following directory:
#       /var/lib/mysql/[database name]/my.cnf
#
# Windows installations, the file should be called:
#       my.ini
#
# For more information on this, please review MySQL documentation:
#       http://dev.mysql.com/doc/refman/5.1/en/option-files.html
#
# After adding this file to the proper directory, restart MySQL.
# MySQL can be restarted in my linux distros by running the following command as root/superuser:
#       /etc/init.d/mysql restart
#
# change fulltext min character lenght to 3
# http://dev.mysql.com/doc/refman/5.1/en/fulltext-fine-tuning.html
ft_min_word_len=3
```
  1. Restart the MySQL database server. In Linux, use the root/super-user account to run the following command:
```
/etc/init.d/mysqld restart
```
  1. Once restarted, the index will need rebuilt. Run the following SQL command to rebuild the index:
```
REPAIR TABLE questions QUICK;
```
**NOTE** If query cache is enabled, this QUICK method may not work.