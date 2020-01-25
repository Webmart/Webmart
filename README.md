# Webmart

A basic PHP framework for web applications and websites. [http://webmartphp.com/](http://webmartphp.com/)

### Required

- PHP: >=5.6.*
- [Flight](https://github.com/mikecao/flight/), by mikecao
- Apache

Released under the [MIT License](https://github.com/Webmart/webmart/blob/master/LICENSE.md).

## 1/2 Install

The first time you'll open in your browser, Webmart will auto-generate `.htaccess` and `wm.php` for you.

### A. Setup manually

- Download [Webmart](https://github.com/webmart/webmart/archive/master.zip) and unzip in your directory.
- Download [Flight](https://github.com/mikecao/flight/archive/master.zip) and unzip inside `webmart/engine/` (make sure it's not flight/flight).
- Setup your theme folders inside `webmart/themes/` - feel free to use the [Boilerplate](https://github.com/Webmart/wm-boilerplate/archive/master.zip) theme.
- Open in your browser.

### B. Install with Composer

Available versions on [Packagist](https://packagist.org/packages/webmart/webmart). Run:

```
composer require webmart/webmart
```

Create an `index.php` file in your root directory. Require the autoloader:

```php
require 'vendor/autoload.php';
```

Setup your theme folders inside `themes/` - feel free to use the [Boilerplate](https://github.com/Webmart/wm-boilerplate).

Open in your browser.

## 2/2 Configure

See the [docs](http://webmartphp.com/docs) for more.
