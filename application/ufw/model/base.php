<?php
namespace ufw\model;

/**
 *
 * @property \ufw\helper\user $user
 *
 */
abstract class base
{

    /**
     * Record class name - must be re-declared in each derived class
     * @var string
     */
    protected $recordClassName = '\model\record\base';
    
    public function __construct()
    {}

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

    /**
     *
     * @return string
     */
    public function getRecordClassName()
    {
        return $this->recordClassName;
    }

    public function getUser()
    {
        return \application::getInstance()->getHelper('user');
    }
}