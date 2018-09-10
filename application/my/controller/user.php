<?php
namespace controller;

class user extends base
{
    public function indexAction()
    {
        return $this->loginAction();
    }
    
    public function loginAction() {
        if (!$_SERVER['HTTPS']) {
            $this->redirect('https://' . ($_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']) . $this->url([], false) . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
        }
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Hush hush"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Access denied.';
            die();
        } else {
            if (!\helper\Credentials::checkPassword($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
                header('WWW-Authenticate: Basic realm="Hush hush"');
                header('HTTP/1.0 401 Unauthorized');
                echo 'Access denied.';
                die();
            }
        }
        setcookie('logged', true, null, '/', null, true, true);
        
        $proceedTo = $this->getRequest()->getParam('proceed_to', '/');
        
        $proceedToHost = parse_url($proceedTo, PHP_URL_HOST);
        if ($proceedToHost && !@in_array($proceedToHost, @array_filter([$_SERVER['HTTP_HOST'], $_SERVER['SERVER_NAME']]))) {
            $proceedTo = '/';
        }
        
        $this->redirect($proceedTo);
    }
    
    public function logoutAction() {
        setcookie('logged', false, time() - 3600, '/', null, true, true);
        echo $this->render();
    }
    
    public function logout2Action() {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            setcookie('logged', false, time() - 3600, '/', null, true, true);
        }
        header('WWW-Authenticate: Basic realm="Type anyhing in user field, click OK, click Cancel, reload page and then - you are logged out completely. Or just close all browser windows."');
        header('HTTP/1.0 401 Unauthorized');
        echo $this->render();
    }
}

