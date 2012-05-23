﻿<?php
/* ---------------------------------------------- */
/* Online / Offline checker */
/* Free script */
/* Use and modify it — as you wish */
/* ---------------------------------------------- */

require_once( 'inc/utils.php' );
//$perf = new perf_timer();

function testHostPort ($host, $port) {
	error_reporting( 0 );

	$socket = fsockopen( gethostbyname( $host ), $port, $errno, $errstr, 1 );
	stream_set_timeout( $socket, 2 );
	(bool) $status = $socket;
	fputs( $socket, "HELO" );
	fclose( $socket );
	return $status;
	
	/* $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec'=>1,'usec'=>0));
	$status = socket_connect($socket, $host, $port);
	//usleep(500);
	socket_shutdown($socket, 2);
	socket_close($socket);
	return print_status($status); */
}

/* function testHTTP ($host, $port) {
	return (bool) file_get_contents('http://$host:$port/');
} */

function testUtilMinPromTorg () {
	//$html = 'xxx<span>Осталось сертификатов по первому этапу:  -3. По второму: <font size="2"> 3</font>. По третьему: <font size="2"> 1</font>.</span>xxx';
	$html = file_get_contents('http://util.minprom.gov.ru/login.aspx?ReturnUrl=%2fDiler%2fsvidForm.aspx&/Diler/svidForm.aspx');

	$html = strip_tags($html, '<span>');
	$state = preg_match('/<span>([^-0-9]+)([-0-9]+)([^-0-9]+)([-0-9]+)([^-0-9]+)([-0-9]+)([^-0-9]+)<\/span>/Usi', $html, $need);
	$count = $need[2] + $need[4] + $need[6];
	
	if ($state === 0) {
		return array( "code" => "error", "text" => "Ошибка" );
	}
	if ($count > 0) {
		return array( "code" => "warning", "text" => $count );
	} else {
		return array( "code" => "good", "text" => $count );
	}
}

function testLadaDirect () {
	$html = file_get_contents('http://lada-direct.ru/');
	
	$state = preg_match('/granta.*mainBlock/Usi', $html, $need);
	
	if ($state === 0) {
		return array( "code" => "good", "text" => "Нет" );
	}
	return array( "code" => "warning", "text" => "Есть" );
}

function testInternetConnection () {
	$hosts = array(
		"ya.ru",
		"www.google.com",
		"4.2.2.2",
		"8.8.8.8"
	);
	
	foreach ( $hosts as $host ) {
		$answer = ping( $host );
		if ( $answer['code'] === 'good' ) {
			return $answer;
		}
	}
	return false;
}

function ping ($host) {
	exec( 'ping '.$host.' -n 1', $res);
	$reply = iconv( 'cp866', 'utf-8', $res[3] );
	
	$state = preg_match('/^\D+(\d+\.\d+\.\d+\.\d+)\D+(\d+)[^\d<]+([\d<]+)\D+(\d+)$/Usi', $reply, $answer);
	if ($state) {
		return array( "code" => "good", "text" => "Доступен, {$answer[3]} мс" );
	} else {
		return false;
	}
}

function print_status ($status) {
	if ( is_array( $status ) ){
		return $status;
	}
	if ( (bool) $status ) {
		return array( "code" => "good", "text" => "Доступен" );
	} else {
		return array( "code" => "error", "text" => "Недоступен" );
	}
}

/* function get_ip ($host) {

	if (dns_check_record($host, "CNAME")) {
	
		$dns = dns_get_record($host, DNS_CNAME);
		$host = $dns[0][target];
		return get_ip($host);
		
	} else {
	
		if (dns_check_record($host, "A")) {
		
			$dns = dns_get_record($host, DNS_A);
			$host = $dns[0][ip];
			return $host;
			
		} else {
		
			echo $host ."\n";
			return $host;
			
		}
		
	}
} */

$array['page']['title'] = 'Доступность ресурсов';
$array['page']['timestamp'] = strtotime( 'now' ); 

$data = array(
	array(
		"name"	=> "proxy:3129",
		"type"	=> "host-port",
		"host"	=> "proxy.stok.local",
		"port"	=> 3129,
		"desc"	=> "прокси"
	),
	array(
		"name"	=> "base",
		"type"	=> "host-port",
		"host"	=> "base.stok.local",
		"port"	=> 80,
		//"link"	=> "http://base/",
		"desc"	=> "вебсервер"
	),
	array(
		"name"	=> "terminal",
		"type"	=> "host-port",
		"host"	=> "terminal.stok.local",
		"port"	=> 3389,
		//"link"	=> "rdp://terminal",
		"desc"	=> "Терминальный сервер, 1С"
	),
	array(
		"name"	=> "znago",
		"type"	=> "ping",
		"host"	=> "znago.stok.local",
		//"link"	=> "vnc://znago",
		"desc"	=> "сервер ЗНиАГО"
	),
	array(
		"name"	=> "sto3",
		"type"	=> "ping",
		"host"	=> "sto3.stok.local",
		//"link"	=> "vnc://sto3",
		"desc"	=> "автосфера"
	),
	array(
		"name"	=> "proxy",
		"type"	=> "ping",
		"host"	=> "proxy.stok.local",
		//"link"	=> "vnc://proxy",
		"desc"	=> "шлюз в Интернет"
	),
	/* array(
		"name"	=> "videosrv",
		"type"	=> "host-port-off",
		"host"	=> "videosrv.stok.local",
		"port"	=> 21400,
		//"link"	=> "vnc://videosrv",
		"desc"	=> "видеосервер"
	),
	array(
		"name"	=> "garantia2",
		"type"	=> "host-port-off",
		"host"	=> "garantia2.stok.local",
		"port"	=> 21400,
		//"link"	=> "vnc://garantia2",
		"desc"	=> "видеосервер"
	), */
	array(
		"name"	=> "tportal.vaz.ru",
		"type"	=> "host-port",
		"host"	=> "tportal.vaz.ru",
		"port"	=> 80,
		"link"	=> "http://tportal.vaz.ru/",
		"desc"	=> "технопортал"
	),
	array(
		"name"	=> "autosphere.ru",
		"type"	=> "ping",
		"host"	=> "vpn.autosphere.ru",
		//"link"	=> "http://www.autosphere.ru/",
		"desc"	=> "автосфера"
	),
	/* array(
		"name"	=> "util.minprom.gov.ru",
		"type"	=> "util-minprom",
		"link"	=> "http://util.minprom.gov.ru/",
		"desc"	=> "утилизация"
	), */
	array(
		"name"	=> "lada-direct.ru",
		"type"	=> "lada-direct",
		"link"	=> "http://lada-direct.ru/",
		"desc"	=> "продажи Гранты"
	), 
	array(
		"name"	=> "Интернет",
		"type"	=> "internet"
	),
	array(
		"name"	=> "mail.stok.local:110",
		"type"	=> "host-port",
		"host"	=> "mail.stok.local",
		"port"	=> 110,
		"desc"	=> "почтовый сервер"
	)
);



foreach ( $data as &$item ) {
	switch ($item['type']) {
		// case 'ping-any':
			// break;
		case 'ping':
			$item['stat'] = print_status( ping( $item['host'] ) );
			break;
		case 'util-minprom':
			$item['stat'] = print_status( testUtilMinPromTorg() );
			break;
		case 'lada-direct':
			$item['stat'] = print_status( testLadaDirect() );
			break;
		case 'internet':
			$item['stat'] = print_status( testInternetConnection() );
			break;
		case 'host-port':
			$item['stat'] = print_status( testHostPort( $item['host'], $item['port'] ) );
			break;
		default:
			$item['stat'] = array( "code" => "", "text" => "test" );
	}
}
unset( $item ); // break the reference with the last element



$array['data'] = $data;

chdir( dirname( __FILE__ ) );
save( 'status.php', $array );

//return $array;
//$perf->text();

exit( 0 );