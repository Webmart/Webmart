# Webmart

A basic PHP framework for web applications and websites. [http://webmartphp.com/](http://webmartphp.com/)

### Required

- PHP: >=5.6.*
- [Flight](https://github.com/mikecao/flight/), by mikecao
- Apache

### Suggested

- [mobiledetect/mobiledetectlib](https://packagist.org/packages/mobiledetect/mobiledetectlib)
- [catfan/medoo](https://packagist.org/packages/catfan/medoo)

Released under the [MIT License](https://github.com/Webmart/webmart/blob/master/LICENSE.md).

## 1/2 Install

Webmart auto-generates `.htaccess` and `wm.php` for you.

### A. Setup manually

- Download [Webmart](https://github.com/webmart/webmart/archive/master.zip) and unzip in your directory.
- Download [Flight](https://github.com/mikecao/flight/archive/master.zip) and unzip inside `webmart/engine/` (make sure it's a subfolder named `flight`).
- Setup your theme folders inside `webmart/themes/` - feel free to use the [Boilerplate](https://github.com/Webmart/wm-boilerplate/archive/master.zip) theme.
- Open in your browser.

### B. Install with Composer

See available versions on [Packagist](https://packagist.org/packages/webmart/webmart) or [GitHub](https://github.com/Webmart/webmart/releases).

Navigate to your directory and run:

```
composer require webmart/webmart
```

Create an `index.php` and require the autoloader:

```php
require 'vendor/autoload.php';
```

Setup your theme folders inside `webmart/themes/` - feel free to use the [Boilerplate](https://github.com/Webmart/wm-boilerplate/archive/master.zip) theme.

Open in your browser.

## 2/2 Configure

See the [docs](http://webmartphp.com/docs) for more.
