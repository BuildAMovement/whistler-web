<?php
namespace ufw\form\element;

class csrf extends hidden
{

    /**
     *
     * @var \EasyCSRF\EasyCSRF
     */
    protected $csrfProvider = null;

    public function init()
    {
        @session_start();
        parent::init();
        $this->addValidator('csrf', array(
            'options' => [
                $this->getAttrib('name')
            ]
        ));
        return $this;
    }

    public function getValue()
    {
        $value = parent::getValue();
        if (!$value) {
            $value = $this->getCsrfProvider()->generate($this->getAttrib('name'));
            $this->setValue($value);
        }
        return $value;
    }

    protected function getCsrfProvider()
    {
        if (!$this->csrfProvider) {
            $this->csrfProvider = new \EasyCSRF\EasyCSRF(new \EasyCSRF\NativeSessionProvider());
        }
        return $this->csrfProvider;
    }
}
