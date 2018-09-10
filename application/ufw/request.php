<?php
namespace ufw;

/**
 *
 * @method string getController()
 * @method string getAction()
 *        
 */
class request extends \ArrayObject
{

    protected $defaults = array();

    protected $values = array();

    /**
     *
     * @var request
     */
    protected $prevRequest = null;

    public function __construct($values, $params, $defaults, request $prevRequest = null)
    {
        $this->setDefaults($defaults)
            ->setValues($values + $params)
            ->setPrevRequest($prevRequest);
        parent::__construct($this->getValues() + $this->getDefaults(), \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);
    }

    public function __call($method, $params)
    {
        if (substr($method, 0, 3) == 'get') {
            $prop = lcfirst(substr($method, 3));
            return $this->$prop;
        }
        
        return null;
    }

    /**
     * Retrieve a member of the $_POST superglobal
     *
     * If no $key is passed, returns the entire $_POST array.
     *
     * @todo How to retrieve from nested arrays
     * @param string $key            
     * @param mixed $default
     *            Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getPost($key = null, $default = null)
    {
        if (null === $key) {
            return $_POST;
        }
        
        return (isset($_POST[$key])) ? $_POST[$key] : $default;
    }

    /**
     * Return the method by which the request was made
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getServer('REQUEST_METHOD');
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
    public function getServer($key = null, $default = null)
    {
        if (null === $key) {
            return $_SERVER;
        }
        
        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }

    public function getParam($key, $default = null)
    {
        if (isset($this->$key)) {
            return $this->$key;
        } elseif (isset($_GET[$key])) {
            return $_GET[$key];
        } elseif (isset($_POST[$key])) {
            return $_POST[$key];
        }
        return $default;
    }

    /**
     * Was the request made by POST?
     *
     * @return boolean
     */
    public function isPost()
    {
        return 'POST' == $this->getMethod();
    }

    /**
     * Was the request made by GET?
     *
     * @return boolean
     */
    public function isGet()
    {
        return 'GET' == $this->getMethod();
    }

    /**
     * Was the request made by PUT?
     *
     * @return boolean
     */
    public function isPut()
    {
        return 'PUT' == $this->getMethod();
    }

    /**
     * Was the request made by DELETE?
     *
     * @return boolean
     */
    public function isDelete()
    {
        return 'DELETE' == $this->getMethod();
    }

    /**
     * Was the request made by HEAD?
     *
     * @return boolean
     */
    public function isHead()
    {
        return 'HEAD' == $this->getMethod();
    }

    /**
     * Was the request made by OPTIONS?
     *
     * @return boolean
     */
    public function isOptions()
    {
        return 'OPTIONS' == $this->getMethod();
    }

    /**
     * Was the request made by PATCH?
     *
     * @return boolean
     */
    public function isPatch()
    {
        return 'PATCH' == $this->getMethod();
    }

    /**
     * Return the value of the given HTTP header.
     * Pass the header name as the
     * plain, HTTP-specified header name. Ex.: Ask for 'Accept' to get the
     * Accept header, 'Accept-Encoding' to get the Accept-Encoding header.
     *
     * @param string $header
     *            HTTP header name
     * @return string|false HTTP header value, or false if not found
     */
    public function getHeader($header)
    {
        if (empty($header)) {
            return null;
        }
        
        // Try to get it from the $_SERVER array first
        $temp = strtoupper(str_replace('-', '_', $header));
        if (isset($_SERVER['HTTP_' . $temp])) {
            return $_SERVER['HTTP_' . $temp];
        }
        
        /*
         * Try to get it from the $_SERVER array on POST request or CGI environment
         * @see https://www.ietf.org/rfc/rfc3875 (4.1.2. and 4.1.3.)
         */
        if (isset($_SERVER[$temp]) && in_array($temp, array(
            'CONTENT_TYPE',
            'CONTENT_LENGTH'
        ))) {
            return $_SERVER[$temp];
        }
        
        // This seems to be the only way to get the Authorization header on
        // Apache
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers[$header])) {
                return $headers[$header];
            }
            $header = strtolower($header);
            foreach ($headers as $key => $value) {
                if (strtolower($key) == $header) {
                    return $value;
                }
            }
        }
        
        return false;
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * @return boolean
     */
    public function isXmlHttpRequest()
    {
        return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
    }

    /**
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     *
     * @param array $values            
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     *
     * @param array $defaults            
     */
    public function setDefaults($defaults)
    {
        $this->defaults = $defaults;
        return $this;
    }

    /**
     *
     * @return request
     */
    public function getPrevRequest()
    {
        return $this->prevRequest;
    }

    /**
     *
     * @param request $prevRequest            
     */
    public function setPrevRequest(request $prevRequest = null)
    {
        $this->prevRequest = $prevRequest;
        return $this;
    }
}