# Webmart 2.0

A simple PHP framework for building web applications and websites.

Jump to:

- [Intro](https://github.com/Webmart/webmart-2-0#framework)
- [Configuring your Theme](https://github.com/Webmart/webmart-2-0#theme)
- [How to work with Routing](https://github.com/Webmart/webmart-2-0#routing)
- [How to work with the View](https://github.com/Webmart/webmart-2-0#templating)
- [Constants](https://github.com/Webmart/webmart-2-0#constants)
- [Variables](https://github.com/Webmart/webmart-2-0#variables)
- [Methods](https://github.com/Webmart/webmart-2-0#methods)
- [Libraries](https://github.com/Webmart/webmart-2-0#libraries)

### Installation

Clone this repository to your application's or website's root folder:

```
cd {ROOTDIRECTORY}
git clone https://github.com/Webmart/webmart-2-0 {DIRECTORYNAME}
```

Use the [boilerplate theme](https://github.com/Webmart/boilerplate-2-0) to get started:

```
cd {ROOTDIRECTORY}/{DIRECTORYNAME}
git clone https://github.com/Webmart/boilerplate-2-0 view/{THEMENAME}
```

Finally, open `wm.php` and edit accordingly:

```php
define('WM_THEME', ''); /** Set a theme folder */
define('WM_DEBUG', false); /** Enable debug mode */
define('WM_BASE', ''); /** Set the base URL */

define('WM_SITEMAP', false); /** Auto-generate a sitemap */
define('WM_ROBOTS', false); /** Auto-generate a robots file */

define('WM_HTTPS', false); /** Force HTTPs */
```

Cheers.

## Framework

Webmart prepares the framework and looks for an active theme.

If the active theme exists, Webmart loads `Config.php` and `Theme.php`. After starting the routing process, Webmart follows the redirects and routes applied by the theme.

If a controller exists for that view OR page, Webmart loads it and creates a new instance.

If a `Theme.php` method exists starting with `route` and the view OR page name, it executes that method.

*For example:*

```php
// controller exists
require_once DIR_CONTROLLERS . 'Login.php';
new Login();

// controller doesn`t exist
if (method_exists('Theme', 'routeLogin')) {
    Theme::routeLogin();
}
```

The `Theme.php` controller acts as the global controller for all views and pages.

### Theme

### Routing

Webmart follows the functionality of routing variables from Flight. By following the rules applied inside the `Config::$routes` array of the active theme, it returns the parameters to the new instance.

Which means these variables are available in your controllers, through the constructor.

*For example:*

```php
$routes = array(
    'blog-(year-(month-(day-(postname))))'
);

require DIR_CONTROLLERS . 'Blog.php';
new Blog($params);

$params[0] = '2019';
$params[1] = 'august';
$params[2] = '05';
$params[3] = 'pepe-sad-became-happy';
```

However, the Flight object is not available outside the Webmart class.

Feel free to read up on [Flight](http://flightphp.com/learn/).

### Templating

### Constants

#### Webmart

|Name|Origin|Description|
|:---|:---|:-----------|
|DIR_|Webmart.php|Root directory of the Webmart installation.|
|DIR_ENGINE|Webmart.php|Directory of Webmart's engine.|
|DIR_CORE|Webmart.php|Subdirectory of Webmart's core files.|
|DIR_LIBS|Webmart.php|Subdirectory of Webmart's available libraries.|

|Name|Origin|Description|
|:---|:---|:-----------|
|WM_THEME|wm.php|Name of the active theme.|
|WM_DEBUG|wm.php|Option to enable debugging and errors.|
|WM_BASE|wm.php|Base URL of the Webmart installation.|
|WM_SITEMAP|wm.php|Option to auto-create a sitemap.|
|WM_ROBOTS|wm.php|Option to auto-create robots.txt.|
|WM_HTTPS|wm.php|Option to force HTTPs (301).|

#### Theme

|Name|Origin|Description|
|:---|:---|:-----------|
|DIR_VIEW|Webmart.php|Root directory of Webmart's active theme.|
|DIR_ASSETS|Webmart.php|Subdirectory for the theme's assets.|
|DIR_CLASSES|Webmart.php|Subdirectory for the theme's classes.|
|DIR_CONTROLLERS|Webmart.php|Subdirectory for the theme's controllers.|
|DIR_TEMPLATES|Webmart.php|Subdirectory for the theme's templates.|
|DIR_JSON|Webmart.php|Subdirectory for the theme's JSON files.|

### Variables

#### Webmart

|Name|Type|Description|
|:---|:---|:-----------|
|Webmart::$page|string|Name of the current page.|
|Webmart::$view|string|Name of the assigned view to the current page.|
|Webmart::$url|string|URL of the current page.|
|Webmart::$cookies|array|Collection of request cookies, as accepted by the theme.|
|Webmart::$query|array|Collection of GET data, as accepted by the theme.|
|Webmart::$data|array|Collection of POST data, as accepted by the theme.|

#### Theme

|Name|Type|Description|
|:---|:---|:-----------|
|Config::$version|string|Version of the active theme.|
|Config::$cookies|array|Cookies accepted by the theme.|
|Config::$query|array|GET data accepted by the theme.|
|Config::$data|array|POST data accepted by the theme.|
|Config::$db|array|Database settings.|
|Config::$redirects|array|Redirect rules and protocols applied by the theme.|
|Config::$routes|array|Routing rules accepted by the theme.|

### Methods

### Libraries
