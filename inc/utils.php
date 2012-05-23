<?php

//date_default_timezone_set( 'Europe/Moscow' );
date_default_timezone_set( 'Europe/Moscow' );

//////////////////
class perf_timer {
	var $starttime;	

	////////////////////////
	function __construct() {
		$mtime = microtime();
		$mtime = explode( " ", $mtime );
		$mtime = $mtime[1] + $mtime[0];
		$this->starttime = $mtime;
	}

	///////////////////////
	function __destruct() {
		unset($this->starttime);
	}
	
	///////////////////////
	public function get() {
		$mtime = microtime();
		$mtime = explode( " ", $mtime );
		$endtime = $mtime[1] + $mtime[0];
		return $endtime - $this->starttime;
	}

	////////////////////////
	public function text() {
		return sprintf("Выполнение страницы заняло %01.5f секунд.", $this->get());
	}
}

function save($path, $array) 
{
    $content = '<?php' . PHP_EOL . 'return ' . var_export($array, true) . ';';
    return is_numeric(file_put_contents($path, $content));
}
function load($path) 
{
    return require $path;
}

function isCli() {
     return php_sapi_name()==="cli";
 }
