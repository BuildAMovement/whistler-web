<?php
namespace ufw\form\element;

class textarea extends base
{

    protected $type = 'textarea';
    
    protected $defaultOptionsToAttribs = [
        'name',
        'id',
        'placeholder',
        'class',
        'type',
        'required',
        'maxlength',
        'rows',
        'cols'
    ];
}
