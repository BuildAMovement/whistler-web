<?php
namespace ufw\helper;

class user extends base {
    
    public function __invoke() {
        return $this;
    }
    
    public function isLoggedOn() {
        return $_SERVER['PHP_AUTH_USER'] && $_SERVER['PHP_AUTH_PW'] && $_COOKIE['logged'];
    }
    
    public function isAdmin() {
        return $this->isLoggedOn() && !strcmp($_SERVER['PHP_AUTH_USER'], 'admin'); 
    }
}