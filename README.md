# Webmart 2.0

A simple PHP framework for building web applications and websites.

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

### Constants

Webmart:

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

Your theme:

|Name|Origin|Description|
|:---|:---|:-----------|
|DIR_VIEW|Webmart.php|Root directory of Webmart's active theme.|
|DIR_ASSETS|Webmart.php|Subdirectory for the theme's assets.|
|DIR_CLASSES|Webmart.php|Subdirectory for the theme's classes.|
|DIR_CONTROLLERS|Webmart.php|Subdirectory for the theme's controllers.|
|DIR_TEMPLATES|Webmart.php|Subdirectory for the theme's templates.|
|DIR_JSON|Webmart.php|Subdirectory for the theme's JSON files.|

### Variables

### Methods
