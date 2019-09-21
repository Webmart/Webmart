<?php

/*!
* Webmart
* A basic PHP framework for web applications and websites.
* https://webmartphp.com/
*/

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
        } elseif (file_exists(WM_DIR_ASSETS . $type . '/' . $name . '.' . $type)) {
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
        if (is_array($name)) { // handle multiple fonts
            foreach ($name as $fontname => $data) {
                self::addGoogleFont($fontname, $data);
            }
        } else { // handle one font
            if (!empty($set)) {
                $font = str_replace(' ', '+', $name);

                // add font weights
                if (isset($set['weights']) && !empty($set['weights'])) {
                    $i = 1;
                    $font .= ':';

                    foreach ($set['weights'] as $weight) {
                        $font .= $weight;

                        if ($i < count($set['weights'])) {
                            $font .= ',';
                        }

                        $i++;
                    }
                }

                $font .= '&display=swap';

                // add font subsets
                if (isset($set['subsets']) && !empty($set['subsets'])) {
                    $i = 1;
                    $font .= '&subset=';

                    foreach ($set['subsets'] as $subset) {
                        $font .= $subset;

                        if ($i < count($set['subsets'])) {
                            $font .= ',';
                        }

                        $i++;
                    }
                }

                self::addAsset('css', 'https://fonts.googleapis.com/css?family=' . $font);
            }
        }
    }

    /**
    * @method
    */

    public static function addGoogleLibrary($name, $info = '')
    {
        $supported = array(
            'jquery', // https://developers.google.com/speed/libraries/
            'maps' // https://developers.google.com/maps/documentation/javascript/libraries
        );

        $name = strtolower(str_replace(' ', '', $name));
        $script = '';

        if (!in_array($name, $supported)) {
            return;
        }

        if ($name == 'maps') { // handle Google maps
            $script .= $info . '&libraries=geometry';

            self::addAsset('js', 'https://maps.googleapis.com/maps/api/js?key=' . $script);
        } else { // handle all other similarly loaded scripts
            $script .= $name . '/' . $info . '/' . $name . '.min.js';

            self::addAsset('js', 'https://ajax.googleapis.com/ajax/libs/' . $script);
        }
    }

}
