<?php
namespace ufw\form\element;

class checkbox extends base
{

    protected $type = 'checkbox';

    protected $checkedValue = '1';

    protected $uncheckedValue = '0';
    
    protected $checked = false;

    protected function getDefaultOptions()
    {
        return [
            // 'class' => 'form-control',
            'type' => $this->getType()
        ];
    }

    /**
     *
     * @return string
     */
    public function getCheckedValue()
    {
        return $this->checkedValue;
    }

    /**
     *
     * @param string $checkedValue            
     */
    public function setCheckedValue($checkedValue)
    {
        $this->checkedValue = $checkedValue;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getUncheckedValue()
    {
        return $this->uncheckedValue;
    }

    /**
     *
     * @param string $uncheckedValue            
     */
    public function setUncheckedValue($uncheckedValue)
    {
        $this->uncheckedValue = $uncheckedValue;
        return $this;
    }

    public function setValue($value)
    {
        if ($value == $this->getCheckedValue()) {
            parent::setValue($value);
            $this->setChecked(true);
        } else {
            parent::setValue($this->getUncheckedValue());
            $this->setChecked(false);
        }
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function getChecked()
    {
        return $this->checked;
    }

    /**
     *
     * @param boolean $checked            
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;
        return $this;
    }
 
}
