<?php
namespace ufw\helper;

class url extends base
{

    /**
     * URI delimiter
     */
    const URI_DELIMITER = '/';

    public function __invoke($params = array(), $reset = true)
    {
        return $this->url($params, $reset);
    }

    public function url($params = array(), $reset = true)
    {
        $url = array();
        $encode = true;
        $reqValues = $reset ? [] : $this->getRequest()->getValues();
        $reqDefaults = $this->getRequest()->getDefaults();
        
        $controller = isset($params['controller']) && $params['controller'] ? $params['controller'] : ($reset ? null : $reqValues['controller']);
        $action = isset($params['action']) && $params['action'] ? $params['action'] : ($reset ? null : $reqValues['action']);
        
        if (!$controller && array_intersect(\application::getInstance()->getDispatchControllers(), array_keys($params))) {
            // using default controller, and has param with supported controller name
            $controller = \application::getInstance()->getCurrentController()->getCurrentControllerName();
        }
        
        unset($params['controller'], $params['action'], $reqValues['controller'], $reqValues['action']);
        
        if (isset($controller)) {
            if (strcmp($controller, $reqDefaults['controller'])) {
                $url[] = $encode ? urlencode($controller) : $controller;
            }
        }
        if (isset($action)) {
            if (strcmp($action, $reqDefaults['action'])) {
                $url[] = $encode ? urlencode($action) : $action;
            }
        }
        
        if (!$reset) {
            $params = $params + $reqValues;
        }
        foreach ($params as $key => $value) {
            $key = $encode ? urlencode($key) : $key;
            if (is_array($value)) {
                foreach ($value as $arrayValue) {
                    $arrayValue = $encode ? urlencode($arrayValue) : $arrayValue;
                    $url[] = $key;
                    $url[] = $arrayValue;
                }
            } else {
                if ($encode) {
                    $value = urlencode($value);
                }
                $url[] = $key;
                $url[] = $value;
            }
        }
        return '/' . join(self::URI_DELIMITER, $url);
    }

    public function full($params = array(), $reset = true)
    {
        return $this->getScheme() . '://' . $this->getHttpHost() . $this->url($params, $reset);
    }

    /**
     * Retrieve a member of the $_SERVER superglobal
     *
     * If no $key is passed, returns the entire $_SERVER array.
     *
     * @param string $key            
     * @param mixed $default
     *            Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    protected function getServer($key = null, $default = null)
    {
        if (null === $key) {
            return $_SERVER;
        }
        
        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }

    /**
     * Get the request URI scheme
     *
     * @return string
     */
    protected function getScheme()
    {
        return ($this->getServer('HTTPS') == 'on') ? 'https' : 'http';
    }

    /**
     * Get the HTTP host.
     *
     * "Host" ":" host [ ":" port ] ; Section 3.2.2
     * Note the HTTP Host header is not the same as the URI host.
     * It includes the port while the URI host doesn't.
     *
     * @return string
     */
    protected function getHttpHost()
    {
        $host = $this->getServer('HTTP_HOST');
        if (!empty($host)) {
            return $host;
        }
        
        $scheme = $this->getScheme();
        $name = $this->getServer('SERVER_NAME');
        $port = $this->getServer('SERVER_PORT');
        
        if (null === $name) {
            return '';
        } elseif (($scheme == self::SCHEME_HTTP && $port == 80) || ($scheme == self::SCHEME_HTTPS && $port == 443)) {
            return $name;
        } else {
            return $name . ':' . $port;
        }
    }
}