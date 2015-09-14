WP?LinkoScope 
============================

This is a simple clone of Hacker News built with the Yii2 framework.

I uses WordPress for its backend and can be configured to talk to either the WordPress.com API or the 
plugin used for locally hosted WordPress sites. 


REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 5.4.0.

You should also have composer installed. If you do not have [Composer](http://getcomposer.org/), 
you may install it by following the instructions 
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).



INSTALLATION
------------

Download or check out the code, then set up the requirements by typing:

~~~
composer global require "fxp/composer-asset-plugin:~1.0.0"
composer install
~~~

Now you should be able to access the application through the following URL, assuming `linkoscope` is the directory
directly under the Web root.

~~~
http://localhost/linkoscope/web/
~~~


CONFIGURATION
-------------

### Administration

The administration password should be changed in `config/params.php`

### API Connection

You will need to setup your API connection from the administration section.


DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources
