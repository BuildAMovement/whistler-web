<?php
namespace ufw\model\record;

abstract class base extends \ufw\utils\arrayAccess
{

    protected static $tableName = null;

    /**
     *
     * @var \ufw\info_hasharray
     */
    protected static $behavioursHash = null;

    public function __construct($data = array())
    {
        $this->setData($data);
    }

    public function behaviours()
    {
        return array();
    }

    protected function getHelper($name)
    {
        return \application::getInstance()->getHelper($name);
    }

    public static function getTableName()
    {
        return static::$tableName;
    }

    /**
     * * events and behaviour handlers **
     */
    protected function beforeDataSet($data)
    {
        return $data;
    }

    protected function afterDataSet()
    {}

    protected function beforeSave($data)
    {
        return $data;
    }

    protected function afterSave()
    {}

    final protected function onBeforeDataSet($data)
    {
        foreach ($this->getBehaviours('beforeDataSet') as $behaviour) {
            $data = $behaviour->beforeDataSet($data);
        }
        return $this->beforeDataSet($data);
    }

    final protected function onAfterDataSet()
    {
        foreach ($this->getBehaviours('afterDataSet') as $behaviour) {
            $behaviour->afterDataSet();
        }
        $this->afterDataSet();
    }

    final protected function onBeforeSave($data)
    {
        foreach ($this->getBehaviours('afterDataSet') as $behaviour) {
            $data = $behaviour->beforeSave($data);
        }
        return $this->beforeSave($data);
    }

    final protected function onAfterSave()
    {
        foreach ($this->getBehaviours('afterDataSet') as $behaviour) {
            $behaviour->afterSave();
        }
        $this->afterSave();
    }

    /**
     * * getters and setters **
     */
    public function getBehaviours($eventName)
    {
        if (isset($this->getBehavioursHash()->hash[$eventName])) {
            foreach ($this->getBehavioursHash()->hash[$eventName] as $key) {
                yield $key => $this->getBehavioursHash()->info[$key]->obj;
            }
        }
    }

    /**
     *
     * @return \ufw\info_hasharray
     */
    public function getBehavioursHash()
    {
        if (!isset(static::$behavioursHash)) {
            static::$behavioursHash = new \ufw\info_hasharray('ident', 'method');
            foreach ($this->behaviours() as $ident => $behaviour) {
                if (!is_object($behaviour)) {
                    $class = $behaviour['class'];
                    $obj = new $class($this);
                    foreach ($behaviour as $key => $value) {
                        $obj->$key = $value;
                    }
                    $behaviour = $obj;
                }
                
                foreach (array_intersect(\ufw\model\behaviour\base::$behaviourMethods, get_class_methods($behaviour)) as $method) {
                    static::$behavioursHash->add_entry_array(array(
                        'method' => $method,
                        'ident' => $ident,
                        'obj' => $behaviour
                    ));
                }
            }
        }
        return static::$behavioursHash;
    }

    public function getData()
    {
        return $this->getAll();
    }

    public function setData($data)
    {
        $this->setAll($data);
        $this->data = $this->onBeforeDataSet($this->data);
        $this->onAfterDataSet();
        return $this;
    }
}