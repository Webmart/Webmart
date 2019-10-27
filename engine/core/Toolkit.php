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
    * @var string view assets
    */

    public static $assets = '';

    /**
    * @method redirects to path with given protocol
    * @param $where = string
    * @param $http = string
    */

    public static function redirect($where = '', $http = '303')
    {
        if (!$where) {
            return;
        }

        \Flight::redirect($where, $http);

        exit();
    }

    /**
    * @method stores a variable
    * @param $name = string
    * @param $value
    */

    public static function set($name = null, $value = null)
    {
        if (!$name) {
            return;
        }

        \Flight::set($name, $value);

        return $value;
    }

    /**
    * @method retrieves a variable
    * @param $name = string
    */

    public static function get($name = null)
    {
        if (!$name || $name == '') {
            $collection = array();

            foreach (\Flight::get() as $item => $value) {
                switch ($item) {
                    case 'flight.base_url':
                    case 'flight.case_sensitive':
                    case 'flight.handle_errors':
                    case 'flight.log_errors':
                    case 'flight.views.path':
                    case 'flight.views.extension':
                        continue;
                        break;
                    default:
                        $collection[$item] = $value;
                }
            }

            return $collection;
        }

        return \Flight::get($name);
    }

    /**
    * @method passes a variable into the view
    * @param $name = string
    * @param $value
    */

    public static function pass($name = null, $value = null)
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

    public static function asset($type = '', $name = null)
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

            self::$assets .= $html;
            return $html;
        } elseif (file_exists(WM_DIR_ASSETS . $type . '/' . $name . '.' . $type)) {
            $url = self::get('urls')[$type] . $name . '.' . $type;

            if ($type == 'css') {
                $html .= '<link rel="stylesheet" href="' . $url . '" />';
            } else {
                $html .= '<script src="' . $url . '"></script>';
            }

            self::$assets .= $html;
            return $html;
        }
    }

    /**
    * @method passes inline CSS into the view
    * @param $rules = array
    */

    public static function style($rules = array())
    {
        if (!$rules || !is_array($rules) || empty($rules)) {
            return;
        }

        $html = '<style type="text/css">';

        foreach ($rules as $rule => $styles) {
            $html .= $rule . '{';

            foreach ($styles as $style => $value) {
                $html .= $style . ':' . $value . ';';
            }

            $html .= '}';
        }

        $html .= '</style>';

        self::$assets .= $html;
        return $html;
    }

    /**
    * @method generates a PHP based form with a request/response callback
    * @param $config = array
    * @param $fields = array
    * @param $callback = function
    */

    public static function form($config = array(), $fields = array(), $callback = null)
    {
        $html = '';
        $request = array();
        $response = array();
        $success = true;

        if ($config['method'] == 'POST') {
            $request = self::get('data');
        } elseif ($config['method'] == 'GET') {
            $request = self::get('query');
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
                    if ($settings['type'] == 'email') {
                        $input = filter_var(trim($request[$field]), FILTER_VALIDATE_EMAIL);
                    } elseif ($settings['type'] == 'radio' || $settings['type'] == 'checkbox') {
                        $input = $request[$field];
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

        // handle response

        $response['success'] = $success;

        if (!empty($request) && $callback) {
            $override = $callback($response);

            // override response fields with closure

            if (!empty($override)) {
                foreach ($override as $field => $data) {
                    $response[$field] = $data;
                }
            }
        }

        // prepare form fields

        foreach ($fields as $field => $set) {
            $html .= '<div class="form-group field-' . $i . ' field-' . $field . '">';

            if (isset($set['heading'])) {
                $html .= '<h4>' . $set['heading'] . '</h4>';
            }

            $html .= '<p>';
            $id = $set['type'] . ucfirst($field) . $i;

            // prepare label

            if (isset($set['label']) && $set['label'] != '') {
                $html .= '<label class="form-check-label" for="' . $id . '">' . $set['label'] . '</label>';
            }

            // prepare individual options

            switch ($set['type']) {
                case 'select':
                    $html .= '<select name="' . $field . '" id="' . $id . '" class="form-control ' . $id;

                    $html .= isset($set['class']) ? ' ' . $set['class'] . '">' : '">';

                    if (isset($set['placeholder']) && $set['placeholder'] != '') {
                        $html .= '<option>' . $set['placeholder'] . '</option>';
                    }

                    if (isset($set['options']) && !empty($set['options'])) {
                        foreach ($set['options'] as $name) {
                            $html .= '<option name="' . $name . '" value="' . $name . '" ';

                            if (isset($request[$field]) && $request[$field] == $name) {
                                $html .= ' selected="selected"';
                            }

                            $html .= '>' . $name . '</option>';
                        }
                    }

                    $html .= '</select></p>';

                    break;
                case 'email':
                case 'password':
                case 'text':
                case 'textarea':
                    $html .= '<input type="' . $set['type'] . '" name="' . $field . '" id="' . $id . '" class="form-control ' . $id;

                    $html .= isset($set['class']) ? ' ' . $set['class'] . '" ' : '" ';

                    if (isset($set['placeholder']) && $set['placeholder'] != '') {
                        $html .= ' placeholder="' . $set['placeholder'] . '"';
                    }

                    if (isset($request[$field]) && $request[$field] != '') {
                        $html .= ' value="' . $request[$field] . '" ';
                    } elseif (isset($set['value']) && $set['value'] != '') {
                        $html .= ' value="' . $set['value'] . '"';
                    }

                    $html .= '/></p>';

                    break;
                case 'radio':
                case 'checkbox':
                    $j = 1;
                    $html .= '</p>';

                    foreach ($set['options'] as $option) {
                        $jid = $id . '_' . $j;

                        $html .= '<div class="form-check';
                        $html .= isset($set['class']) ? ' ' . $set['class'] . '">' : '">';

                        $html .= '<input type="' . $set['type'] . '" name="' . $field . '" id="' . $jid . '" class="form-check-input" value="' . $option . '"';

                        if ($set['type'] == 'radio' && $j == 1) {
                            $html .= ' checked';
                        }

                        $html .= ' /><label class="form-check-label" for="' . $jid . '">' . $option . '</label>';

                        $html .= '</div>';

                        $j++;
                    }

                    break;
            }

            // prepare error message

            if (isset($response[$field]['error'])) {
                $html .= '<p class="error"><span class="error bubble bubble-red">';
                $html .= $response[$field]['error'] . '</span></p>';
            }

            $html .= '</div>';

            $i++;
        }

        // close form

        $html .= '<div class="submit">';
        $html .= '<input class="button btn btn-primary" type="submit" value="' . $config['submit'] . '" ';
        $html .= 'name="' . self::get('page') . '" /></div></form>';

        return self::pass($config['name'], $html);
    }

    /**
    * @method passes a new Google font to the view
    * @param $name = string
    * @param $set = array
    */

    public static function font($name = null, $set = array())
    {
        if (!$name || $name == '' || empty($set)) {
            return;
        }

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

        return self::asset('css', 'https://fonts.googleapis.com/css?family=' . $font);
    }

    /**
    * @method passes a Google library into the view
    */

    public static function library($name, $info = '')
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

                return self::asset('js', 'https://maps.googleapis.com/maps/api/js?key=' . $script);
            }
        } else { // handle all other similarly loaded scripts
            $script .= $name . '/' . $info . '/' . $name . '.min.js';

            return self::asset('js', 'https://ajax.googleapis.com/ajax/libs/' . $script);
        }
    }

    /**
    * @method loads Bootstrap
    */

    public static function bootstrap($bundle = false)
    {
        $source = 'https://stackpath.bootstrapcdn.com/bootstrap/';
        $version = '4.3.1';
        $html = '';

        $html .= self::asset('css', $source . $version . '/css/bootstrap.min.css');

        // handle the JavaScript package

        if ($bundle) {
            $script = '/js/bootstrap.bundle.min.js';
        } else {
            $script = '/js/bootstrap.min.js';
        }

        $html .= self::asset('js', $source . $version . $script);

        return $html;
    }

}
