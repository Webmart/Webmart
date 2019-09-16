<?php

/*!
* Webmart 2.0
* A simple PHP framework for building web applications and websites.
* https://github.com/Webmart
*/

class Config
{

    public static $version = '1.0';

    /** COOKIES to accept */

    public static $cookies = array();

    /** GET data to accept */

    public static $query = array();

    /** POST data to accept */

    public static $data = array();

    /** Database access */

    public static $db = array(
        'database_type' => '',
        'database_name' => '',
        'server' => '',
        'username' => '',
        'password' => ''
    );

    /** Set your Flight redirects. */

    public static $redirects = array(
        '301' => array(
            '/home' => '/'
        )
    );

    /** Set your Flight routes. */

    public static $routes = array(
        'example-(subpage-(finalpage))'
    );

}
