<?php
namespace ufw\form\element;

abstract class base
{
    use \ufw\form\attribs, \ufw\form\options, \ufw\form\filters, \ufw\form\validators;

    protected $defaultOptionsToAttribs = [
        'name',
        'id',
        'placeholder',
        'class',
        'type',
        'required',
        'maxlength'
    ];

    /**
     *
     * @var string
     */
    protected $value = null;
    
    protected $valueUnfiltered = null;

    protected $type = 'text';

    protected $errors;
    
    /**
     * Ignore flag (used when retrieving values at form level)
     * @var bool
     */
    protected $ignore = false;
    
    /**
     * 
     * @var \ufw\form\base
     */
    protected $form = null;

    public function __construct($name, array $options = null)
    {
        $this->setOptionsToAttribs($this->defaultOptionsToAttribs);
        $options = array_merge($this->getDefaultOptions(), (array)$options, ['name' => $name]);
        $this->setOptions($options);
        $this->preInitGeneric();
        $this->init();
        $this->postInitGeneric();
    }
    
    protected function getDefaultOptions() {
        return [
            'class' => 'form-control',
            'type' => $this->getType(),
        ];
    }

    protected function preInitGeneric()
    {
        if (null === $this->getAttrib('id')) {
            $id = trim(strtolower(preg_replace(array(
                '~([A-Z][^A-Z])~',
                '~[\-_\[\]]{1,}~',
                '~\-+~'
            ), array(
                '-$1',
                '-',
                '-'
            ), $this->getAttrib('name'))), '-');
            $this->setAttrib('id', $id . '-form-element');
        }
        return $this;
    }

    public function init()
    {
        return $this;
    }

    public function __get($name)
    {
        if (method_exists($this, 'get' . ucfirst($name))) {
            return call_user_func(array(
                $this,
                'get' . ucfirst($name)
            ));
        }
        if ($this->hasOption($name)) {
            return $this->getOption($name);
        }
        if ($this->hasElement($name)) {
            return $this->getElement($name);
        }
        if ($this->hasAttrib($name)) {
            return $this->getAttrib($name);
        }
        return null;
    }

    public function __set($name, $value)
    {
        if (method_exists($this, 'set' . ucfirst($name))) {
            return call_user_func(array(
                $this,
                'set' . ucfirst($name)
            ), $value);
        }
    }

    public function __isset($name)
    {
        return $this->hasOption($name) || $this->hasAttrib($name);
    }

    public function __call($method, $args)
    {
        if (substr($method, 0, 3) == 'get') {
            $prop = lcfirst(substr($method, 3));
            return $this->$prop;
        }
        
        return null;
    }

    public function __toString()
    {
        return $this->getHelper('bootstrap3')->field($this);
    }

    public function addError($message)
    {
        $this->errors[] = $message;
        return $this;
    }

    public function addErrors($messages)
    {
        foreach ($messages as $message) {
            $this->addError($message);
        }
        return $this;
    }

    public function clearErrors()
    {
        $this->errors = [];
        return $this;
    }

    public function isValid($context = null)
    {
        $value = $this->getValue();
        $this->clearErrors();
        $result = true;
        if ($this->isRequired() && !strlen($value)) {
            $result = false;
            $this->addError('Value is required and can\'t be empty');
        }
        foreach ($this->getValidatorNames() as $validatorKey) {
            $validator = $this->getValidator($validatorKey);
            if (!$validator->isValid($value, $context)) {
                $result = false;
                $this->addErrors($validator->getErrors());
            }
        }
        
        return $result;
    }

    public function getHelper($name)
    {
        return \application::getInstance()->getHelper($name);
    }

    public function isRequired()
    {
        return !!$this->getAttrib('required');
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     *
     * @param string $value            
     */
    public function setValue($value)
    {
        $this->valueUnfiltered = $value;
        if ($this->getFilters()) {
            foreach ($this->getFilterNames() as $filterKey) {
                $filter = $this->getFilter($filterKey);
                $value = $filter->filter($value);
            }
        }
        $this->value = $value;
        return $this;
    }

    /**
     *
     * @return \ufw\form\base
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     *
     * @param \ufw\form\base $form
     */
    public function setForm(\ufw\form\base $form = null)
    {
        $this->form = $form;
        return $this;
    }

    /**
     *
     * @return bool
     */
    public function getIgnore()
    {
        return $this->ignore;
    }

    /**
     *
     * @param bool $ignore
     */
    public function setIgnore($ignore)
    {
        $this->ignore = $ignore;
        return $this;
    }
 
 
}
