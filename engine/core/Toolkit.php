<?php

/*!
* Webmart
* A basic PHP framework for web applications and websites.
* https://webmartphp.com/
*/

namespace Webmart;
class Toolkit
{

    /**
    * @method redirects to path
    */

    public static function redirect($where = '', $http = null)
    {
        if (!$where || $where == '') {
            return false;
        }

        Flight::redirect($where, $http);
        exit();
    }

    /**
    * @method renders a template
    */

    public static function render($template = '')
    {
        if (!$template || $template = '') {
            return false;
        }

        if (file_exists(DIR_TEMPLATES . $template . '.php')) {
            self::$flight->render($template, $data, $varname);
        }
    }







    /**
    * @method passes a variable into the view
    * @param $name = string
    * @param $value
    */

    public static function newVar($name = null, $value = null)
    {
        if (!$name || $name == '' || !$value) {
            return;
        }

        \Flight::view()->set($name, $value);

        return $value;
    }

    /**
    * @method passes a CSS/JS asset into the view
    * @param $type = string
    * @param $name = string
    */

    public static function newAsset($type = '', $name = null)
    {
        // perform checks

        if (!$type || !$name) {
            return;
        } elseif (!in_array($type, array('css', 'js'))) {
            return;
        }

        $html = '';

        // prepare external/internal links

        if (strpos($name, 'http') === 0) {
            if ($type == 'css') {
                $html .= '<link rel="stylesheet" href="' . $name . '" />';
            } elseif ($type == 'js') {
                $html .= '<script src="' . $name . '"></script>';
            }

            return self::newVar('assets', $html);
        } elseif (file_exists(WM_DIR_ASSETS . $type . '/' . $name . '.' . $type)) {
            $url = self::$vars->urls[$type] . $name . '.' . $type;

            if ($type == 'css') {
                $html .= '<link rel="stylesheet" href="' . $url . '" />';
            } else {
                $html .= '<script src="' . $url . '"></script>';
            }

            return self::newVar('assets', $html);
        }
    }

    /**
    * @method passes inline CSS into the view
    * @param $rules = array
    */

    public static function newCSS($rules = array())
    {
        if (!$rules || !is_array($rules) || empty($rules)) {
            return;
        }

        $html = '<style type="text/css">';
    }

    /**
    * @method generates a PHP based form with a request/response callback
    * @param $config = array
    * @param $fields = array
    * @param $callback = function
    */

