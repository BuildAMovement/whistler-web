<?php
namespace controller;

/**
 *
 * @method string partial(string $script, array $vars = array()) render some partial script
 * @method void redirect(string $url) redirect to url
 * @method string render(array $vars = array(), string $script = null, boolean $controllerDir = true) render view script, pass $vars, if script is omitted use script name after action name in controller name directory (3rd param true)
 * @method \ufw\helper\user user() current logged on user helper
 * @method \ufw\helper\url url() url builder
 * @method \ufw\helper\flashMessenger flashMessenger() Flash Messenger - implement session-based messages
 *        
 * @property \helper\user $user       
 *        
 */
abstract class base extends \ufw\controller\base
{
    
}