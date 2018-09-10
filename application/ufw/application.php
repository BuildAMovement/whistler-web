<?php
namespace ufw;

/**
 * 
 * @method static application getInstance()
 *
 */
class application
{
    use singleton;

    const URI_DELIMITER = '/';

    /**
     *
     * @var \controller\base
     */
    protected $currentController = null;

    protected static $dispatchDefaults = array();

    protected $dispatchControllers = array();

    protected $helpersInstantiated = array();

    /**
     *
     * @var request
     */
    protected $request = null;

    /**
     * Word delimiter characters
     *
     * @var array
     */
    protected $wordDelimiter = array(
        '-',
        '.'
    );

    public function __construct()
    {}

    public function run($request = null)
    {
        try {
            if ($_COOKIE['logged']) {
                $this->dispatchLogin();
            }
            
            $this->dispatch($request);
        } catch (\Exception $e) {
            // 404?
            http_response_code(404);
            $this->dispatchError(404);
        }
        return $this;
    }

    public function getHelper($name, $controller = null)
    {
        if (isset($this->helpersInstantiated[$name])) {
            $helper = $this->helpersInstantiated[$name];
        } elseif (class_exists("\\helper\\$name")) {
            $className = "\\helper\\$name";
            $this->helpersInstantiated[$name] = $helper = new $className();
        } elseif (class_exists("\\ufw\\helper\\$name")) {
            $className = "\\ufw\\helper\\$name";
            $this->helpersInstantiated[$name] = $helper = new $className();
        }
        if ($helper && $controller) {
            $helper->setController($controller);
        }
        return $helper;
    }

    protected function dispatch($request = null)
    {
        $this->setRequest($request ? $request : $this->decodeRequest());
        $controllerClass = "\\controller\\" . $this->formatToPhpFriendlyName($this->request['controller'], '');
        
        /**
         *
         * @var controller\base $controller
         */
        $controller = new $controllerClass($this);
        $this->setCurrentController($controller);
        
        $obLevel = ob_get_level();
        ob_start();
        
        try {
            $action = $this->formatToPhpFriendlyName($this->request['action'], 'Action');
            $controller->dispatch($action);
        } catch (\Exception $e) {
            $curObLevel = ob_get_level();
            if ($curObLevel > $obLevel) {
                do {
                    ob_get_clean();
                    $curObLevel = ob_get_level();
                } while ($curObLevel > $obLevel);
            }
            throw $e;
        }
        
        $content = ob_get_clean();
        
        echo $content;
        return $this;
    }

    protected function dispatchError($code)
    {
        return $this->dispatch(new request([
            'controller' => 'error',
            'action' => 'page-not-found'
        ], [], $this->getDispatchDefaults(), $this->request));
    }

    protected function dispatchLogin()
    {
        if (!$_SERVER['HTTPS']) {
            $this->redirect('https://' . ($_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']) . $this->url([], false));
        }
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Hush hush"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Access denied.';
            die();
        } else {
            if (!\ufw\helper\Credentials::checkPassword($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
                header('WWW-Authenticate: Basic realm="Hush hush"');
                header('HTTP/1.0 401 Unauthorized');
                echo 'Access denied.';
                die();
            }
        }
    }

    protected function decodeRequest()
    {
        $values = array();
        $params = array();
        
        $path = trim(preg_replace('~\?.*$~', '', $_SERVER['REQUEST_URI']), static::URI_DELIMITER);
        if ($path != '') {
            $path = explode(static::URI_DELIMITER, $path);
            
            if (count($path) && !empty($path[0]) && in_array($path[0], $this->dispatchControllers)) {
                $values['controller'] = array_shift($path);
            }
            
            if (count($path) && !empty($path[0]) && $this->hasAction($values['controller'], $path[0])) {
                $values['action'] = array_shift($path);
            }
            
            if ($numSegs = count($path)) {
                for ($i = 0; $i < $numSegs; $i = $i + 2) {
                    $key = urldecode($path[$i]);
                    $val = isset($path[$i + 1]) ? urldecode($path[$i + 1]) : null;
                    $params[$key] = (isset($params[$key]) ? (array_merge((array) $params[$key], array(
                        $val
                    ))) : $val);
                }
            }
        }
        
        $request = new request($values, $params, $this->getDispatchDefaults());

        return $request;
    }

    protected function hasAction($controllerName, $actionName)
    {
        if (!$controllerName) {
            $controllerName = $this->getDispatchDefaults()['controller'];
        }
        $controllerClass = "\\controller\\" . $this->formatToPhpFriendlyName($controllerName, '');
        $rc = new \ReflectionClass($controllerClass);
        /**
         *
         * @var \ReflectionMethod $method
         */
        $actions = array_filter(array_map(function ($method) {
            return $method->getName();
        }, $rc->getMethods(\ReflectionMethod::IS_PUBLIC)), function ($method) {
            return preg_match('~Action$~', $method);
        });
        return in_array($this->formatToPhpFriendlyName($actionName, 'Action'), $actions);
    }

    /**
     * Formats a string from a URI into a PHP-friendly name.
     *
     * By default, replaces words separated by the word separator character(s)
     * with camelCaps. If $isAction is false, it also preserves replaces words
     * separated by the path separation character with an underscore, making
     * the following word Title cased. All non-alphanumeric characters are
     * removed.
     *
     * @param string $unformatted            
     * @param boolean $isAction
     *            Defaults to false
     * @return string
     */
    protected function formatToPhpFriendlyName($unformatted, $suffix = '')
    {
        $segments = (array) $unformatted;
        
        foreach ($segments as $key => $segment) {
            $segment = str_replace($this->getWordDelimiter(), ' ', strtolower($segment));
            $segment = preg_replace('/[^a-z0-9 ]/', '', $segment);
            $segments[$key] = str_replace(' ', '', ucwords($segment));
        }
        
        $formatted = join('_', $segments);
        return strtolower(substr($formatted, 0, 1)) . substr($formatted, 1) . $suffix;
    }
    
    /**
     *
     * @return array
     */
    public function getWordDelimiter()
    {
        return $this->wordDelimiter;
    }

    /**
     *
     * @param array $wordDelimiter            
     */
    public function setWordDelimiter(array $wordDelimiter)
    {
        $this->wordDelimiter = $wordDelimiter;
        return $this;
    }

    /**
     *
     * @return request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param request $request            
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    public static function getDispatchDefaults()
    {
        return static::$dispatchDefaults;
    }

    /**
     *
     * @return \controller\base
     */
    public function getCurrentController()
    {
        return $this->currentController;
    }

    public function getScriptName($action) {
        $action = $this->formatToPhpFriendlyName($action, '');
        return strtolower(preg_replace(['~(?<=(?:[A-Z]))([A-Z]+)([A-Z][A-z])~', '~(?<=(?:[a-z0-9]))([A-Z])~'], ['\1-\2', '-\1'], $action));
    }
    
    /**
     *
     * @param \controller\base $currentController            
     */
    public function setCurrentController($currentController)
    {
        $this->currentController = $currentController;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getDispatchControllers()
    {
        return $this->dispatchControllers;
    }
}
