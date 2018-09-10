<?php
namespace ufw\helper;

class redirect extends base
{
    
    public function __invoke($url) {
        header('Location: ' . $url);
        die;
    }
}