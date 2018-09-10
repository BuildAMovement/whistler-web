<?php
namespace ufw\helper;

class base
{

    /**
     *
     * @var \controller\base
     */
    protected $controller = null;

    public function __construct()
    {}

    public function __call($method, $args)
    {
        return call_user_func_array(array(
            $this->getController(),
            $method
        ), $args);
    }
    
    public function __get($name) {
        return $this->getController()->$name;
    }
    
    public function getRequest()
    {
        return $this->getController()->getRequest();
    }

    /**
     *
     * @return \controller\base
     */
    public function getController()
    {
        return $this->controller ? $this->controller : \application::getInstance()->getCurrentController();
    }

    /**
     *
     * @param \controller\base $controller            
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }
}