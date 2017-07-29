# PHPMVC

A custom and lightweight server-side MVC-based framework written in PHP.

## Put it work on Debian

```
$ sudo aptitude install apache2
$ sudo aptitude install mysql-server mysql-client
$ sudo aptitude install php
$ sudo aptitude install php7.0-mysql
$ sudo a2enmod rewrite
$ sudo /etc/init.d/apache2 restart
# install composer, and then
$ php <path_to_your_installation>/composer.phar install
```

## Local development

```
$ sudo aptitude install phpmyadmin
$ sudo mysql -u root -p
  > GRANT ALL PRIVILEGES ON * . * TO 'phpmyadmin'@'localhost' IDENTIFIED BY '<phpmyadmin password>';
  > FLUSH PRIVILEGES;

### add new MySQL users
  > CREATE USER 'jose'@'localhost' IDENTIFIED BY '<password>';
  > GRANT ALL PRIVILEGES ON * . * TO 'jose'@'localhost' IDENTIFIED BY '<password>';
  > FLUSH PRIVILEGES;

$ sudo a2enmod userdir
## $ sudo vim /etc/apache2/mods-available/php7.0.conf # enable PHP in user directories

# add the following to /etc/apache2/apache2.conf
<Directory /home/*/public_html/>
   Options FollowSymLinks
   AllowOverride All
</Directory>

$ sudo /etc/init.d/apache2 restart
```
