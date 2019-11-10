<?php

/*!
* Webmart
* A basic PHP framework for web applications and websites.
* https://webmartphp.com/
*/

class Config
{

    /** Theme VERSION */

    public static $version = '1.0';

    /** COOKIES data to accept */

    public static $cookies = array();

    /** GET data to accept */

    public static $query = array(
        'utm_source',
        'medium',
        'ref'
    );

    /** POST data to accept */

    public static $data = array();

    /** FLIGHT redirect requests to pages with an HTTP response */

    public static $redirects = array(
        '301' => array(
            '/home' => '/',
            '/about-us' => 'about/',
            '/legal/terms-of-service' => 'legal/terms/'
        )
    );

    /** FLIGHT route pages with dynamic parameters */

    public static $routes = array(
        'about', // absolute rule
        'about/team',
        'how-it-works',
        'legal-(subpage)' // dynamic rule, ex. legal/privacy/, legal/terms/
    );

    /** SEO disallow pages to be indexed on robots.txt **/

    public static $noindex = array(
        'home',
        'about-us',
        'legal/terms-of-service'
    );

    /** GOOGLE load supported libraries */

    public static $libs = array(
        'jquery' => '3.4.1',
        'maps' => 'API_KEY'
    );

    /** GOOGLE load fonts with weights and subsets */

    public static $fonts = array(
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

    public static $bootstrap = 'bundle'; // false, true, 'bundle'

    /** Enable Hotjar tracking - does not work if WM_DEBUG is enabled */

    public static $hotjar = false;

    /** Enable Google Analytics (Gtag) - does not work if WM_DEBUG is enabled */

    public static $gtag = '';

}
