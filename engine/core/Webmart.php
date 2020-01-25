<?php

/*!
* Webmart
* A basic PHP framework for web applications and websites.
* https://webmartphp.com/
*/

class Webmart
{

    private static $ready = false;

    private static $composer = false;

    /**
    * @method prepares the framework
    */

    public static function init($composer = false)
    {
        if (self::$ready) {
            return;
        }

        self::$composer = $composer;

        // define directories and load core files

        define('WM_ROOT', getcwd() . '/');

        if (self::$composer) {
            define('WM_DIR', realpath(__DIR__ . '/../..') . '/');
            define('WM_DIR_THEMES', WM_ROOT . 'themes/');
        } else {
            define('WM_DIR', WM_ROOT);
            define('WM_DIR_THEMES', WM_DIR . 'themes/');
        }

        define('WM_DIR_ENGINE', WM_DIR . 'engine/');
        define('WM_DIR_CORE', WM_DIR_ENGINE . 'core/');
        define('WM_DIR_LIBS', WM_DIR_ENGINE . 'libs/');

        require_once WM_DIR_CORE . 'Toolkit.php';

        // attempt to load Flight

        if (!self::$composer) {
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

        if (!file_exists(WM_ROOT . 'wm.php')) {
            Flight::route('/', function() {
                $data = array();

                foreach ((array) Flight::request()->data as $flight => $collection) {
                    foreach ($collection as $item => $value) {
                        $data[$item] = $value;
                    }
                }

                self::set('data', empty($data) ? null : $data);
                self::setup();
            });

            Flight::start();

            return;
        } else {
            require_once WM_ROOT . 'wm.php';
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

        $html .= self::font('Fira Sans', array(
            'weights' => array(
                '300'
            )
        )) . self::font('Noto Serif', array(
            'weights' => array(
                '400'
            )
        ));
        $html .= self::library('jquery', '3.4.1') . self::bootstrap(true);
        $html .= self::style(array(
            'body' => array(
                'background' => '#f8f8f8',
                'font-family' => 'Fira Sans, sans-serif',
                'font-weight' => '300',
                'cursor' => 'default'
            ),
            'img' => array(
                'width' => '20%',
                'height' => 'auto',
                'margin' => '0 auto',
                'display' => 'block'
            ),
            'h1,h4,label' => array(
                'font-family' => 'Noto serif, serif',
                'letter-spacing' => '-0.05em'
            ),
            'select,input[type="radio"]' => array(
                'cursor' => 'pointer'
            ),
            'label' => array(
                'line-height' => '35px'
            ),
            'h4' => array(
                'margin' => '45px 0 14px 0'
            ),
            'p' => array(
                'margin' => '0'
            ),
            '.form-group' => array(
                'padding' => '4px 0',
                'margin' => '0'
            ),
            '.field-3 label, .field-3 input' => array(
                'display' => 'inline'
            ),
            '.field-3 input' => array(
                'width' => '40%',
                'margin-left' => '5px'
            ),
            '.submit' => array(
                'margin-top' => '45px'
            ),
            '.submit input' => array(
                'width' => '25%'
            ),
            'span.error' => array(
                'padding' => '5px',
                'font-size' => '14px',
                'line-height' => '15px',
                'background' => 'red',
                'color' => 'white',
                'font-weight' => 'bold',
                'border-radius' => '6px',
                'margin' => '4px 0 0 0',
                'display' => 'inline-block'
            )
        ));

        $html .= '</head><body><div class="container">';
        $html .= '<div class="row mt-5 mb-5"><div class="col-md-3"></div><div class="col-md-6">';
        $html .= '<img src="https://avatars1.githubusercontent.com/u/35627431?s=200&v=4" />';
        $html .= '<h1 class="mt-2 pt-2 text-center">Welcome to Webmart</h1>';
        $html .= '<p class="text-center">Start building with this quick installation.</p>';
        $html .= '<div class="mt-5 form-box">';

        // prepare and handle the form

        $html .= self::form(
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
                    'placeholder' => 'ex. http://mywebsite.com/'
                ),
                'folder' => array(
                    'type' => 'text',
                    'heading' => 'Working on a subdirectory?',
                    'label' => 'ex. localhost/',
                    'placeholder' => 'webmart'
                ),
                'robots' => array(
                    'type' => 'radio',
                    'heading' => 'SEO',
                    'label' => 'Generate a robots.txt file?',
                    'options' => array('No', 'Yes'),
                    'class' => 'form-check-inline'
                ),
                'debug' => array(
                    'type' => 'radio',
                    'heading' => 'Developers',
                    'label' => 'Enable debugging?',
                    'options' => array('Disable', 'Enable'),
                    'class' => 'form-check-inline'
                ),
                'autoclass' => array(
                    'type' => 'radio',
                    'label' => 'Auto-include all the theme`s classes?',
                    'options' => array('No', 'Yes'),
                    'class' => 'form-check-inline'
                )
            ),
            function($response) {
                // redirect in case of form resubmission

                if (file_exists(WM_ROOT . 'wm.php')) {
                    self::redirect('/?wm=ready');
                }

                // handle response

                if (!$response['folder']['value']) {
                    $response['folder']['error'] = null;
                }

                foreach ($response as $item => $data) {
                    if ($data['error'] && $item != 'folder') {
                        return $response;
                    } else {
                        $response['success'] = true;
                    }
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
                            case 'autoclass':
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
                                if ($name == 'url' && strpos($data['value'], '/', -1) == false) {
                                    $data['value'] .= '/';
                                }

                                $output .= 'define("WM_' . strtoupper($name) . '", "' . $data['value'] . '");' . PHP_EOL;

                                break;
                        }
                    }

                    file_put_contents(WM_ROOT . 'wm.php', $output);

                    self::redirect('/?wm=ready');
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

        if (!defined('WM_THEME') || WM_THEME == '') {
            exit('Theme not defined. Please check your wm.php configuration.');
        } elseif (!is_dir(WM_DIR_THEMES . WM_THEME)) {
            exit('Theme folder not detected.');
        }

        // define theme directory

        define('WM_DIR_THEME', WM_DIR_THEMES . WM_THEME . '/');

        // attempt to load core theme files

        foreach (array('Config', 'Theme') as $file) {
            if (!file_exists(WM_DIR_THEME . $file . '.php')) {
                exit($file . ' file not detected in selected theme.');
            }

            require_once WM_DIR_THEME . $file . '.php';

            if (!class_exists($file)) {
                exit($file . ' class not detected in selected theme.');
            }
        }

        // define secondary directories

        define('WM_DIR_ASSETS', WM_DIR_THEME . 'assets/');
        define('WM_DIR_CLASSES', WM_DIR_THEME . 'classes/');
        define('WM_DIR_CONTROLLERS', WM_DIR_THEME . 'controllers/');
        define('WM_DIR_TEMPLATES', WM_DIR_THEME . 'templates/');
        define('WM_DIR_JSON', WM_DIR_THEME . 'json/');

        // generate robots.txt file

        if (defined('WM_ROBOTS') && WM_ROBOTS === true && !file_exists(WM_ROOT . 'robots.txt')) {
            $robots = 'User-agent: *' . PHP_EOL;

            if (isset(Config::$noindex) && !empty(Config::$noindex)) {
                foreach (Config::$noindex as $page) {
                    foreach (array('Disallow', 'Noindex') as $cmd) {
                        $robots .= $cmd . ': /' . $page . '/' . PHP_EOL;
                    }
                }
            } else {
                $robots .= 'Disallow: ' . PHP_EOL;
                $robots .= 'Noindex: ' . PHP_EOL;
            }

            if (isset(Config::$redirects) && !empty(Config::$redirects)) {
                foreach (Config::$redirects as $group) {
                    foreach ($group as $source => $to) {
                        foreach (array('Disallow', 'Noindex') as $cmd) {
                            $robots .= $cmd . ': /' . ltrim($source, '/') . '/' . PHP_EOL;
                        }
                    }
                }
            }

            file_put_contents(WM_ROOT . 'robots.txt', $robots);
        }

        // auto-include classes

        if (defined('WM_AUTOCLASS') && WM_AUTOCLASS === true) {
            $classes = scandir(WM_DIR_CLASSES);

            array_shift($classes);
            array_shift($classes);

            foreach ($classes as $class) {
                if (is_file(WM_DIR_CLASSES . $class)) {
                    require_once WM_DIR_CLASSES . $class;
                }
            }
        }
    }

    /**
    * @method handles the routing request
    */

    private static function route()
    {
        // configure Flight

        Flight::set('flight.views.path', WM_DIR_TEMPLATES);
        Flight::set('flight.base_url', rtrim(WM_URL, '/'));

        // handle global requests

        Flight::route('*', function() {
            $request = Flight::request();

            // remove query params

            if (strpos($request->url, '?') != 0) {
                $request->url = substr($request->url, 0, strpos($request->url, '?'));
            }

            // assign page, template and URL

            self::set('url', '');
            self::set('page', 'home');
            self::set('template', 'home');

            if ($request->url != '/') {
                $request->url = rtrim($request->url, '/');

                // perform redirects

                if (isset(Config::$redirects) && !empty(Config::$redirects)) {
                    foreach (Config::$redirects as $http => $paths) {
                        if (isset($paths[$request->url])) {
                            self::redirect($paths[$request->url], $http);
                        }
                    }
                }

                self::set('url', substr($request->url, 1)); // remove first slash

                $routes = explode('/', self::get('url'));

                self::set('template', $routes[0]); // assign first as template
                self::set('page', $routes[count($routes) - 1]); // assign last as page
            }

            // collect GET data and cookies

            foreach (array('query', 'cookies') as $type) {
                $storage = array();
                $clean = '';

                if (
                    isset(Config::${$type}) &&
                    !empty(Config::${$type}) &&
                    !empty($request->{$type})
                ) {
                    foreach ($request->{$type} as $name => $value) {
                        if (!in_array($name, Config::${$type})) {
                            continue;
                        }

                        $clean = filter_var(trim($value), FILTER_SANITIZE_STRIPPED);

                        if ($clean) {
                            $storage[$name] = $clean;
                        } else {
                            $storage[$name] = null;
                        }
                    }
                }

                self::set($type, empty($storage) ? null : $storage);
            }

            // collect POST data

            $data = array();

            foreach ((array) Flight::request()->data as $flight => $collection) {
                foreach ($collection as $item => $value) {
                    $data[$item] = $value;
                }
            }

            self::set('data', empty($data) ? null : $data);

            // collect URLs

            self::set('urls', array(
                'base' => WM_URL,
                'page' => WM_URL . self::get('url'),
                'css' => 'themes/' . WM_THEME . '/assets/css/',
                'imgs' => 'themes/' . WM_THEME . '/assets/imgs/',
                'js' => 'themes/' . WM_THEME . '/assets/js/'
            ));

            // include functions.php

            if (defined('WM_DEBUG') && file_exists(WM_DIR_THEME . 'functions.php')) {
                include_once WM_DIR_THEME . 'functions.php';
            }

            // collect global assets

            if (isset(Config::$fonts) && !empty(Config::$fonts)) {
                foreach (Config::$fonts as $font => $set) {
                    self::font($font, $set);
                }
            }

            if (isset(Config::$libs) && !empty(Config::$libs)) {
                foreach (Config::$libs as $lib => $data) {
                    self::library($lib, $data);
                }
            }

            if (isset(Config::$bootstrap) && Config::$bootstrap != false) {
                self::bootstrap(Config::$bootstrap === 'bundle' ? true : false);
            }

            self::asset('css', 'global');
            self::asset('js', 'global');

            foreach (array('page', 'template') as $asset) {
                foreach (array('css', 'js') as $type) {
                    self::asset($type, self::get($asset));
                }
            }

            return true; // re-route Flight
        });

        // check for individual routes

        if (!isset(Config::$routes) || empty(Config::$routes)) {
            Flight::start();
            self::view();

            exit();
        }

        // handle 404 requests

        Flight::map('notFound', function() {
            self::set('template', '404');

            $controller = new Theme(null);

            if (method_exists('Theme', 'route404')) {
                $controller->route404(null);
            }

            self::view();

            exit();
        });

        // handle individual requests

        Config::$routes[] = ''; // auto-include the homepage

        foreach (Config::$routes as $route) {
            $path = '/' . $route;

            // handle routing paths

            Flight::route($path, function() {
                $controller = null;
                $heir = '';
                $parsed = explode('/', self::get('url'));
                $args = func_get_args();

                if (empty($args)) {
                    $args = null;
                }

                // handle and load controllers

                foreach ($parsed as $item) {
                    $item = str_replace('-', '_', $item);
                    $heir .= ucfirst($item);

                    if (file_exists(WM_DIR_CONTROLLERS . ucfirst($heir) . '.php')) {
                        require WM_DIR_CONTROLLERS . ucfirst($heir) . '.php';

                        if (class_exists($heir)) {
                            $controller = $heir;
                        }
                    }
                }

                // create instance

                if (!$controller) {
                    $class = 'Theme';
                    $method = self::get('template');
                } else {
                    $class = '\\' . $controller;
                    $method = self::get('page');
                }

                $method = 'route' . ucfirst(str_replace('-', '_', $method));
                $instance = new $class($args);

                // execute assigned method

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
    * @method loads the view
    */

    private static function view()
    {
        // pass page/template

        foreach (array('page', 'template') as $item) {
            self::pass($item, self::get($item));
        }

        // pass other values

        if (isset(Config::$version)) {
            self::pass('version', Config::$version);
        }

        self::pass('urls', self::get('urls'));

        // collect assets

        if (defined('WM_DEBUG') && WM_DEBUG == false) {
            if (isset(Config::$hotjar) && Config::$hotjar === true) {
                self::script("(function(h,o,t,j,a,r){
                    h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
                    h._hjSettings={hjid:1,hjsv:5};
                    a=o.getElementsByTagName('head')[0];
                    r=o.createElement('script');r.async=1;
                    r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
                    a.appendChild(r);
                })(window,document,'//static.hotjar.com/c/hotjar-','.js?sv=');");
            }

            if (isset(Config::$gtag) && Config::$gtag != '') {
                self::asset('js', 'https://www.googletagmanager.com/gtag/js?id=' . Config::$gtag, true);

                self::script("window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', '" . Config::$gtag . "');");
            }
        }

        // render assets into the view

        self::pass('assets', Webmart\Toolkit::$assets);

        // create <head> and <body> markup

        $head = '';
        $head .= '<base href="' . self::get('urls')['base'] . '" />';
        $head .= '<link rel="canonical" href="' . self::get('urls')['page'] . '" />';
        $head .= '<meta name="robots" content="';

        if (defined('WM_ROBOTS') && WM_ROBOTS == true) {
            $head .= 'index, follow';
        } else {
            $head .= 'noindex, nofollow';
        }

        $head .= '" />';

        if (isset(Config::$gcode) && Config::$gcode != '') {
            $head .= '<meta name="google-site-verification" content="' . Config::$gcode . '" />';
        }

        self::pass('head', $head);
        self::pass('body', ' view-' . self::get('template') . ' page-' . self::get('page'));

        // load templates

        foreach (array('header', self::get('template'), 'footer') as $template) {
            if (file_exists(WM_DIR_TEMPLATES . $template . '.php')) {
                Flight::render($template);
            }
        }

        // complete

        self::$ready = true;
        return;
    }

    /**
    * @method Toolkit handler
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
