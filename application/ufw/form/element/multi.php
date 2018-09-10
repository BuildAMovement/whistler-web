<?php
namespace ufw\form\element;

abstract class multi extends base
{

    protected $multiOptions = array();
    
    public function init()
    {
        $this->addValidator('inArray', [
            'options' => [
                'values' => $this->getMultiOptionValues()
            ]
        ]);
        parent::init();
        return $this;
    }
    
    public function getMultiOptionValues() {
        return array_keys($this->getMultiOptions());
    }

    /**
     * Retrieve options array
     *
     * @return array
     */
    public function getMultiOptions()
    {
        if (null === $this->multiOptions || !is_array($this->multiOptions)) {
            $this->multiOptions = array();
        }
        
        return $this->multiOptions;
    }

    /**
     * Set all options at once (overwrites)
     *
     * @param array $options            
     */
    public function setMultiOptions(array $options)
    {
        $this->clearMultiOptions();
        return $this->addMultiOptions($options);
    }

    /**
     * Add many options at once
     *
     * @param array $options            
     */
    public function addMultiOptions(array $options)
    {
        foreach ($options as $option => $value) {
            if (is_array($value) && array_key_exists('key', $value) && array_key_exists('value', $value)) {
                $this->addMultiOption($value['key'], $value['value']);
            } else {
                $this->addMultiOption($option, $value);
            }
        }
        return $this;
    }

    /**
     * Add an option
     *
     * @param string $option            
     * @param string $value            
     */
    public function addMultiOption($option, $value = '')
    {
        $option = (string) $option;
        $this->getMultiOptions();
        $this->multiOptions[$option] = $value;
        return $this;
    }

    /**
     * Clear all options
     */
    public function clearMultiOptions()
    {
        $this->multiOptions = array();
        return $this;
    }
}
