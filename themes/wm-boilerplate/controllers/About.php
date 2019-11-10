<?php

/*!
* Webmart
* A basic PHP framework for web applications and websites.
* https://webmartphp.com/
*/

class About extends Theme
{

    public function __construct($route)
    {
        parent::__construct($route);

        Webmart::pass('title', 'Webmart - About');
        Webmart::pass('heading', 'About');
        Webmart::pass('welcome', 'mywebsite/about/');
    }

}