    public static function newForm($config = array(), $fields = array(), $callback = null)
    {
        $html = '';
        $request = array();
        $response = array();
        $success = true;

        if ($config['method'] == 'POST') {
            $request = \Webmart::$data;
        } elseif ($config['method'] == 'GET') {
            $request = \Webmart::$query;
        }

        $html .= '<form method="' . strtolower($config['method']) . '" ';
        $html .= 'class="' . $config['name'] . '" ' . ' id="' . $config['name'] . '" ';

        if (isset($config['action'])) {
            $html .= 'action="' . $config['action'] . '">';
        } else {
            $html .= '>';
        }

        $i = 1;

        foreach ($fields as $field => $settings) {
            $error = null;

            if (!isset($settings['type'])) {
                $settings['type'] = 'text';
            }

            if (!empty($request)) {
                $response[$field] = array(
                    'error' => null,
                    'value' => null
                );

                if (!isset($request[$field]) || $request[$field] == '') {
                    $error = ucfirst($field) . ' is empty.';
                } else {
                    if ($settings['type'] == 'e-mail') {
                        $input = filter_var(trim($request[$field]), FILTER_VALIDATE_EMAIL);
                    } else {
                        $input = filter_var(trim($request[$field]), FILTER_SANITIZE_STRIPPED);

                        if ($settings['type'] == 'password' && strlen($input) < 8) {
                            $error = ucfirst($field) . ' is too small (min 8 characters).';
                        }

                        if ($settings['type'] == 'select' && isset($settings['placeholder'])) {
                            if ($request[$field] == $settings['placeholder']) {
                                $error = 'You must choose an option.';
                            }
                        }
                    }

                    if ($input == false) {
                        $error = ucfirst($field) . ' is invalid.';
                    } else {
                        if ($settings['type'] == 'text') {
                            // replace multiple spaces
                            $input = preg_replace('!\s+!', ' ', $input);
                        }

                        $response[$field]['value'] = $input;
                    }
                }

                if ($error) {
                    $response[$field]['error'] = $error;
                    $success = false;
                }
            }
        }

        $response['success'] = $success;

        if (!empty($request) && $callback) {
            $override = $callback($response);

            if (!empty($override)) {
                foreach ($override as $field => $data) {
                    $response[$field] = $data;
                }
            }
        }

        foreach ($fields as $field => $settings) {
            $html .= '<div class="field field-' . $i . ' field-' . $settings['type'] . '">';

            if (isset($settings['title'])) {
                $html .= '<h4>' . $settings['title'] . '</h4>';
            }

            $html .= '<p>';

            if (isset($response[$field]['error'])) {
                $html .= '<span class="error bubble bubble-red">';
                $html .= $response[$field]['error'] . '</span>';
            }

            if ($settings['type'] == 'select') {
                $html .= '<select name="' . $field . '">';

                if (isset($settings['placeholder'])) {
                    $html .= '<option>' . $settings['placeholder'] . '</option>';
                }

                if (isset($settings['options']) && !empty($settings['options'])) {
                    foreach ($settings['options'] as $name) {
                        $html .= '<option name="' . $name . '" value="' . $name . '" ';

                        if (isset($request[$field]) && $request[$field] == $name) {
                            $html .= ' selected="selected"';
                        }

                        $html .= '>' . $name . '</option>';
                    }
                }

                $html .= '</select>';
            } else {
                $html .= '<input type="' . $settings['type'] . '" name="' . $field . '" ';

                if (isset($settings['placeholder'])) {
                    $html .= ' placeholder="' . $settings['placeholder'] . '" ';
                }

                if (isset($request[$field]) && $request[$field] != '') {
                    $html .= 'value="' . $request[$field] . '" ';
                }

                $html .= '/>';
            }

            $html .= '</p></div>';

            $i++;
        }

        $html .= '<div class="submit">';
        $html .= '<input class="button" type="submit" value="' . $config['submit'] . '" ';
        $html .= 'name="' . \Webmart::$page . '" /></div></form>';

        return self::newVar($config['name'], $html, true);
    }

    /**
    * @method passes a new Google font to the view
    * @param $name = string
    * @param $set = array
    */

    public static function newFont($name, $set = array())
    {
        if (!isset($name) || $name == '' || empty($set)) {
            return;
        }

        if (is_array($name)) { // handle multiple fonts
            foreach ($name as $fontname => $data) {
                self::newFont($fontname, $data);
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

                return self::newAsset('css', 'https://fonts.googleapis.com/css?family=' . $font);
            }
        }
    }

    /**
    * @method passes a Google library into the view
    */

    public static function newLibrary($name, $info = '')
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
            if ($info != 'API_KEY' && $info != '') {
                $script .= $info . '&libraries=geometry';

                self::addAsset('js', 'https://maps.googleapis.com/maps/api/js?key=' . $script);
            }
        } else { // handle all other similarly loaded scripts
            $script .= $name . '/' . $info . '/' . $name . '.min.js';

            self::addAsset('js', 'https://ajax.googleapis.com/ajax/libs/' . $script);
        }
    }

    /**
    * @method loads Bootstrap
    */

    public static function loadBootstrap($bundle = false)
    {
        $source = 'https://stackpath.bootstrapcdn.com/bootstrap/';
        $version = '4.3.1';
        $html = '';

        $html .= self::newAsset('css', $source . $version . '/css/bootstrap.min.css');

        // handle the JavaScript package

        if ($bundle) {
            $script = '/js/bootstrap.bundle.min.js';
        } else {
            $script = '/js/bootstrap.min.js';
        }

        $html .= self::newAsset('js', $source . $version . $script);

        return $html;
    }

}
