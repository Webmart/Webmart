<?php

namespace Webmart;
class View
{

    public static $vars = null;

    public static $html = null;

    /**
    * @method
    */

    public static function addMarkup($varname, $value = '', $override = false)
    {
        if (!isset($varname)) {
            return self::$vars;
        }

        if (!self::$html) {
            self::$html = new \stdClass();
        }

        if (!isset(self::$html->{$varname})) {
            self::$html->{$varname} = '';
        }

        if ($override == true) {
            self::$html->{$varname} = $value;
        } else {
            self::$html->{$varname} .= $value;
        }

        return self::$html;
    }

    /**
    * @method
    */

    public static function addValue($varname = '', $data)
    {
        if (!isset($varname)) {
            return self::$vars;
        }

        if (!self::$vars) {
            self::$vars = new \stdClass();
        }

        if (isset(self::$vars->{$varname}) && is_array(self::$vars->{$varname})) {
            self::$vars->{$varname}[] = $data;
        } else {
            self::$vars->{$varname} = $data;
        }

        return self::$vars;
    }

    /**
    * @method
    */

    public static function addAsset($type, $name)
    {
        try {
            if (!in_array($type, array('css', 'js'))) {
                throw new Exception('Asset type is invalid.');
            }
        } catch (Exception $error) {
            echo 'Webmart - ' . $error->getMessage();
            exit();
        }

        $html = '';

        if (file_exists(DIR_ASSETS . $type . '/' . $name . '.' . $type)) {
            $url = self::$vars->urls[$type] . $name . '.' . $type;

            if ($type == 'css') {
                $html .= '<link rel="stylesheet" href="' . $url . '" />';
            } else {
                $html .= '<script src="' . $url . '"></script>';
            }

            return self::addMarkup('assets', $html);
        }

        return null;
    }

}
