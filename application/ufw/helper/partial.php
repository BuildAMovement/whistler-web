<?php
namespace ufw\helper;

/**
 *
 * @method render(array $vars, string $script, bool $controllerDir)
 *        
 */
class partial extends base
{

    public function __invoke($script, $vars = array())
    {
        return $this->render($vars, "partials/$script", false);
    }
}