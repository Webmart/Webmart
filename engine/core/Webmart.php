<?php

/*!
* Webmart
* A basic PHP framework for web applications and websites.
* https://webmartphp.com/
*/

class Webmart
{

    public static $page = 'home'; /** Request page */

    public static $template = 'home'; /** Request template */

    public static $url = ''; /** Request URL */

    public static $cookies = array(); /** Request COOKIES */

    public static $query = array(); /** Request GET data */

    public static $data = array(); /** Request POST data */

    private static $view = null; /** Response variables */

    private static $json = null; /** Response JSON */

    private static $ready = false; /** Framework status */

    /**
    * @method prepares the framework
    */

    public static function init($composer = false)
    {
        if (self::$ready) {
            return;
        }

        // define directories

        define('WM_ROOT', getcwd() . '/');

        if ($composer) {
            define('WM_DIR', realpath(__DIR__ . '/../..') . '/');
        } else {
            define('WM_DIR', WM_ROOT);
        }

        define('wM_DIR_THEMES', WM_DIR . 'themes/');
        define('WM_DIR_ENGINE', WM_DIR . 'engine/');
        define('WM_DIR_CORE', WM_DIR_ENGINE . 'core/');
        define('WM_DIR_LIBS', WM_DIR_ENGINE . 'libs/');

        // load core files

        require_once WM_DIR_CORE . 'Toolkit.php';

        // attempt to load Flight

        if (!$composer) {
            if (!file_exists(WM_DIR_ENGINE . 'flight/Flight.php')) {
                exit('Flight framework not detected.');
            }

            require_once WM_DIR_ENGINE . 'flight/Flight.php';
        } else {
            if (!class_exists('flight\Engine')) {
                exit('Flight framework not detected.');
            }
        }

        // load configuration and theme

        if (file_exists(WM_DIR . 'wm.php')) {
            require WM_DIR . 'wm.php';
            self::theme();
        } else {
            self::setup();
        }

        // enable debugging

        if (WM_DEBUG == true) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);

            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
        }

        // generate .htaccess file

        if (!file_exists(WM_ROOT . '.htaccess')) {
            $htaccess = 'RewriteEngine On' . PHP_EOL;
            $htaccess .= 'RewriteBase /' . WM_FOLDER;

            if (WM_FOLDER != '') {
                $htaccess .= '/';
            }

            $htaccess .= PHP_EOL;

            $htaccess .= 'RewriteCond %{REQUEST_FILENAME} !-f' . PHP_EOL;
            $htaccess .= 'RewriteCond %{REQUEST_FILENAME} !-d' . PHP_EOL;
            $htaccess .= 'RewriteRule ^(.*)$ index.php [QSA,L]' . PHP_EOL;

            file_put_contents(WM_ROOT . '.htaccess', $htaccess);
            exit('Generated .htaccess file - please refresh the page.');
        }

        // complete

        self::$ready = true;
        return;
    }

    /**
    * @method
    */

    private static function setup()
    {
        $output = '<?php' . PHP_EOL;
        $wm = array(
            'folder' => '',
            'theme' => '',
            'debug' => false,
            'base' => '',
            'sitemap' => false,
            'robots' => false,
            'https' => false
        );
        $fields = array(
            'folder' => array(
                'title' => 'Working on a subdirectory?',
                'type' => 'text',
                'placeholder' => 'ex. localhost/subfolder/'
            ),
            'theme' => array(
                'title' => 'Select a theme',
                'type' => 'select',
                'options' => array(
                    'one',
                    'basic-website'
                )
            )
        );

        self::addBootstrap(true);

        self::newForm(array(
            'name' => 'wmsetup',
            'method' => 'POST',
            'action' => '',
            'submit' => 'submit'
        ), $fields, function($success, $response) {
            var_dump(1);
        });

        var_dump(0);
        die;

        // file_put_contents(WM_DIR . 'wm.php', $output);
    }

    /**
    * @method prepares and loads the theme
    */

    private static function theme()
    {
        // perform checks

        if (!WM_THEME) {
            exit('Theme not defined. Please check your wm.php configuration.');
        } elseif (WM_THEME == '') {
            exit('Theme not defined.');
        } elseif (!is_dir(WM_DIR_THEMES . WM_THEME)) {
            exit('Theme folder not detected.');
        }

        // define theme directory

        define('WM_DIR_THEME', WM_DIR_THEMES . WM_THEME . '/');

        // attempt to load core theme files

        foreach (array('Config', 'Theme') as $file) {
            if (!file_exists(WM_DIR_THEME . $file . '.php')) {
                exit($file . ' not detected in selected theme.');
            }

            require_once WM_DIR_THEME . $file . '.php';
        }

        // define secondary directories

        define('WM_DIR_ASSETS', WM_DIR_THEME . 'assets/');
        define('WM_DIR_CLASSES', WM_DIR_THEME . 'classes/');
        define('WM_DIR_CONTROLLERS', WM_DIR_THEME . 'controllers/');
        define('WM_DIR_TEMPLATES', WM_DIR_THEME . 'templates/');
        define('WM_DIR_JSON', WM_DIR_THEME . 'json/');

        // generate robots.txt file

        if (WM_ROBOTS && WM_ROBOTS === true && !file_exists(WM_ROOT . 'robots.txt')) {
            $robots = 'User-agent: *' . PHP_EOL;

            if (isset(\Config::$noindex) && !empty(\Config::$noindex)) {
                foreach (\Config::$noindex as $page) {
                    foreach (array('Disallow', 'Noindex') as $cmd) {
                        $robots .= $cmd . ': /' . $page . '/' . PHP_EOL;
                    }
                }
            } else {
                $robots .= 'Disallow: ' . PHP_EOL;
                $robots .= 'Noindex: ' . PHP_EOL;
            }

            file_put_contents(WM_ROOT . 'robots.txt', $robots);
        }

        // continue

        self::request();
    }

    /**
    * @method handles the request
    */

    private static function request()
    {
        // configure Flight

        Flight::set('flight.views.path', WM_DIR_TEMPLATES);

        // handle global requests

        Flight::route('*', function() {
            $request = self::$flight->request();

            // clean the URL from queries
            if (strpos($request->url, '?') != 0) {
                $request->url = substr($request->url, 0, strpos($request->url, '?'));
            }

            //// page & view

            if ($request->url != '/') {
                $request->url = rtrim($request->url, '/');

                // redirects
                if (isset(\Config::$redirects) && !empty(\Config::$redirects)) {
                    foreach (\Config::$redirects as $http => $paths) {
                        if (isset($paths[$request->url])) {
                            self::$flight->redirect($paths[$request->url], $http);
                            exit();
                        }
                    }
                }

                self::$url = substr($request->url, 1); // remove the first slash from the URL

                $routes = explode('/', self::$url);

                self::$view = $routes[0]; // assign first as view
                self::$page = $routes[count($routes) - 1]; // assign last as page
            }

            //// data

            // GET and cookies
            foreach (array('query', 'cookies') as $type) {
                $clean = '';

                if (
                    isset(\Config::${$type}) &&
                    !empty(\Config::${$type}) &&
                    !empty($request->{$type})
                ) {
                    foreach ($request->{$type} as $name => $value) {
                        if (!in_array($name, \Config::${$type})) {
                            continue;
                        }

                        $clean = filter_var(trim($value), FILTER_SANITIZE_STRIPPED);

                        if ($clean) {
                            self::${$type}[$name] = $clean;
                        } else {
                            self::${$type}[$name] = null;
                        }
                    }
                }
            }

            // POST
            foreach ((array) $request->data as $data) {
                self::$data = $data;
            }

            // functions.php
            if (WM_DEBUG && file_exists(WM_DIR_VIEW . 'functions.php')) {
                include_once WM_DIR_VIEW . 'functions.php';
            }

            //// view

            self::addValue('version', \Config::$version);
            self::addValue('urls', array(
                'base' => WM_BASE,
                'page' => WM_BASE . self::$url,
                'css' => WM_BASE . 'view/' . WM_THEME . '/assets/css/',
                'imgs' => WM_BASE . 'view/' . WM_THEME . '/assets/imgs/',
                'js' => WM_BASE . 'view/' . WM_THEME . '/assets/js/'
            ));

            self::addAsset('css', 'global');
            self::addAsset('js', 'global');

            $query = array();

            foreach (\Config::$query as $name) {
                if (isset(self::$query[$name])) {
                    $query[$name] = self::$query[$name];
                }
            }

            self::addValue('query', $query);

            return true; // flight
        });

        //// individual

        \Config::$routes[] = '/'; // auto-include the homepage

        foreach (\Config::$routes as $route) {
            $exploded = explode('-', $route);
            $path = '/';

            // prepare the routing path
            foreach ($exploded as $item) {
                if (strpos($item, '(') === 0) {
                    $path .= str_replace('(', '(/@', $item);
                    continue;
                }

                $path .= $item;
            }

            // assign routing path
            self::$flight->route($path, function() {
                $controller = null;
                $parsed = explode('/', self::$url);
                $args = func_get_args();

                if (empty($args)) {
                    $args = null;
                }

                // controllers
                foreach ($parsed as $item) {
                    if (file_exists(WM_DIR_CONTROLLERS . ucfirst($item) . '.php')) {
                        require WM_DIR_CONTROLLERS . ucfirst($item) . '.php';
                        $controller = ucfirst($item);
                    }
                }

                // classes
                if (!$controller) {
                    $class = '\Theme';
                    $method = 'route' . ucfirst(self::$view);
                } else {
                    $class = '\\' . $controller;
                    $method = 'route' . ucfirst(self::$page);
                }

                $instance = new $class($args);

                // methods
                if (method_exists($class, $method)) {
                    $instance->$method($args);
                }
            });
        }

        // continue

        Flight::start();
        self::initView();
    }

    /**
    * @method prepares and loads the view
    */

    private static function initView()
    {
        self::addValue('page', self::$page);
        self::addValue('view', self::$view);

        //// markup

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

        self::addValue('head', $head);

        // <body>
        self::addValue('body', ' view-' . self::$view . ' page-' . self::$page);

        // assets
        foreach (array('page', 'view') as $file) {
            foreach (array('css', 'js') as $type) {
                self::addAsset($type, self::${$file});
            }
        }

        // google libraries
        if (isset(\Config::$googlelibs) && !empty(\Config::$googlelibs)) {
            foreach (\Config::$googlelibs as $name => $data) {
                Webmart\View::addGoogleLibrary($name, $data);
            }
        }

        // google fonts
        if (isset(\Config::$googlefonts) && !empty(\Config::$googlefonts)) {
            Webmart\View::addGoogleFont(\Config::$googlefonts);
        }

        // bootstrap
        if (isset(\Config::$bootstrap) && \Config::$bootstrap != false) {
            Webmart\View::addBootstrap(\Config::$bootstrap === 'bundle' ? true : false);
        }

        // Flight setup of the view

        self::$flight->view()->set('vars', Webmart\View::$vars);
        self::$flight->view()->set('html', Webmart\View::$html);

        // autoload the header template
        if (file_exists(WM_DIR_TEMPLATES . 'header.php')) {
            self::$flight->render('header');
        }

        // autoload the page template
        if (file_exists(WM_DIR_TEMPLATES . self::$view . '.php')) {
            self::$flight->render(self::$view);
        }

        // autoload the footer template
        if (file_exists(WM_DIR_TEMPLATES . 'footer.php')) {
            self::$flight->render('footer');
        }

        self::$initialised = true;
    }

    /**
    * @method handles calls to framework methods
    */

    public static function __callStatic($name = null, $params)
    {
        if (!$name) {
            return null;
        }

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

    private function __construct() {}

    private function __destruct() {}

    private function __clone() {}

}
