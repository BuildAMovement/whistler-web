<?php
namespace ufw\form\filter;

class singleline extends base
{

    public function filter($value)
    {
        return preg_replace("~[\r\n]~", ' ', $value);
    }

}
