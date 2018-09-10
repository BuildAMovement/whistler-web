<?php
namespace ufw\form;

trait validators {

    /**
     *
     * @var \ufw\form\collection
     */
    protected $validators = null;

    /**
     *
     * @return \ufw\form\collection
     */
    protected function validators()
    {
        if (!isset($this->validators)) {
            $this->validators = new collection();
        }
        return $this->validators;
    }

    public function getValidator($name)
    {
        $validator = $this->validators()->get($name);
        if ($validator instanceof \ufw\form\validator\base) {
            return $validator;
        }
        if (is_string($validator)) {
            $class = 'form\\validator\\' . $validator;
            if (!class_exists($class)) {
                $class = 'ufw\\form\\validator\\' . $validator;
            }
            $validator = new $class;
            $this->setValidator($name, $validator);
            return $validator;
        }
        if (is_array($validator)) {
            $class = 'form\\validator\\' . (isset($validator['name']) && $validator['name'] ? $validator['name'] : $name);
            if (!class_exists($class)) {
                $class = 'ufw\\form\\validator\\' . (isset($validator['name']) && $validator['name'] ? $validator['name'] : $name);
            }
            $r = new \ReflectionClass($class);
            if ($r->hasMethod('__construct')) {
                $validator = $r->newInstanceArgs((array)$validator['options']);
            } else {
                $validator = $r->newInstance();
            }
            $this->setValidator($name, $validator);
            return $validator;
        }
    }
    
    public function addValidator($name, $value = null) {
        if ($name instanceof \ufw\form\validator\base) {
            $value = $name;
            $name = $value->getName();
        } elseif (is_array($name)) {
            $value = $name;
            $name = $name['name'];
        }
        if (!$value) {
            $value = $name;
        }
        $this->validators()->set($name, $value);
        return $this;
    }

    public function setValidator($name, $value)
    {
        $this->validators()->set($name, $value);
        return $this;
    }

    public function hasValidator($name)
    {
        return $this->validators()->has($name);
    }

    public function removeValidator($name)
    {
        return $this->validators()->remove($name);
    }

    /**
     *
     * @return array
     */
    public function getValidators()
    {
        return $this->validators()->getAll();
    }
    
    public function getValidatorNames() {
        return array_keys($this->getValidators());
    }

    /**
     *
     * @param array $values            
     */
    public function setValidators($values)
    {
        return $this->validators()->setAll($values);
    }
}