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

|Name|Description|
|:---|:-----------|
|DIR_|Root directory of the Webmart installation.|
|DIR_ENGINE|Directory of Webmart's engine.|
|DIR_CORE|Subdirectory of Webmart's core files.|
|DIR_LIBS|Subdirectory of Webmart's available libraries.|

|Name|Description|
|:---|:-----------|
|WM_THEME|Name of the active theme.|
|WM_DEBUG|Option to enable debugging and errors.|
|WM_BASE|Base URL of the Webmart installation.|
|WM_SITEMAP|Option to auto-create a sitemap.|
|WM_ROBOTS|Option to auto-create robots.txt.|
|WM_HTTPS|Option to force HTTPs (301).|

Your theme:

|Name|Description|
|:---|:-----------|
|DIR_VIEW|Root directory of Webmart's active theme.|
|DIR_ASSETS|Subdirectory for the theme's assets.|
|DIR_CLASSES|Subdirectory for the theme's classes.|
|DIR_CONTROLLERS|Subdirectory for the theme's controllers.|
|DIR_TEMPLATES|Subdirectory for the theme's templates.|
|DIR_JSON|Subdirectory for the theme's JSON files.|

### Variables

### Methods
