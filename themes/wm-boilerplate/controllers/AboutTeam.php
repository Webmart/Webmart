<?php

/*!
* Webmart
* A basic PHP framework for web applications and websites.
* https://webmartphp.com/
*/

class AboutTeam extends About
{

    public function __construct($route)
    {
        parent::__construct($route);

        Webmart::pass('heading', 'Team');
        Webmart::pass('welcome', 'mywebsite/about/team/');
    }

}
