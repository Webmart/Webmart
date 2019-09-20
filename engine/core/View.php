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
        if (!in_array($type, array('css', 'js'))) {
            Webmart::error('Asset type for', 'is invalid', $name);
        }

        $html = '';

        if (strpos($name, 'http') === 0) {
            if ($type == 'css') {
                $html .= '<link rel="stylesheet" href="' . $name . '" />';
            } elseif ($type == 'js') {
                $html .= '<script src="' . $name . '"></script>';
            }

            self::addMarkup('assets', $html);
        } elseif (file_exists(DIR_ASSETS . $type . '/' . $name . '.' . $type)) {
            $url = self::$vars->urls[$type] . $name . '.' . $type;

            if ($type == 'css') {
                $html .= '<link rel="stylesheet" href="' . $url . '" />';
            } else {
                $html .= '<script src="' . $url . '"></script>';
            }

            self::addMarkup('assets', $html);
        }
    }

    /**
    * @method
    */

    public static function addGoogleFont($name, $set = array())
    {
        $html = '';

        if (is_array($name)) { // handle multiple fonts
            foreach ($name as $fontname => $data) {
                self::addGoogleFont($fontname, $data);
            }
        } else { // handle one font
            if (!empty($set)) {
                $font = ucfirst(strtolower($name));

                // add font weights
                if (isset($set['weights']) && !empty($set['weights'])) {
                    $font .= ':';

                    foreach ($set['weights'] as $weight) {
                        $font .= $weight . ',';
                    }
                }

                $font .= '&display=swap';

                // add font subsets
                if (isset($set['subsets']) && !empty($set['subsets'])) {
                    $font .= '&subset=';

                    foreach ($set['subsets'] as $subset) {
                        $font .= $subset . ',';
                    }
                }

                self::addAsset('css', 'https://fonts.googleapis.com/css?family=' . $font);
            }
        }
    }

}
