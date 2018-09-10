<?php
namespace ufw\form;

abstract class base
{
    use \ufw\form\attribs, \ufw\form\options, \ufw\form\elements;

    protected $defaultOptionsToAttribs = [
        'action',
        'class',
        'method',
        'name',
        'id'
    ];

    protected $validated = false;
    
    protected $autoCSRF = true;
    
    /**
     * 
     * @var \EasyCSRF\EasyCSRF
     */
    protected $csrfProvider = null;
    
    public function __construct(array $options = null)
    {
        $this->setOptionsToAttribs($this->defaultOptionsToAttribs);
        $this->setOptions($options);
        $this->preInitGeneric();
        $this->preInit();
        $this->init();
        $this->postInitGeneric();
        $this->postInit();
    }

    protected function preInitGeneric()
    {
        if (null === $this->getAttrib('id')) {
            $id = trim(strtolower(preg_replace(array(
                '~^(ufw\\\\form\\\\|form\\\\)~i',
                '~([A-Z][^A-Z])~',
                '~[\-_]{1,}~'
            ), array(
                '',
                '-$1',
                '-'
            ), get_class($this))), '-');
            $this->setAttrib('id', $id . '-form');
        }
        return $this;
    }

    protected function preInit()
    {
        return $this;
    }

    protected function postInitGeneric()
    {
        if (!$this->getAttrib('action')) {
            // handling action forwarding in controllers, too
            $urlHelper = $this->getHelper('url');
            $this->setAction($urlHelper->url([], false) . ($_SERVER['QUERY_STRING'] ? "?{$_SERVER['QUERY_STRING']}" : ''));
        }
        if (!$this->getAttrib('method')) {
            $this->setAttrib('method', 'post');
        }
        
        if ($this->getAutoCSRF()) {
            $element = new \ufw\form\element\csrf('csrf_token');
            $this->addElement($element);
        }
        return $this;
    }

    protected function postInit()
    {}

    public function __get($name)
    {
        if (method_exists($this, 'get' . ucfirst($name))) {
            return call_user_func(array(
                $this,
                'get' . ucfirst($name)
            ));
        }
        
        if ($this->hasOption($name)) {
            return $this->getOption($name);
        }
        if ($this->hasElement($name)) {
            return $this->getElement($name);
        }
        if ($this->hasAttrib($name)) {
            return $this->getAttrib($name);
        }
        return null;
    }

    public function __set($name, $value)
    {
        if (method_exists($this, 'set' . ucfirst($name))) {
            return call_user_func(array(
                $this,
                'set' . ucfirst($name)
            ), $value);
        }
        if ($value instanceof \ufw\form\element\base) {
            return $this->setElement($name, $value);
        }
    }

    public function __isset($name)
    {
        return $this->hasOption($name) || $this->hasElement($name) || $this->hasAttrib($name);
    }

    public function __toString()
    {
        return $this->getHelper('bootstrap3')->form($this);
    }

    public function getHelper($name)
    {
        return \application::getInstance()->getHelper($name);
    }

    public function populate($values)
    {
        foreach ($this->getElements() as $element) {
            $element->setValue($values[$element->getName()]);
        }
        return $this;
    }

    public function getValues()
    {
        $out = [];
        foreach ($this->getElements() as $element) {
            if (!$element->getIgnore()) {
                $out[$element->getName()] = $element->getValue();
            }
        }
        return $out;
    }

    public function isValid()
    {
        $valid = true;
        $context = $this->getValues();
        foreach ($this->getElements() as $element) {
            $validElement = $element->isValid($context);
            $valid = $valid && $validElement; // avoid optimization
        }
        $this->validated = true;
        return $valid;
    }

    public function getErrors()
    {
        $errors = [];
        foreach ($this->getElements() as $key => $element) {
            $err = $element->getErrors();
            if ($err) {
                $errors[$key] = $err;
            }
        }
        return $errors;
    }

    /**
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getAttrib('action');
    }

    /**
     *
     * @param string $action            
     */
    public function setAction($action)
    {
        $this->setAttrib('action', $action);
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getAttrib('method');
    }

    /**
     *
     * @param string $method            
     */
    public function setMethod($method)
    {
        $this->setAttrib('method', $method);
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     *
     * @param boolean $validated            
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;
        return $this;
    }
    /**
     * @return boolean
     */
    public function getAutoCSRF()
    {
        return $this->autoCSRF;
    }

    /**
     * @param boolean $autoCSRF
     */
    public function setAutoCSRF($autoCSRF)
    {
        $this->autoCSRF = $autoCSRF;
    }
 
    /**
     * @return \EasyCSRF\EasyCSRF
     */
    protected function getCsrfProvider()
    {
        if (!$this->csrfProvider) {
            $this->csrfProvider = new \EasyCSRF\EasyCSRF(new \EasyCSRF\NativeSessionProvider());
        }
        return $this->csrfProvider;
    }


}