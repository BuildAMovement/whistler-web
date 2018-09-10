<?php

/**
 * 
 * @method static application getInstance()
 *
 */
class application extends \ufw\application
{
    protected static $dispatchDefaults = array(
        'controller' => 'page',
        'action' => 'index'
    );

    protected $dispatchControllers = array(
        'download',
        'page',
        'reports',
        'xmedias',
        'user'
    );
}
