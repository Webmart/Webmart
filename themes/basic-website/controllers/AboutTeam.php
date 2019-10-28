<?php

/*!
* Webmart 2.0
* A simple PHP framework for building web applications and websites.
* https://github.com/Webmart
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
