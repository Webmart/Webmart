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

        \Webmart::addMarkup($config['name'], $html, true);
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
