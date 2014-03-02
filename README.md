# Endo

A light-weight PHP MVC framework.

**DISCLAIMER:** Endo is provided as-is, and the author is not liable for any database losses that might occurr due to lax security.

---

## Using Endo

### Basic Concepts

Anyone familiar with CakePHP, Laravel, RoR or other Ruby-on-Rails-types will already know the drill:

- your development occurs in `/app`
- `webroot` folders are public (helpfully called `public` in some other frameworks... :) )


## Basic Setup

### Directory Structure

	/
		.htaccess
		app/
			webroot/
				index.php
			.htaccess
			configure.php
			domain.inc


### Installing Endo

This will install **Endo** to `/endo` as a sibling to `/app`:

```Shell
git submodule add endo https://github.com/nemoDreamer/endo.git
git submodule init && git submodule update
```

## Required Files

### `/.htaccess` URL re-writes

```ApacheConf
<IfModule mod_rewrite.c>
	RewriteEngine  on

	# endo webroot
	RewriteRule    ^assets/?(.*)$ endo/webroot/$1 [L]

	# default
	RewriteRule    ^$ app/webroot/ [L]
	RewriteRule    (.*) app/webroot/$1 [L]
</IfModule>
```


### `/app/.htaccess` to protect `/app`

```ApacheConf
<IfModule mod_rewrite.c>
	RewriteEngine  on
	RewriteRule    ^$ webroot/       [L]
	RewriteRule    (.*) webroot/$1   [L]
</IfModule>
```


### `/app/domain.inc` to define domain and SQL connection string

```PHP
<?php
	define('DOMAIN', 'my-awesome-app.com');
	define('MYACTIVERECORD_CONNECTION_STR', 'mysql://<user>:<pwd>@'.$_ENV['DATABASE_SERVER'].'/<database>');
?>
```


### `/app/configure.php` defines application-specific constants

```PHP
<?php

/**
 * APP Configure
 * Define application-specific constants
 * (can override ENDO constants defined in `/<ENDO_ROOT>/configure.php`)
 *
 * @author Philip Blyth
 */

define('DEBUG',                             LOCAL ? 1 : (STAGING ? 1: 0)); // 0:none | 1:basic | 2:basic+smarty

// --------------------------------------------------
// LAYOUT
// --------------------------------------------------

define('SITE_NAME',                         'My Awesome App');
define('FOOTER',                            'Joyfully produced by <a href="mailto:me@my-awesome-app.com">me</a>, yay!');

// --------------------------------------------------
// ROOTS && DB
// --------------------------------------------------

define('ENDO_ROOT',                         ROOT.'endo'.DS); // default

// switch for local dev environment
if (LOCAL) {
  define('DOMAIN',                          'localhost.com'); // point /etc/hosts localhost.com to 127.0.0.1
  define('MYACTIVERECORD_CONNECTION_STR',   'mysql://root:root@localhost/endo_my-awesome-app');
} else {
  include(APP_ROOT.'domain.inc');
}

// --------------------------------------------------
// ADMIN
// --------------------------------------------------

define('ADMIN_DEFAULT_CONTROLLER',          'posts');

?>
```

### `/app/webroot/index.php` loads the app

```PHP
<?php

/**
 * Index
 * gets called first, via .htaccess
 *
 * this file needs to be overwriteable by any ulterior version!
 * (use bootstrap for app-specific additions)
 *
 * @author Philip Blyth
 */

define('LOCAL', strpos($_SERVER['SERVER_NAME'], 'localhost') !== false);
define('STAGING', strpos($_SERVER['HTTP_HOST'], 'staging') !== false);
define('DS', DIRECTORY_SEPARATOR); // do not change!

// --------------------------------------------------
// ROOTS
// --------------------------------------------------

define('WEB_ROOT', dirname(__FILE__).DS); // do not change!
define('APP_ROOT', dirname(dirname(__FILE__)).DS); // do not change!
define('ROOT', dirname(dirname(dirname(__FILE__))).DS); // do not change!

// --------------------------------------------------
// CONFIG
// --------------------------------------------------

require_once(APP_ROOT.'configure.php');

// --------------------------------------------------
// CORE will handle the rest...
// --------------------------------------------------

require_once(ENDO_ROOT.'core.php');

?>
```

## TODO

- Add LICENSE
- ! Use prepared statements !
- Add sample app, instead of lengthy "Required Files" section.
