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

    private static $ready = false; /** Framework status */

    /**
    * @method prepares the framework
    */

    public static function init($composer = false)
    {
        if (self::$ready) {
            return;
        }

        // define directories and load core files

        define('WM_ROOT', getcwd() . '/');

        if ($composer) {
            define('WM_DIR', realpath(__DIR__ . '/../..') . '/');
        } else {
            define('WM_DIR', WM_ROOT);
        }

        define('WM_DIR_THEMES', WM_DIR . 'themes/');
        define('WM_DIR_ENGINE', WM_DIR . 'engine/');
        define('WM_DIR_CORE', WM_DIR_ENGINE . 'core/');
        define('WM_DIR_LIBS', WM_DIR_ENGINE . 'libs/');

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

        // load the configuration and theme

        if (!file_exists(WM_DIR . 'wm.php')) {
            Flight::route('/', function() {
                $request = Flight::request();

                foreach ((array) $request->data as $data) {
                    self::$data = $data;
                }

                self::setup();
            });

            Flight::start();

            return;
        } else {
            require_once WM_DIR . 'wm.php';
            self::theme();
        }

        // enable debugging

        if (defined('WM_DEBUG') && WM_DEBUG == true) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);

            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
        }

        // generate .htaccess file

        if (!file_exists(WM_ROOT . '.htaccess')) {
            $htaccess = 'RewriteEngine On' . PHP_EOL;
            $htaccess .= 'RewriteBase /';

            if (defined('WM_FOLDER') && WM_FOLDER != '') {
                $htaccess .= WM_FOLDER . '/';
            }

            $htaccess .= PHP_EOL;

            $htaccess .= 'RewriteCond %{REQUEST_FILENAME} !-f' . PHP_EOL;
            $htaccess .= 'RewriteCond %{REQUEST_FILENAME} !-d' . PHP_EOL;
            $htaccess .= 'RewriteRule ^(.*)$ index.php [QSA,L]' . PHP_EOL;

            file_put_contents(WM_ROOT . '.htaccess', $htaccess);

            Flight::redirect('/?wm=ready');
            exit();
        }

        // continue

        self::route();
    }

    /**
    * @method prepares and handles the installation
    */

    private static function setup()
    {
        // scan available themes

        $scan = scandir(WM_DIR_THEMES);
        $themes = array();

        foreach ($scan as $theme) {
            if (is_dir(WM_DIR_THEMES . $theme) && $theme != '.' && $theme != '..') {
                $themes[] = $theme;
            }
        }

        if (empty($themes)) {
            exit('No theme folders detected.');
        }

        // prepare the HTML

        $html = '<!DOCTYPE html><html><head><title>Webmart - Installation Wizard</title>';
        $html .= self::newFont('Fira Sans', array(
            'weights' => array(
                '300i',
                '400',
                '400i'
            )
        ));
        $html .= self::newCSS(array(
            'body' => array(
                'background' => '#f8f8f8',
                'font-family' => 'Fira Sans, sans-serif',
                'font-weight' => '300'
            ),
            'form-box field' => array(

            )
        ));

        $html .= self::loadBootstrap(true) . '</head><body><div class="container">';
        $html .= '<div class="row mt-3 mb-5"><div class="col-md-3"></div><div class="col-md-6">';
        $html .= '<img src="https://avatars1.githubusercontent.com/u/35627431?s=200&v=4" />';
        $html .= '<h1 class="mt-5 pt-5 text-center">Welcome to Webmart</h1>';
        $html .= '<p class="text-center">Start building with this quick installation.</p>';
        $html .= '<div class="mt-3 p-4 form-box">';

        // prepare and handle the form

        $html .= self::newForm(
            array(
                'name' => 'wmsetup',
                'method' => 'POST',
                'submit' => 'Complete'
            ),
            array(
                'theme' => array(
                    'type' => 'select',
                    'label' => 'Choose a theme:',
                    'placeholder' => '',
                    'options' => $themes
                ),
                'url' => array(
                    'type' => 'text',
                    'label' => "What's your base URL?",
                    'placeholder' => 'ex. mywebsite.com'
                ),
                'folder' => array(
                    'type' => 'text',
                    'heading' => 'Working on a subdirectory?',
                    'label' => 'ex. localhost/',
                    'placeholder' => 'webmart'
                ),
                'sitemap' => array(
                    'type' => 'radio',
                    'heading' => 'SEO',
                    'label' => 'Generate a sitemap.xml file?',
                    'options' => array('No', 'Yes'),
                    'class' => 'form-check-inline'
                ),
                'robots' => array(
                    'type' => 'radio',
                    'label' => 'Generate a robots.txt file?',
                    'options' => array('No', 'Yes'),
                    'class' => 'form-check-inline'
                ),
                'https' => array(
                    'type' => 'radio',
                    'label' => 'Force HTTPs?',
                    'options' => array('No', 'Yes'),
                    'class' => 'form-check-inline'
                ),
                'debug' => array(
                    'type' => 'radio',
                    'heading' => 'Enable debugging?',
                    'options' => array('Disable', 'Enable'),
                    'class' => 'form-check-inline'
                )
            ),
            function($response) {
                // redirect in case of form resubmission

                if (file_exists(WM_DIR . 'wm.php')) {
                    Flight::redirect('/');
                    exit();
                }

                // override form values

                if ($response['folder']['value'] == '') {
                    $response['success'] = true;
                }

                // generate wm.php file

                if ($response['success'] == true) {
                    $output = '<?php' . PHP_EOL . PHP_EOL;

                    foreach ($response as $name => $data) {
                        if ($name == 'success') {
                            continue;
                        }

                        if (!isset($data['value'])) {
                            $data['value'] = '';
                        }

                        switch ($name) {
                            case 'sitemap':
                            case 'robots':
                            case 'https':
                            case 'debug':
                                if ($data['value'] == 'No' || $data['value'] == 'Disable') {
                                    $data['value'] = "false";
                                } else {
                                    $data['value'] = "true";
                                }

                                $output .= 'define("WM_' . strtoupper($name) . '", ' . $data['value'] . ');' . PHP_EOL;

                                break;
                            case 'theme':
                            case 'url':
                            case 'folder':
                                $output .= 'define("WM_' . strtoupper($name) . '", "' . $data['value'] . '");' . PHP_EOL;

                                break;
                        }
                    }

                    file_put_contents(WM_DIR . 'wm.php', $output);

                    Flight::redirect('/');
                    exit();
                }
            }
        );

        echo $html . '</div></div><div class="col-md-3"></div></div></div></body>';
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
    * @method handles the routing request
    */

    private static function route()
    {
        // configure Flight

        Flight::set('flight.views.path', WM_DIR_TEMPLATES);

        // handle global requests

        Flight::route('*', function() {
            $request = Flight::request();

            // collect cookies, GET & POST data

            foreach ((array) $request->data as $data) {
                self::$data = $data;
            }

            // freeze routing for framework installation

            if (self::$wm == false) {
                return false;
            }

            // remove query params

            if (strpos($request->url, '?') != 0) {
                $request->url = substr($request->url, 0, strpos($request->url, '?'));
            }

            // assign page, template and URL

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

            // collect query params and cookies

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

        Flight::start();

        // continue

        self::view();
    }

    /**
    * @method prepares and loads the view
    */

    private static function view()
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

        // complete

        self::$ready = true;
        return;
    }

    /**
    * @method
    */

    public static function __callStatic($name, $params)
    {
        if (method_exists('Webmart\Toolkit', $name)) {
            if (isset($params[4])) {
                return Webmart\Toolkit::$name($params[0], $params[1], $params[2], $params[3], $params[4]);
            } elseif (isset($params[3])) {
                return Webmart\Toolkit::$name($params[0], $params[1], $params[2], $params[3]);
            } elseif (isset($params[2])) {
                return Webmart\Toolkit::$name($params[0], $params[1], $params[2]);
            } elseif (isset($params[1])) {
                return Webmart\Toolkit::$name($params[0], $params[1]);
            } elseif (isset($params[0])) {
                return Webmart\Toolkit::$name($params[0]);
            } else {
                return Webmart\Toolkit::$name();
            }
        }

        return null;
    }

    private function __construct() {}

    private function __destruct() {}

    private function __clone() {}

}
