<?php
namespace ufw;

/**
 *
 * @method static registry getInstance()
 *
 */
class registry extends \ArrayObject
{
    use singleton;

    /**
     * Constructs a parent ArrayObject with default
     * ARRAY_AS_PROPS to allow acces as an object
     *
     * @param array $array
     *            data array
     * @param integer $flags
     *            ArrayObject flags
     */
    public function __construct()
    {
        parent::__construct(array(), \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * getter method, basically same as offsetGet().
     *
     * This method can be called from an object of type Zend_Registry, or it
     * can be called statically. In the latter case, it uses the default
     * static instance stored in the class.
     *
     * @param string $index
     *            - get the value associated with $index
     * @return mixed
     */
    public function get($index)
    {
        return $this->offsetGet($index);
    }

    /**
     * setter method, basically same as offsetSet().
     *
     * This method can be called from an object of type Zend_Registry, or it
     * can be called statically. In the latter case, it uses the default
     * static instance stored in the class.
     *
     * @param string $index
     *            The location in the ArrayObject in which to store
     *            the value.
     * @param mixed $value
     *            The object to store in the ArrayObject.
     * @return void
     */
    public function set($index, $value)
    {
        $this->offsetSet($index, $value);
        return $this;
    }

    /**
     * Returns TRUE if the $index is a named value in the registry,
     * or FALSE if $index was not found in the registry.
     *
     * @param string $index            
     * @return boolean
     */
    public function has($index)
    {
        return $this->offsetExists($index);
    }
}
