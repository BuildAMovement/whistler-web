<?php
namespace ufw\helper;

class render extends base
{

    public function __invoke($vars = array(), $script = null, $controllerDir = true)
    {
        return $this->render($vars, $script, $controllerDir);
    }

    public function render($vars = array(), $script = null, $controllerDir = true)
    {
        ob_start();
        $c = $controllerDir ? $this->getCurrentControllerName() : '';
        if (!$script) {
            $script = $this->getScriptName() . '.php';
        }
        if ($c) {
            $script = "$c/$script";
        }
        unset($vars['script']);
        extract($vars);
        include (APPLICATION_PATH . '/views/' . $script);
        return ob_get_clean();
    }
}