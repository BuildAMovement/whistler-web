<?php
namespace ufw\form\element;

class email extends input
{

    protected $type = 'email';

    public function init() {
        parent::init();
        $this->addValidator('email');
        return $this;
    }
}
