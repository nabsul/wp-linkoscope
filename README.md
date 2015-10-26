LinkoScope 
============================

This is a clone of Hacker News built with the Yii2 framework. It uses WordPress for its backend.

It can be configured to talk to either the WordPress.com API or the WP-API (v2) plugin.


REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 5.6.0.

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

The /web folder of this project is the folder from which web pages are served. 
In Apache you will want to make that folder your DocumentRoot. 
Alternatively, you can access the /web folder explicitly in the url path (ie: http://localhost/linkoscope/web/), 
which is ok for development but not very secure:

GENERAL CONFIGURATION
-------------

### Administration Password

To access the administration section of the app, you will need to create an 
admin password file in /runtime/adminPass.txt 

You can then got to [rootUrl]/admin and login with username admin and the password you specified in the file.

For details on setting up each type of connection (COM/ORG), see the following sections.

WORDPRESS.COM API DETAILS
-------------

The WordPress.com API does not support custom post types. Shared links are therefore added as regular posts and comments. 
The result is that users that go to the blog's website will see the shared links as regular posts. 
For this reason it's recommended to create and WordPress.com blog that is dedicated to LinkoScope and not use it 
through the regular interface.  

You will then need to create application credentials in the "My Application" section of your WordPress.com account. 
Details can be found at [https://developer.wordpress.com/docs/oauth2/].

Note that:

- The redirect URL must point to [linkoscope-root-url]/site/login (ie: http://mysite.com/site/login)
- Localhost is accepted, so you can create credentials for your local installation
- You will need to create separate credentials for each location you install the App to.
 
Users can only log into the site if:

- They are members of the site (you can add new users by inviting them from the blog administration panel)
- The have Editor or Administrator privileges

WP-API PLUGIN DETAILS
-------------

For LinkoScope to work with a self-hosted WordPress site, the following plugins must be installed:

- Oauth1: https://github.com/WP-API/OAuth1
- WP-API: http://v2.wp-api.org/
- LinkoScope: https://github.com/nabsul/wp-linkoscope-plugin

You will need to setup Oauth1 credentials for your application. 
Instructions are found here: http://v2.wp-api.org/guide/authentication/
 
The LinkoScope plugin creates custom post and comment types which will not interfere with regular post types. 
You therefore don't need to have a separate blog dedicated for LinkoScope.

Members will need Author, Editor or Administrator privileges to post links to the site
