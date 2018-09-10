<?php
namespace ufw\form;

trait elements {

    /**
     *
     * @var \ufw\form\collection
     */
    protected $elements = null;

    /**
     *
     * @return \ufw\form\collection
     */
    protected function elements()
    {
        if (!isset($this->elements)) {
            $this->elements = new collection();
        }
        return $this->elements;
    }

    public function getElement($name)
    {
        return $this->elements()->get($name);
    }

    public function addElement(\ufw\form\element\base $el)
    {
        $this->elements()->set($el->getName(), $el);
        $el->setForm($this);
        return $this;
    }

    public function setElement($name, $value)
    {
        $this->elements()->set($name, $value);
        return $this;
    }

    public function hasElement($name)
    {
        return $this->elements()->has($name);
    }

    public function removeElement($name)
    {
        return $this->elements()->remove($name);
    }

    /**
     *
     * @return \ufw\form\element\base[]
     */
    public function getElements()
    {
        return $this->elements()->getAll();
    }

    /**
     *
     * @param array $values            
     */
    public function setElements($values)
    {
        $this->elements()->setAll($values);
        return $this;
    }
}