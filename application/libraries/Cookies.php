<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cookies {
	function set($name,$value,$time=FALSE){
	    if (empty($time)){ $time = time() + (86400 * 30) ; }
        setcookie($name,$value,$time);
    }
    function get($name){
	    return $_COOKIE[$name];
    }
}