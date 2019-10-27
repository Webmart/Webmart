<?php

/*!
* Webmart
* A basic PHP framework for web applications and websites.
* https://webmartphp.com/
*/

namespace Webmart;
class Tools
{

    /**
    * @method
    */

    public static function newDatabase($data = array())
    {
        if (empty($data) && !isset(\Config::$db) || empty(\Config::$db)) {
            Webmart::error('Database configuration', 'is invalid');
        }

        if (!class_exists('Medoo')) {
            if (!file_exists(WM_DIR_LIBS . 'Medoo.php')) {
                return null;
            }

            require_once WM_DIR_LIBS . 'Medoo.php';
        }

        return new \Medoo\Medoo(!empty($data) ? $data : \Config::$db);
    }

    /**
    * @method
    */

    public static function newDetect()
    {
        if (!class_exists('Mobile_Detect')) {
            if (!file_exists(WM_DIR_LIBS . 'Mobile_Detect.php')) {
                return null;
            }

            require_once WM_DIR_LIBS . 'Mobile_Detect.php';
        }

        return new \Mobile_Detect();
    }

    /**
    * @method
    */

    public static function newEditor()
    {
        if (!class_exists('Quill')) {
            require_once WM_DIR_LIBS . 'Quill.php';
        }

        return new Quill();
    }

    /**
    * @method
    */

    public static function newGoogleLibrary($name = '', $version = '')
    {
        $available = array(
            'jQuery' => '',
            'maps' => ''
        );
    }

    /**
    * @method
    */

    public static function getJSON($filename, $array = true)
    {
        if (!$filename) {
            return null;
        }

        if (file_exists(WM_DIR_JSON . $filename . '.json')) {
            return json_decode(file_get_contents(WM_DIR_JSON . $filename . '.json'), $array);
        }

        return null;
    }

}
