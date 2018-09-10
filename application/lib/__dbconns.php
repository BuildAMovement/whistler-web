<?php

if (APPLICATION_ENV == 'development') {
    // windows developer
    $___dbconnection_list = array(
        'local' => array(
            'ident' => 'local',
            'host' => 'localhost',
            'user' => '',
            'pass' => '',
            'dbname' => 'whistler',
            'newlink' => false,
            'collation' => 'utf8'
        )
    );
} else {
    $___dbconnection_list = array(
        'local' => array(
            'ident' => 'local',
            'host' => '127.0.0.1',
            'user' => '',
            'pass' => '',
            'dbname' => 'whistler',
            'newlink' => false,
            'collation' => 'utf8'
        )
    );
}

$___dbconnection_list['default'] =& $___dbconnection_list['local'];
$___dbconnection_list['master_server'] =& $___dbconnection_list['local'];

return $___dbconnection_list;