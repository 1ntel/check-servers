<?php 

error_reporting( E_ERROR );

require_once( 'inc/utils.php' );
//$perf = new perf_timer();

$array = load( 'status.php' );
if ( ( strtotime( 'now' ) - $array['page']['timestamp'] ) > 150 ){
	require( 'check.php' );
	$array = load( 'status.php' );
}
$data = $array['data'];
$page = $array['page'];
unset($array);

?>
<!DOCTYPE html>
<html>
<head>
<title><?=$page['title']?></title>
<meta charset="utf-8" />
<meta http-equiv="refresh" content="10">
<style>

body{
	font-family: Geneva, Arial, Helvetica, sans-serif;
	max-width: 990px;
	margin-left: auto;
	margin-right: auto;
	padding-left: 8px;
	padding-right: 8px;
}
table{
	width: 100%;
	border: 0px;
	border-collapse: separate;
	border-spacing: 0px;
}
tr:nth-child(odd) td, tr:nth-child(even) td{
	background: #F8F8F8;
}
tr:nth-child(even) td, tr:nth-child(odd) th{
	background: #EEE;
}
tr{
	height: 32px;
}
td{
	padding-left: 6px;
}

tr:last-of-type td:last-of-type{
	border-bottom-right-radius: 6px;
}
tr:last-of-type td:first-of-type{
	border-bottom-left-radius: 6px;
}
tr:first-of-type th:first-of-type{
	border-top-left-radius: 6px;
}
tr:first-of-type th:last-of-type{
	border-top-right-radius: 6px;
}

.good{
	background: #21D850 !important;
}
.warning{
	background: red !important;
}
.error{
	background: yellow !important;
}
</style>
</head>
<body>
	<h1><?=$page['title']?></h1>
	<p>Обновлено <time datetime="<?=date(DATE_ATOM, $page['timestamp'])?>" pubdate><?=date('d.m.Y, H:i:s', $page['timestamp'])?></time></p>
	<table>
		<thead>
			<tr>
				<th>Адрес</th>
				<th>Описание</th>
				<!--<th>Примечание</th>-->
				<th>Статус</th>
			</tr>
		</thead>
		<tbody>
			<? foreach ($data as $item) { ?>
			<tr>
				<td><?
				echo( $item['link'] 
					? '<a target="_blank" href="'.$item['link'].'">'.$item['name'].'</a>' 
					: $item['name']
				);
				?></td>
				<td><?=$item['desc']?></td>
				<!--<td><?=$item['note']?></td>-->
				<td class="<?=$item['stat']['code']?>"><?=htmlspecialchars($item['stat']['text'])?></td>
			</tr>
			<? } ?>
		</tbody>
	</table>
</body>
<script>
function timeAgo(date1, date2, granularity){
	
	var self = this;
	
	periods = [];
	periods['week'] = 604800;
	periods['day'] = 86400;
	periods['hour'] = 3600;
	periods['minute'] = 60;
	periods['second'] = 1;
	
	if(!granularity){
		granularity = 5;
	}
	
	(typeof(date1) == 'string') ? date1 = new Date(date1).getTime() / 1000 : date1 = new Date().getTime() / 1000;
	(typeof(date2) == 'string') ? date2 = new Date(date2).getTime() / 1000 : date2 = new Date().getTime() / 1000;
	
	if (date1 == date2)	{
		return 'прямо сейчас'
	}
	
	if(date1 > date2){
		difference = date1 - date2;
	}else{
		difference = date2 - date1;
	}

	output = '';
	
	for(var period in periods){
		var value = periods[period];
		
		if(difference >= value){
			time = Math.floor(difference / value);
			difference %= value;
			
			output = output +  time + ' ';
			
			if(time > 1){
				output = output + period + 's ';
			}else{
				output = output + period + ' ';
			}
		}
		
		granularity--;
		if(granularity == 0){
			break;
		}	
	}
	
	return output + 'назад';
}

function init() {
    if (arguments.callee.done) return;
    arguments.callee.done = true;

    // ваш код здесь
	
	timeUpdate();
	
}

// ff, opera
if (document.addEventListener) {
    document.addEventListener("DOMContentLoaded", init, false);
}

// ie
/*@cc_on @*/
/*@if (@_win32)
document.write("<script id=__ie_onload defer src=javascript:void(0)>");
document.write("<\/script>");
var script = document.getElementById("__ie_onload");
script.onreadystatechange = function() {
    if (this.readyState == "complete") {
        init();
    }
};
/*@end @*/

// safari
if (/WebKit/i.test(navigator.userAgent)) {
    var _timer = setInterval(function() {
        if (/loaded|complete/.test(document.readyState)) {
            clearInterval(_timer);
            delete _timer;
            init();
        }
    }, 10);
}

// others
window.onload = init;


var timeElement = document.getElementsByTagName('time')[0];
function timeUpdate() {
	timeElement.innerText = timeAgo(timeElement.getAttribute("datetime"), new Date());
	setTimeout('timeUpdate()', 1000)
}

</script>
</html>