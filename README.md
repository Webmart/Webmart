# Webmart 2.0

A simple PHP framework for building web applications and websites.

Jump to:

- [Understand the basics](https://github.com/Webmart/webmart-2-0#framework)
- [How to work with Routing](https://github.com/Webmart/webmart-2-0#routing)
- [Configuring your Theme](https://github.com/Webmart/webmart-2-0#theme)
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

### Framework

The basic actions of the Webmart framework are, in the following order:

- Prepare the framework
- Load the theme and its settings
- Start the routing process
- Prepare the view
- Render the view

Webmart loads the theme's `Config.php` file to apply the settings. After that, it loads the theme's `Theme.php` controller, which acts as a global controller, unless a page OR view controller overrides it.

Which means your controllers can extend the Theme class.

With the help of the routing process supplied by Flight, Webmart performs the following:

```php
// if a page/view controller exists
require DIR_CONTROLLERS . 'Pagename.php';
new Pagename($params);

// if it doesn't
if (method_exists('Theme', 'routePagename')) {
    Theme::routePagename($params);
}
```

Webmart matches pages and views automatically, unless you override them.

```
mywebsite.com
=> loads Home.php OR executes routeHome()

mywebsite.com/about/team/
=> loads About.php AND Team.php if they exist OR executes routeTeam()
```

Webmart prepares the view your theme has collected and renders it in the following order:

- header.php
- pagename.php
- footer.php

If there's a redirect rule for that request in your theme, Webmart will oblige and redirect.

And that's pretty much it.

### Routing

Webmart follows the functionality of routing variables from Flight.

Which means if your theme's routing includes variables, then they will be treated as such.

```php
$routes = array(
    'blog-category-tag-(postname)', // option A
    'blog-(category-(tag-(postname)))' // option B
);

// routing for option A
new Tag($params);
$params[0] = 'pepe-sad-became-happy';

// routing for option B
new Blog($params);
$params[0] = 'jokes';
$params[1] = 'pepe';
$params[2] = 'pepe-sad-became-happy';

// in case there's no controller
Theme::routeBlog($params);
```

It's up to you how you want to handle the routing process.

Feel free to read up on [Flight](http://flightphp.com/learn/), optionally.

### Theme

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
