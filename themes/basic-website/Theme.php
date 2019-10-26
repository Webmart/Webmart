<?php

/*!
* Webmart
* A basic PHP framework for web applications and websites.
* https://webmartphp.com/
*/

class Theme
{

    public $route;

    public static $seo = array();

    /**
    * @method
    */

    public function __construct($route)
    {
        $this->route = $route;
        self::$seo = Webmart::getJSON('seo');

        Webmart::addValue('version', \Config::$version);

        if (isset(self::$seo[Webmart::$view]['title'])) {
            Webmart::addValue('title', self::$seo[Webmart::$view]['title']);
        } else {
            Webmart::addValue('title', self::$seo['home']['title']);
        }

        Webmart::addValue('description', '');

        require WM_DIR_CLASSES . 'FrontEnd.php';

        FrontEnd::newMenu(
            'hello',
            array(
                'Get Started' => array(
                    'url' => 'http://webmartphp.com/',
                    'class' => 'webmart'
                )
            ),
            'Welcome to Webmart',
            'http://webmartphp.com/'
        );
    }

    /**
    * @method
    */

    public function routeHome()
    {
        Webmart::addValue('description', self::$seo['home']['description']);
    }

}
