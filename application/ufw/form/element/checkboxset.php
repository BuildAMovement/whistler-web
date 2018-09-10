<?php
namespace ufw\form\element;

class checkboxset extends multi
{

    protected $type = 'checkbox';
    
    protected $autoArray = true;

    protected function getDefaultOptions()
    {
        return [
//             'class' => 'form-control',
            'type' => $this->getType()
        ];
    }
    
    public function isValid($context = null) {
        $value = $this->getValue();
        $this->clearErrors();
        $result = true;
        if ($this->isRequired() && !count($value)) {
            $result = false;
            $this->addError('Value is required and can\'t be empty');
        }
        if ($value) {
            foreach ($this->getValidatorNames() as $validatorKey) {
                $validator = $this->getValidator($validatorKey);
                if (!$validator->isValid($value, $context)) {
                    $result = false;
                    $this->addErrors($validator->getErrors());
                }
            }
        }
        
        return $result;
    }
    
    public function getAttribs() {
        $attribs = parent::getAttribs();
        if ($this->getAutoArray() && isset($attribs['name']) && $attribs['name']) {
            $attribs['name'] = $attribs['name'] . '[]';
        }
        return $attribs;
    }
    
    public function setValue($value) {
        parent::setValue($value);
        $this->value = array_intersect($this->value, $this->getMultiOptionValues());
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function getAutoArray()
    {
        return $this->autoArray;
    }

    /**
     *
     * @param boolean $autoArray            
     */
    public function setAutoArray($autoArray)
    {
        $this->autoArray = $autoArray;
        return $this;
    }
 
}
