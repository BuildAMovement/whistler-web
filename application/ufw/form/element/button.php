<?php
namespace ufw\form\element;

class button extends base
{

    protected $type = 'submit';
    
    /**
     * @var boolean
     */
    protected $nameWithinAttribs = true;

    protected $ignore = true;
    
    /**
     *
     * @return boolean
     */
    public function getNameWithinAttribs()
    {
        return $this->nameWithinAttribs;
    }

    /**
     *
     * @param boolean $nameWithinAttribs
     */
    public function setNameWithinAttribs($nameWithinAttribs)
    {
        $this->nameWithinAttribs = $nameWithinAttribs;
        return $this;
    }
 
}
