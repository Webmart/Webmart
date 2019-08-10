# Webmart 2.0

A simple PHP framework for building web applications and websites.

### Installation

Clone this repository to your application's or website's folder:

```
cd {MYFOLDER}
git clone https://github.com/Webmart/webmart-2-0
```

Clone the [Flight repo](https://github.com/Webmart/flight) inside the `engine` subdirectory, to enable Webmart's routing:

```
cd engine/
git clone https://github.com/Webmart/flight
```

Finally, open `wm.php` and edit accordingly:

```
define('WM_THEME', ''); /** Set a theme folder */
define('WM_DEBUG', false); /** Enable debug mode */
define('WM_BASE', ''); /** Set the base URL */

define('WM_SITEMAP', false); /** Auto-generate a sitemap */
define('WM_ROBOTS', false); /** Auto-generate a robots file */

define('WM_HTTPS', false); /** Force HTTPs */
```

### Libraries

You can optionally load additional libraries inside the `engine/libs/` subdirectory.

So first of:

```
cd libs/
```

#### [Medoo](https://github.com/Webmart/Medoo)

```
git clone https://github.com/Webmart/Medoo
```

#### [Mobile Detect](https://github.com/Webmart/Mobile-Detect)

```
git clone https://github.com/Webmart/Mobile-Detect
```

### Boilerplate Theme
