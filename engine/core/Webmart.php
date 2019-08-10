<?php

class Webmart
{

    public static $page = 'home'; /** Request page. */

    public static $view = 'home'; /** Request view. */

    public static $url = ''; /** Request URL. */

    public static $cookies = array(); /** Request COOKIES. */

    public static $query = array(); /** Request GET data. */

    public static $data = array(); /** Request POST data. */

    private static $initialised = false;

    private static $flight = null;

    /**
    * @method
    */

    public static function __callStatic($name = null, $params)
    {
        if (!$name) return null;

        if (method_exists('Webmart\Tools', $name)) {
            if (isset($params[4])) {
                return Webmart\Tools::$name($params[0], $params[1], $params[2], $params[3], $params[4]);
            } elseif (isset($params[3])) {
                return Webmart\Tools::$name($params[0], $params[1], $params[2], $params[3]);
            } elseif (isset($params[2])) {
                return Webmart\Tools::$name($params[0], $params[1], $params[2]);
            } elseif (isset($params[1])) {
                return Webmart\Tools::$name($params[0], $params[1]);
            } elseif (isset($params[0])) {
                return Webmart\Tools::$name($params[0]);
            } else {
                return Webmart\Tools::$name();
            }
        } elseif (method_exists('Webmart\View', $name)) {
            if (isset($params[2])) {
                return Webmart\View::$name($params[0], $params[1], $params[2]);
            } elseif (isset($params[1])) {
                return Webmart\View::$name($params[0], $params[1]);
            } elseif (isset($params[0])) {
                return Webmart\View::$name($params[0]);
            } else {
                return Webmart\View::$name();
            }
        }

        return null;
    }

    /**
    * @method
    */

    public static function redirect($where, $http = null)
    {
        if (!$where) return;
        self::$flight->redirect($where, !$http ? '303' : $http);
        exit();
    }

    /**
    * @method
    */

