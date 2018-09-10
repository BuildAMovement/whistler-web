<?php
namespace ufw\form\filter;

class trim extends base
{

    protected $characterMask = ' ';

    public function __construct($character_mask = " \t\n\r\0\x0B")
    {
        $this->setCharacterMask($character_mask);
    }

    public function filter($value)
    {
        return trim($value, $this->getCharacterMask());
    }

    /**
     *
     * @return string
     */
    public function getCharacterMask()
    {
        return $this->characterMask;
    }

    /**
     *
     * @param string $characterMask            
     */
    public function setCharacterMask($characterMask)
    {
        $this->characterMask = $characterMask;
        return $this;
    }
}
