<?php
namespace ufw\form\validator;

class csrf extends base
{

    const BAD_CSRF = 'badCsrf';

    /**
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::BAD_CSRF => "Invalid CSRF token."
    );

    /**
     *
     * @var string
     */
    protected $tokenKey = 'csrf_token';

    /**
     *
     * @var \EasyCSRF\EasyCSRF
     */
    protected $csrfProvider = null;

    /**
     * Sets validator options
     */
    public function __construct($tokenKey = null)
    {
        $this->setTokenKey($tokenKey);
    }

    /**
     *
     * Returns true if and only if $value is contained in the haystack option.
     *
     * @param mixed $value
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);
        
        try {
            $this->getCsrfProvider()->check($this->getTokenKey(), $value, 3600, false);
            return true;
        } catch (\Exception $e) {}
        
        $this->error(self::BAD_CSRF);
        
        return false;
    }

    /**
     *
     * @return string
     */
    protected function getTokenKey()
    {
        return $this->tokenKey;
    }

    /**
     *
     * @param string $tokenKey
     */
    protected function setTokenKey($tokenKey)
    {
        if ($tokenKey) {
            $this->tokenKey = $tokenKey;
        }
        return $this;
    }

    protected function getCsrfProvider()
    {
        if (!$this->csrfProvider) {
            $this->csrfProvider = new \EasyCSRF\EasyCSRF(new \EasyCSRF\NativeSessionProvider());
        }
        return $this->csrfProvider;
    }
}