    public static function init()
    {
        if (self::$initialised) return;

        define('DIR_', getcwd() . '/');

        define('DIR_CONFIG', DIR_ . 'config/');
        define('DIR_ENGINE', DIR_ . 'engine/');
        define('DIR_CORE', DIR_ . 'engine/core/');
        define('DIR_LIBS', DIR_ . 'engine/libs/');

        // set the theme folder
        define('DIR_VIEW', DIR_ . 'view/' . WM_THEME . '/');

        try {
            if (!is_dir(DIR_VIEW)) {
                throw new Exception('Theme directory is invalid.');
            } elseif (!file_exists(DIR_VIEW . 'Config.php')) {
                throw new Exception('Theme configuration file is missing.');
            } elseif (!file_exists(DIR_VIEW . 'Theme.php')) {
                throw new Exception('Theme routing file is missing.');
            }
        } catch (Exception $error) {
            echo 'Webmart - ' . $error->getMessage();
            exit();
        }

        require_once DIR_VIEW . 'Config.php';
        require_once DIR_VIEW . 'Theme.php';

        define('DIR_ASSETS', DIR_VIEW . 'assets/');
        define('DIR_CLASSES', DIR_VIEW . 'classes/');
        define('DIR_CONTROLLERS', DIR_VIEW . 'controllers/');
        define('DIR_TEMPLATES', DIR_VIEW . 'templates/');
        define('DIR_JSON', DIR_VIEW . 'json/');

        // open debugging
        if (WM_DEBUG == true) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);

            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
        }

        require_once DIR_CORE . 'Tools.php';
        require_once DIR_CORE . 'View.php';
        require_once DIR_ENGINE . 'flight/autoload.php';

        self::initRoute();
    }

    /**
    * @method
    */

    private static function initRoute()
    {
        self::$flight = new flight\Engine();
        self::$flight->set('flight.views.path', DIR_TEMPLATES);

        self::$flight->map('notFound', function() {
            try {
                if (!file_exists(DIR_TEMPLATES . '404.php')) {
                    throw new Exception('404 template is missing.');
                }
            } catch (Exception $error) {
                echo 'Webmart - ' . $error->getMessage();
                exit();
            }

            require_once DIR_TEMPLATES . '404.php';
        });

        // start the routing process
        self::$flight->route('*', function() {
            $request = self::$flight->request();

            // clear the URL from queries
            if (strpos($request->url, '?') != 0) {
                $request->url = substr($request->url, 0, strpos($request->url, '?'));
            }

            // parse the URL
            if ($request->url != '/') {
                $request->url = rtrim($request->url, '/');

                if (isset(\Config::$redirects) && !empty(\Config::$redirects)) {
                    foreach (\Config::$redirects as $http => $paths) {
                        if (isset($paths[$request->url])) {
                            self::$flight->redirect($paths[$request->url], $http);
                            exit();
                        }
                    }
                }

                // remove the first slash
                self::$url = substr($request->url, 1);

                // set the page & view
                $routes = explode('/', self::$url);

                self::$view = $routes[0];
                self::$page = $routes[count($routes) - 1];
            }

            // sanitize queries & cookies
            foreach (array('query', 'cookies') as $type) {
                $clean = '';

                if (isset(\Config::${$type}) && !empty(\Config::${$type}) && !empty($request->{$type})) {
                    foreach ($request->{$type} as $name => $value) {
                        // validate each item
                        if (!in_array($name, \Config::${$type})) continue;

                        $clean = filter_var(trim($value), FILTER_SANITIZE_STRIPPED);

                        if ($clean) {
                            self::${$type}[$name] = $clean;
                        } else {
                            self::${$type}[$name] = null;
                        }
                    }
                }
            }

            foreach ((array) $request->data as $data) {
                self::$data = $data;
            }

            // load custom executable file
            if (WM_DEBUG && file_exists(DIR_VIEW . 'functions.php')) {
                include_once DIR_VIEW . 'functions.php';
            }

            self::initView();
            return true; // continue to next routing rule (Flight)
        });

        Config::$routes[] = '/'; // add the homepage

        // parse the next routing process
        foreach (Config::$routes as $route) {
            $exploded = explode('-', $route);
            $path = '/';

            // create the routing path
            foreach ($exploded as $item) {
                if (strpos($item, '(') === 0) {
                    $path .= str_replace('(', '(/@', $item);
                    continue;
                }

                $path .= $item;
            }

            self::$flight->route($path, function() {
                $controller = null;
                $parsed = explode('/', self::$url);
                $args = func_get_args();

                if (empty($args)) $args = null;

                foreach ($parsed as $item) {
                    if (file_exists(DIR_CONTROLLERS . ucfirst($item) . '.php')) {
                        require DIR_CONTROLLERS . ucfirst($item) . '.php';
                        $controller = ucfirst($item);
                    }
                }

                if (!$controller) {
                    $theme = new Theme($args);
                    $method = 'route' . ucfirst(Webmart::$view);

                    if (method_exists('Theme', $method)) {
                        $theme->$method();
                    }
                } else {
                    new $controller($args);
                }
            });
        }

        self::$flight->start();
        self::initRender();
    }

    /**
    * @method
    */

    private static function initView()
    {
        self::addValue('version', Config::$version);

        self::addValue('urls', array(
            'base' => WM_BASE,
            'page' => WM_BASE . self::$url,
            'css' => WM_BASE . 'view/' . WM_THEME . '/assets/css/',
            'imgs' => WM_BASE . 'view/' . WM_THEME . '/assets/imgs/',
            'js' => WM_BASE . 'view/' . WM_THEME . '/assets/js/'
        ));

        $query = array();

        foreach (\Config::$query as $name) {
            if (isset(self::$query[$name])) {
                $query[$name] = self::$query[$name];
            }
        }

        self::addValue('query', $query);

        self::addAsset('css', 'global');
        self::addAsset('js', 'global');
    }

    /**
    * @method
    */

    private static function initRender()
    {
        self::addValue('page', self::$page);
        self::addValue('view', self::$view);

        $head = '';
        $head .= '<base href="' . WM_BASE . '" />';
        $head .= '<link rel="canonical" href="' . WM_BASE . self::$url . '" />';
        $head .= '<meta name="robots" content="';

        if (WM_ROBOTS == true) {
            $head .= 'index, follow';
        } else {
            $head .= 'noindex, nofollow';
        }

        $head .= '" />';

        self::addMarkup('head', $head);
        self::addMarkup('body', ' view-' . self::$view . ' page-' . self::$page);

        foreach (array('page', 'view') as $file) {
            foreach (array('css', 'js') as $type) {
                self::addAsset($type, self::${$file});
            }
        }

        self::$flight->view()->set('vars', Webmart\View::$vars);
        self::$flight->view()->set('html', Webmart\View::$html);

        foreach (array('header', 'footer') as $global) {
            if (file_exists(DIR_TEMPLATES . $global . '.php')) {
                self::$flight->render($global, $global, $global);
            }
        }

        if (file_exists(DIR_TEMPLATES . self::$view . '.php')) {
            self::$flight->render(self::$view);
        }

        self::$initialised = true;
    }

    private function __construct() {}

    private function __destruct() {}

    private function __clone() {}

}
