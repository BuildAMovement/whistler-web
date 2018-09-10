<?php
namespace ufw\form;

trait filters {

    /**
     *
     * @var \ufw\form\collection
     */
    protected $filters = null;

    /**
     *
     * @return \ufw\form\collection
     */
    protected function filters()
    {
        if (!isset($this->filters)) {
            $this->filters = new collection();
        }
        return $this->filters;
    }

    public function getFilter($name)
    {
        $filter = $this->filters()->get($name);
        if ($filter instanceof \ufw\form\filter\base) {
            return $filter;
        }
        if (is_string($filter)) {
            $class = 'form\\filter\\' . $filter;
            if (!class_exists($class)) {
                $class = 'ufw\\form\\filter\\' . $filter;
            }
            $filter = new $class;
            $this->setFilter($name, $filter);
            return $filter;
        }
        if (is_array($filter)) {
            $class = 'form\\filter\\' . (isset($filter['name']) && $filter['name'] ? $filter['name'] : $name);
            if (!class_exists($class)) {
                $class = 'ufw\\form\\filter\\' . (isset($filter['name']) && $filter['name'] ? $filter['name'] : $name);
            }
            $r = new \ReflectionClass($class);
            if ($r->hasMethod('__construct')) {
                $filter = $r->newInstanceArgs((array)$filter['options']);
            } else {
                $filter = $r->newInstance();
            }
            $this->setFilter($name, $filter);
            return $filter;
        }
    }
    
    public function addFilter($name, $value = null) {
        if ($name instanceof \ufw\form\filter\base) {
            $value = $name;
            $name = $value->getName();
        } elseif (is_array($name)) {
            $value = $name;
            $name = $name['name'];
        }
        if (!$value) {
            $value = $name;
        }
        $this->filters()->set($name, $value);
        return $this;
    }

    public function setFilter($name, $value)
    {
        $this->filters()->set($name, $value);
        return $this;
    }

    public function hasFilter($name)
    {
        return $this->filters()->has($name);
    }

    public function removeFilter($name)
    {
        return $this->filters()->remove($name);
    }

    /**
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters()->getAll();
    }
    
    public function getFilterNames() {
        return array_keys($this->getFilters());
    }

    /**
     *
     * @param array $values            
     */
    public function setFilters($values)
    {
        return $this->filters()->setAll($values);
    }
}