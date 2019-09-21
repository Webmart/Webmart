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

    /** Disallow pages to be indexed - robots.txt **/

    public static $noindex = array(
        'home'
    );

    /** Load Google Hosted Libraries */

    public static $googlelibs = array(
        'jquery' => '3.4.1',
        'maps' => 'API_KEY'
    );

    /** Load Google fonts */

    public static $googlefonts = array(
        'Fira Sans' => array(
            'weights' => array(
                '300i',
                '400',
                '400i'
            ),
            'subsets' => array(
                'cyrillic-ext',
                'greek-ext'
            )
        )
    );

    /** Load Bootstrap */

    public static $bootstrap = 'bundle';

}
