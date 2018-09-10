<?php
namespace ufw\controller;

/**
 *
 * @method string partial(string $script, array $vars = array()) render some partial script
 * @method void redirect(string $url) redirect to url
 * @method string render(array $vars = array(), string $script = null, boolean $controllerDir = true) render view script, pass $vars, if script is omitted use script name after action name in controller name directory (3rd param true)
 * @method \ufw\helper\user user() current logged on user helper
 * @method \ufw\helper\url url() url builder
 * @method \ufw\helper\flashMessenger flashMessenger() Flash Messenger - implement session-based messages
 *        
 * @property \ufw\helper\user $user       
 *        
 */
abstract class base
{
    
    const PER_PAGE = 24;

    /**
     *
     * @var \application
     */
    protected $application;

    /**
     * Controller action content
     *
     * @var string
     */
    protected $content = '';

    protected $disableLayout = false;

    protected $layout = 'site';

    public function __construct(\application $application)
    {
        $this->setApplication($application);
        $this->init();
    }

    public function init()
    {}

    public function __call($method, $args)
    {
        try {
            $helper = \application::getInstance()->getHelper($method, $this);
        } catch (\Exception $e) {
            
        }
        
        if ($helper) {
            $helper->setController($this);
            return call_user_func_array($helper, $args);
        }
        
        throw new \Exception("Method $method in " . get_class($this) . "not found", -1);
    }

    public function __get($name)
    {
        if (method_exists($this, 'get' . ucfirst($name))) {
            return call_user_func(array(
                $this,
                'get' . ucfirst($name)
            ));
        }
        return null;
    }

    public function dispatch($action)
    {
        $this->preDispatch();
        
        try {
            ob_start();
            $this->$action();
            $this->content = ob_get_clean();
        } catch (\Exception $e) {
            throw $e;
        }
        
        $this->postDispatch();
        
        echo $this->getDisableLayout() ? $this->content : $this->render([
            'content' => $this->content
        ], 'layout/' . $this->getLayout() . '.php', false);
    }

    public function getCurrentActionName()
    {
        return $this->getRequest()->action;
    }

    public function getCurrentControllerName()
    {
        return $this->getRequest()->controller;
    }
    
    public function getScriptName($action = null) {
        if (!$action) $action = $this->getCurrentActionName();
        return $this->getApplication()->getScriptName($action);
    }
    
    public function getHelper($name) {
        return \application::getInstance()->getHelper($name, $this);
    }

    protected function preDispatch()
    {
        return $this;
    }

    protected function postDispatch()
    {
        return $this;
    }

    /**
     *
     * @return \application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \application $_application            
     */
    public function setApplication(\application $application)
    {
        $this->application = $application;
        return $this;
    }

    public function getRequest()
    {
        return $this->getApplication()->getRequest();
    }

    /**
     *
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     *
     * @param string $layout            
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function getDisableLayout()
    {
        return $this->disableLayout;
    }

    /**
     *
     * @param boolean
     */
    public function setDisableLayout($disableLayout)
    {
        $this->disableLayout = $disableLayout;
        return $this;
    }
    
    public function getUser() {
        return $this->user();
    }
}