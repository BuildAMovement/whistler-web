<?php
namespace ufw\form\validator;

/**
 * 
 * Html5 email regex
 *
 */
class email extends regex
{
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID   => "Invalid type given. String, integer or float expected",
        self::NOT_MATCH => "'%value%' is not valid email address",
        self::ERROROUS  => "There was an internal error while using the pattern '%pattern%'",
    );

    /**
     * Sets validator options
     *
     * @param  string $pattern
     */
    public function __construct()
    {
        parent::__construct('~^[a-zA-Z0-9.!#$%&’*+\/=?^_\'{|}\~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,253}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,253}[a-zA-Z0-9])?)*$~');
    }
}
