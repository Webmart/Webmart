<?php

/*!
* Webmart
* A basic PHP framework for web applications and websites.
* https://webmartphp.com/
*/

class Theme
{

    protected $route;

    /**
    * @method
    */

    public function __construct($route)
    {
        $this->route = $route;

        Webmart::pass('title', 'Webmart - My First Website');
        Webmart::pass('heading', 'Hello, Webmart.');
    }

    /**
    * @method
    */

    public function routeHome()
    {
        Webmart::pass('welcome', 'My first website.');
    }

}
