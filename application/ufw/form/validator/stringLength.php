<?php 
namespace ufw\form\validator;

class stringLength extends base {
    
    const INVALID   = 'stringLengthInvalid';
    const TOO_SHORT = 'stringLengthTooShort';
    const TOO_LONG  = 'stringLengthTooLong';
    
    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID   => "Invalid type given. String expected",
        self::TOO_SHORT => "'%value%' is less than %min% characters long",
        self::TOO_LONG  => "'%value%' is more than %max% characters long",
    );
    
    protected $maxLen = 255;
    
    public function __construct($maxLen = 255) {
        $this->setMaxLen($maxLen);
    }
    
    public function isValid($value, $context = null) {
        $this->setValue($value);
        
        $length = strlen(utf8_decode($value));
        
        if (null !== $this->_max && $this->_max < $length) {
            $this->error(self::TOO_LONG);
        }
        
        if (count($this->messages)) {
            return false;
        } else {
            return true;
        }
        
    }

    /**
     *
     * @return int
     */
    public function getMaxLen()
    {
        return $this->maxLen;
    }

    /**
     *
     * @param int $maxLen            
     */
    public function setMaxLen($maxLen)
    {
        $this->maxLen = $maxLen;
        return $this;
    }
 
}
