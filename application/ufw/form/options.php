<?php
namespace ufw\form;

trait options {

    /**
     *
     * @var \ufw\form\collection
     */
    protected $options = null;

    protected $optionsToAttribs = [];

    /**
     *
     * @return \ufw\form\collection
     */
    protected function options()
    {
        if (!isset($this->options)) {
            $this->options = new collection();
        }
        return $this->options;
    }

    public function getOption($name)
    {
        return $this->options()->get($name);
    }

    public function setOption($name, $value)
    {
        $this->options()->set($name, $value);
        return $this;
    }

    public function hasOption($name)
    {
        return $this->options()->has($name);
    }

    public function removeOption($name)
    {
        return $this->options()->remove($name);
    }

    /**
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options()->getAll();
    }

    /**
     *
     * @param array $options            
     */
    public function setOptions($options)
    {
        if (method_exists($this, 'setAttrib')) {
            if (!isset($options['attribs'])) {
                $options['attribs'] = array();
            }
            $options['attribs'] += $this->getAttribs();
            foreach ($options as $key => $value) {
                if ($key == 'attribs') continue;
                if (in_array($key, $this->optionsToAttribs)) {
                    if (isset($value)) {
                        $options['attribs'][$key] = $value;
                        unset($options[$key]);
                    }
                } elseif (method_exists($this, 'set' . ucfirst($key))) {
                    call_user_func(array($this, 'set' . ucfirst($key)), $value);
                    unset($options[$key]);
                }
            }
            $this->setAttribs($options['attribs']);
            unset($options['attribs']);
        }
        $this->options()->setAll($options);
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getOptionsToAttribs()
    {
        return $this->optionsToAttribs;
    }

    /**
     *
     * @param array $optionsToAttribs            
     */
    public function setOptionsToAttribs($optionsToAttribs)
    {
        $this->optionsToAttribs = $optionsToAttribs;
        return $this;
    }
}