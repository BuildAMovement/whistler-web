<?php
namespace ufw\form;

trait attribs {

    /**
     *
     * @var \ufw\form\collection
     */
    protected $attribs = null;

    /**
     *
     * @return \ufw\form\collection
     */
    protected function attribs()
    {
        if (!isset($this->attribs)) {
            $this->attribs = new collection();
        }
        return $this->attribs;
    }

    public function getAttrib($name)
    {
        return $this->attribs()->get($name);
    }

    public function setAttrib($name, $value)
    {
        return $this->attribs()->set($name, $value);
    }

    public function hasAttrib($name)
    {
        return $this->attribs()->has($name);
    }

    public function removeAttrib($name)
    {
        return $this->attribs()->remove($name);
    }

    /**
     *
     * @return array
     */
    public function getAttribs()
    {
        return $this->attribs()->getAll();
    }

    /**
     *
     * @param array $values            
     */
    public function setAttribs($values)
    {
        return $this->attribs()->setAll($values);
    }
}