<?php

/*!
* Webmart 2.0
* A simple PHP framework for building web applications and websites.
* https://github.com/Webmart
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
