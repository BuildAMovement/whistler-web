<?php

/**
 * 
 * Iterates object properties (or array or any other primitive type variable), 
 * and apply callback function on all its members / properties.
 * Returns changed object
 * 
 * @param mixed $data
 * @param function $function
 * @return mixed
 */
function object_walk($data, $function)
{
    if (is_array($data) || ($is_o = is_object($data))) {
        if ($is_o)
            $data = (array) $data;
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value))
                $data[$key] = object_walk($value, $function);
            else
                $data[$key] = call_user_func($function, $value);
        }
        if ($is_o)
            $data = (object) $data;
        return $data;
    } else {
        return call_user_func($function, $data);
    }
}

function array_flatten($array = null) {
    $result = array();
    
    if (!is_array($array)) {
        $array = func_get_args();
    }
    
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $result = array_merge($result, array_flatten($value));
        } else {
            $result = array_merge($result, array($key => $value));
        }
    }
    
    return $result;
}

function normalize($str)
{
    include_once 'utf8.inc.php';
    return strtolower(trim(utf8_deaccent(preg_replace(array(
        '~[\.\"\']+~',
        '~[\s\/&\-\?]+~'
    ), array(
        '',
        '-'
    ), $str))));
}

/**
 *
 * Returns existing file from a list of files provided
 *
 * @param array $buf            
 * @param string $postfix            
 * @param string $prefix            
 * @param boolean $return_full_path            
 * @return string
 */
function pick_existing_file($files_list, $postfix = '', $prefix = '', $return_full_path = true)
{
    foreach ($files_list as $value) {
        if (file_exists($prefix . $value . $postfix)) {
            return $return_full_path ? $prefix . $value . $postfix : $value;
            break;
        }
    }
    return;
}

function pprint_r($obj)
{
    echo "<pre>", print_r($obj, true), '</pre>';
}

function sk_query_remove($fields, $base_url = "?", $fix_amps = true)
{
    if ($base_url == "?")
        $base_url = $_SERVER['PHP_SELF'];
    if ($base_url)
        $base_url .= "?";
    
    $pattern = array();
    foreach ((array) $fields as $field) {
        $pattern[] = "/(^|&)$field=[^&]*/i";
    }
    $pattern[] = '/^&/i';
    $pattern[] = '/&$/i';
    
    $out = $base_url . preg_replace($pattern, "", $_SERVER['QUERY_STRING']);
    if ($fix_amps)
        $out = preg_replace('/\&(?!amp;)/', '&amp;', $out);
    return $out;
}

function unistripslashes($data)
{
    if (get_magic_quotes_gpc())
        return object_walk($data, 'stripslashes');
    return $data;
}

function fraction_to_min_sec($coord, $latitude = true)
{
    $isnorth = $coord>=0;
    $coord = abs($coord);
    $deg = floor($coord);
    $coord = ($coord-$deg)*60;
    $min = floor($coord);
    $sec = floor(($coord-$min)*60);
    return sprintf("%d&deg;%d'%d\"%s", $deg, $min, $sec, $isnorth ? ($latitude ? 'N' : 'E') : ($latitude ? 'S' : 'W'));
}

function mydie($msg = '')
{
    echo $msg;
    $bt = debug_backtrace();
    $caller = array_shift($bt);
    echo "<br>" . $caller['line'] . ' @ ' . $caller['file'];
    die();
}
