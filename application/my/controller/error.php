<?php
namespace controller;

class error extends base
{

    public function PageNotFoundAction()
    {
        echo $this->render([], '404.php');
    }
}