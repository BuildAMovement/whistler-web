<?php
namespace ufw\form;

class collection extends \ufw\utils\arrayAccess
{

    public function get($name)
    {
        return isset($this[$name]) ? $this[$name] : null;
    }

    public function has($name)
    {
        return $this->offsetExists($name);
    }

    public function set($name, $value)
    {
        $this[$name] = $value;
        return $this;
    }

    public function remove($name)
    {
        $el = $this[$name];
        unset($this[$name]);
        return $el;
    }
}