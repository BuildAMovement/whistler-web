<?php
namespace ufw\form\element;

class radio extends multi
{

    protected $type = 'radio';

    protected function getDefaultOptions()
    {
        return [
//             'class' => 'form-control',
            'type' => $this->getType()
        ];
    }
}
