<?php
namespace ufw\utils;

class arrayAccess implements \ArrayAccess
{

    protected $data = array();

    public function &__get($name)
    {
        return $this->data[$name];
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
    

    public function getAll()
    {
        return $this->data;
    }

    public function setAll($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * * interface implementation **
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->$offset;
    }
}