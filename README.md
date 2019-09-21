# Webmart

A basic PHP framework for web applications and websites. [http://webmartphp.com/](http://webmartphp.com/)

### Required

- PHP: >=5.5.*
- [Flight](https://github.com/mikecao/flight/), by mikecao
- Apache

Released under the [MIT License](https://github.com/Webmart/webmart/blob/master/LICENSE.md).

## How to Install

The first time you'll open in your browser, Webmart will generate `.htaccess` and `wm.php` for you.

### Setup manually

Download [Webmart](https://github.com/webmart/webmart/archive/master.zip). Unzip in your directory.

Download [Flight](https://github.com/mikecao/flight/archive/master.zip). Unzip flight folder inside `engine/core/` (make sure it's not flight/flight).

Open in your browser.

### Install with Composer

Available versions [here](https://packagist.org/packages/webmart/webmart).

Run:

```
composer require webmart/webmart
```

Create an `index.php` file in your root directory. Require the autoloader:

```php
require 'vendor/autoload.php';
```

Open in your browser.

## How to Edit

In your selected theme under `/webmart/view/`, open `Config.php` and start editing.

Learn more about the features and supported libraries at [http://webmartphp.com/](http://webmartphp.com/docs/).


###### Co-created by [George Kary](http://georgekary.com/), [Valandis Zotos](https://github.com/BalzoT), [John Dimas](https://github.com/jdimas87).<br>Special thanks to [mikecao](https://github.com/mikecao).
