<?php
namespace ufw\form\element;

class input extends base
{

    protected $type = 'text';
    
    public function init() {
        $this->addFilter('trim')->addFilter('singleline');
        return $this;
    }
}
